<?php

namespace App\Http\Controllers;

use App\Http\Requests\TeacherLoginRequest;
use App\Http\Requests\TeacherChangePasswordRequest;
use App\DTOs\TeacherLoginData;
use App\DTOs\TeacherChangePasswordData;
use App\Services\TeacherAuthService;
use App\Models\User;
use App\Enums\AccountStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TeacherAuthController extends Controller
{
    protected TeacherAuthService $authService;

    public function __construct(TeacherAuthService $authService)
    {
        $this->authService = $authService;
    }

    public function showLogin()
    {
        return view('teacher.login');
    }

    public function login(TeacherLoginRequest $request)
    {
        $loginData = TeacherLoginData::fromArray($request->validated());
        $result = $this->authService->attemptLogin($request, $loginData);

        if ($result['status'] === 'FORCE_CHANGE_PASSWORD') {
            return view('teacher.change-password', [
                'email' => $result['email'],
                'temp_password' => $result['temp_password'],
            ]);
        }

        return redirect()->route('teacher.dashboard');
    }

    public function changePassword(TeacherChangePasswordRequest $request)
    {
        $changePasswordData = TeacherChangePasswordData::fromArray($request->validated());
        $this->authService->changePassword($request, $changePasswordData);

        return redirect()->route('teacher.dashboard')->with('success', 'Password updated successfully for the Teacher Portal and Microsoft account. Welcome to the Teacher Portal.');
    }

    public function redirectMicrosoft(Request $request)
    {
        $clientId = config('services.azure.client_id');
        $tenantId = config('services.azure.tenant_id');
        $redirectUri = config('services.azure.redirect_uri_teacher');

        if (!$clientId || !$tenantId || !$redirectUri) {
            return back()->withErrors(['teacher_id' => 'Microsoft sign-in is not configured yet.']);
        }

        $state = Str::random(40);
        $request->session()->put('teacher_microsoft_login_state', $state);

        return redirect()->away("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/authorize?" . http_build_query([
            'client_id' => $clientId,
            'response_type' => 'code',
            'redirect_uri' => $redirectUri,
            'response_mode' => 'query',
            'scope' => 'openid profile email User.Read',
            'state' => $state,
        ]));
    }

    public function callbackMicrosoft(Request $request)
    {
        if (!$request->has('code')) {
            $error = $request->cookie('microsoft_auth_error') ?? 'Microsoft authentication failed.';
            return redirect()->route('teacher.login')->withErrors(['teacher_id' => $error])->withoutCookie('microsoft_auth_error');
        }

        $expectedState = $request->session()->pull('teacher_microsoft_login_state');
        if (!$expectedState || !hash_equals($expectedState, (string) $request->query('state'))) {
            return redirect()->route('teacher.login')->withErrors(['teacher_id' => 'Microsoft sign in failed because the session state was invalid.']);
        }

        $clientId = config('services.azure.client_id');
        $clientSecret = config('services.azure.client_secret');
        $tenantId = config('services.azure.tenant_id');
        $redirectUri = config('services.azure.redirect_uri_teacher');

        $tokenResponse = Http::asForm()->post("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token", [
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
            'code' => $request->query('code'),
        ]);

        if (!$tokenResponse->successful()) {
            return redirect()->route('teacher.login')->withErrors(['teacher_id' => 'Microsoft sign in failed while requesting an access token.']);
        }

        $graphUserResponse = Http::withToken((string) $tokenResponse->json('access_token'))->get('https://graph.microsoft.com/v1.0/me');
        if (!$graphUserResponse->successful()) {
            return redirect()->route('teacher.login')->withErrors(['teacher_id' => 'Microsoft sign in failed while retrieving user profile.']);
        }

        $graphUser = $graphUserResponse->json();
        $email = $graphUser['mail'] ?? $graphUser['userPrincipalName'] ?? '';

        if (empty($email) || !str_ends_with(strtolower($email), '@amis.edu.ph')) {
            return $this->logoutMicrosoftAndRedirect($tenantId, $redirectUri, 'Access denied. This account is not allowed to access the Teacher Portal.');
        }

        $user = User::where('email', $email)->first();
        if (!$user || $user->role !== 'teacher' || ($user->account_status ?? AccountStatus::VERIFIED->value) !== AccountStatus::VERIFIED->value) {
            return $this->logoutMicrosoftAndRedirect($tenantId, $redirectUri, 'Access denied. This account is not allowed to access the Teacher Portal.');
        }

        $this->authService->loginSession($request, $user);
        $this->authService->autoConfirmPasswordChanged($user->email);

        return redirect()->route('teacher.dashboard');
    }

    private function logoutMicrosoftAndRedirect(string $tenantId, string $redirectUri, string $errorMessage)
    {
        $cookie = cookie('microsoft_auth_error', $errorMessage, 5);

        request()->session()->forget(['teacher_portal_authenticated', 'teacher_name', 'teacher_email', 'teacher_portal_data']);
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $logoutUrl = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/logout?" . http_build_query([
            'post_logout_redirect_uri' => $redirectUri,
        ]);

        return redirect()->away($logoutUrl)->withCookie($cookie);
    }

    public function logout(Request $request)
    {
        $this->authService->logout($request);
        return redirect()->route('teacher.login');
    }
}
