<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->role !== 'admin') {
                Auth::logout();
                return back()->withErrors(['email' => 'Access denied. Admin accounts only.']);
            }

            $request->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }

        return back()->withErrors(['email' => 'Invalid credentials.']);
    }

    public function microsoftRedirect()
    {
        $tenantId    = config('services.microsoft.tenant_id');
        $clientId    = config('services.microsoft.client_id');
        $redirectUri = config('services.microsoft.redirect_uri');
        $state       = bin2hex(random_bytes(16));

        session(['ms_oauth_state' => $state]);

        $params = http_build_query([
            'client_id'     => $clientId,
            'response_type' => 'code',
            'redirect_uri'  => $redirectUri,
            'response_mode' => 'query',
            'scope'         => 'openid profile email offline_access https://graph.microsoft.com/.default',
            'state'         => $state,
        ]);

        return redirect("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/authorize?{$params}");
    }

    public function microsoftCallback(Request $request)
    {
        // Validate state
        if ($request->state !== session('ms_oauth_state')) {
            return redirect()->route('admin.login')->withErrors(['email' => 'Invalid OAuth state. Please try again.']);
        }

        if ($request->has('error')) {
            return redirect()->route('admin.login')->withErrors(['email' => 'Microsoft sign-in failed: ' . $request->error_description]);
        }

        $tenantId    = config('services.microsoft.tenant_id');
        $clientId    = config('services.microsoft.client_id');
        $clientSecret = config('services.microsoft.client_secret');
        $redirectUri = config('services.microsoft.redirect_uri');

        // Exchange code for tokens
        $response = \Illuminate\Support\Facades\Http::asForm()->post(
            "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token",
            [
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'code'          => $request->code,
                'redirect_uri'  => $redirectUri,
                'grant_type'    => 'authorization_code',
            ]
        );

        if (!$response->successful()) {
            return redirect()->route('admin.login')->withErrors(['email' => 'Failed to get Microsoft token.']);
        }

        $accessToken = $response->json('access_token');

        // Get user info from Microsoft
        $userInfo = \Illuminate\Support\Facades\Http::withToken($accessToken)
            ->get('https://graph.microsoft.com/v1.0/me')
            ->json();

        $upn = $userInfo['userPrincipalName'] ?? $userInfo['mail'] ?? null;

        if (!$upn) {
            return redirect()->route('admin.login')->withErrors(['email' => 'Could not retrieve Microsoft account info.']);
        }

        // Find matching admin user by email, or auto-create if @amis.edu.ph domain
        $user = \App\Models\User::where('email', $upn)
            ->orWhere('email', strtolower($upn))
            ->first();

        if (!$user) {
            // Auto-create admin for any @amis.edu.ph account
            if (!str_ends_with(strtolower($upn), '@amis.edu.ph')) {
                return redirect()->route('admin.login')->withErrors(['email' => 'Only @amis.edu.ph accounts are allowed. Access denied.']);
            }

            $username = strtolower(explode('@', $upn)[0]);
            $user = \App\Models\User::create([
                'name'           => $userInfo['displayName'] ?? $username,
                'email'          => strtolower($upn),
                'username'       => $username,
                'password'       => bcrypt(\Illuminate\Support\Str::random(32)),
                'role'           => 'admin',
                'account_status' => 'verified',
            ]);
        } elseif ($user->role !== 'admin') {
            // Existing user but not admin — upgrade to admin if @amis.edu.ph
            if (str_ends_with(strtolower($upn), '@amis.edu.ph')) {
                $user->update(['role' => 'admin', 'account_status' => 'verified']);
            } else {
                return redirect()->route('admin.login')->withErrors(['email' => 'Access denied. Admin accounts only.']);
            }
        }

        Auth::login($user);
        $request->session()->regenerate();
        session(['ms_access_token' => $accessToken]);

        return redirect()->route('admin.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('admin.login');
    }
}
