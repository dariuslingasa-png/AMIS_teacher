<?php

namespace App\Http\Controllers;

use App\DTOs\TeacherChangePasswordData;
use App\DTOs\TeacherLoginData;
use App\Enums\AccountStatus;
use App\Http\Requests\TeacherChangePasswordRequest;
use App\Http\Requests\TeacherLoginRequest;
use App\Models\User;
use App\Services\TeacherAuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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

        return redirect()->route('teacher.dashboard')->with('success', 'Password updated successfully. Welcome to the Teacher Portal.');
    }

    public function redirectMicrosoft(Request $request)
    {
        $clientId = config('services.azure.client_id');
        $tenantId = config('services.azure.tenant_id');
        $redirectUri = config('services.azure.redirect_uri_teacher');

        if (! $clientId || ! $tenantId || ! $redirectUri) {
            return back()->withErrors(['teacher_id' => 'Microsoft sign-in is not configured yet.']);
        }

        $state = Str::random(40);
        $request->session()->put('teacher_microsoft_login_state', $state);

        return redirect()->away("https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/authorize?".http_build_query([
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
        $isConnectFlow = $request->session()->has('microsoft_connect_flow');
        $errorRedirectRoute = $isConnectFlow ? 'teacher.settings' : 'teacher.login';
        $errorKey = $isConnectFlow ? 'microsoft' : 'teacher_id';

        if (! $request->has('code')) {
            $error = $request->cookie('microsoft_auth_error') ?? 'Microsoft authentication failed.';
            if ($isConnectFlow) {
                $request->session()->forget('microsoft_connect_flow');

                return redirect()->route($errorRedirectRoute)->withErrors([$errorKey => $error]);
            }

            return redirect()->route($errorRedirectRoute)->withErrors([$errorKey => $error])->withoutCookie('microsoft_auth_error');
        }

        $expectedState = $request->session()->pull('teacher_microsoft_login_state');
        if (! $expectedState || ! hash_equals($expectedState, (string) $request->query('state'))) {
            $request->session()->forget('microsoft_connect_flow');

            return redirect()->route($errorRedirectRoute)->withErrors([$errorKey => 'Microsoft sign in failed because the session state was invalid.']);
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

        if (! $tokenResponse->successful()) {
            Log::error('Microsoft token request failed', [
                'status' => $tokenResponse->status(),
                'body' => $tokenResponse->json() ?: $tokenResponse->body(),
            ]);
            $request->session()->forget('microsoft_connect_flow');

            return redirect()->route($errorRedirectRoute)->withErrors([$errorKey => 'Microsoft sign in failed while requesting an access token.']);
        }

        $graphUserResponse = Http::withToken((string) $tokenResponse->json('access_token'))->get('https://graph.microsoft.com/v1.0/me');
        if (! $graphUserResponse->successful()) {
            $request->session()->forget('microsoft_connect_flow');

            return redirect()->route($errorRedirectRoute)->withErrors([$errorKey => 'Microsoft sign in failed while retrieving user profile.']);
        }

        $graphUser = $graphUserResponse->json();
        $email = $graphUser['mail'] ?? $graphUser['userPrincipalName'] ?? '';

        if (empty($email) || ! str_ends_with(strtolower($email), '@amis.edu.ph')) {
            if ($isConnectFlow) {
                $request->session()->forget('microsoft_connect_flow');

                return redirect()->route('teacher.settings')->withErrors(['microsoft' => 'Access denied. The Microsoft account must use an @amis.edu.ph email address.']);
            }

            return $this->logoutMicrosoftAndRedirect($tenantId, $redirectUri, 'Access denied. This account is not allowed to access the Teacher Portal.');
        }

        if ($isConnectFlow) {
            $loggedInEmail = session('teacher_email');
            if (strtolower($email) !== strtolower($loggedInEmail)) {
                $request->session()->forget('microsoft_connect_flow');

                return redirect()->route('teacher.settings')->withErrors(['microsoft' => 'The Microsoft account ('.$email.') does not match your portal email ('.$loggedInEmail.').']);
            }
        }

        $user = User::where('email', $email)->first();
        if (! $user || $user->role !== 'teacher' || ($user->account_status ?? AccountStatus::VERIFIED->value) !== AccountStatus::VERIFIED->value) {
            if ($isConnectFlow) {
                $request->session()->forget('microsoft_connect_flow');

                return redirect()->route('teacher.settings')->withErrors(['microsoft' => 'No active teacher account found for '.$email]);
            }

            return $this->logoutMicrosoftAndRedirect($tenantId, $redirectUri, 'Access denied. This account is not allowed to access the Teacher Portal.');
        }

        // Link details
        $user->update([
            'microsoft_id' => $graphUser['id'] ?? null,
            'microsoft_email' => $email,
            'microsoft_linked_at' => now(),
        ]);

        if ($isConnectFlow) {
            $request->session()->forget('microsoft_connect_flow');

            return redirect()->route('teacher.settings')->with('success', 'Microsoft 365 account successfully linked!');
        }

        $this->authService->loginSession($request, $user);
        $this->authService->autoConfirmPasswordChanged($user->email);

        return redirect()->route('teacher.dashboard');
    }

    public function connectMicrosoft(Request $request)
    {
        $request->session()->put('microsoft_connect_flow', true);

        return $this->redirectMicrosoft($request);
    }

    public function disconnectMicrosoft(Request $request)
    {
        $user = User::where('email', session('teacher_email'))->first();
        if ($user) {
            $user->update([
                'microsoft_id' => null,
                'microsoft_email' => null,
                'microsoft_linked_at' => null,
            ]);
        }

        return redirect()->route('teacher.settings')->with('success', 'Microsoft 365 account disconnected.');
    }

    private function logoutMicrosoftAndRedirect(string $tenantId, string $redirectUri, string $errorMessage)
    {
        $cookie = cookie('microsoft_auth_error', $errorMessage, 5);

        request()->session()->forget(['teacher_portal_authenticated', 'teacher_name', 'teacher_email', 'teacher_portal_data']);
        request()->session()->invalidate();
        request()->session()->regenerateToken();

        $logoutUrl = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/logout?".http_build_query([
            'post_logout_redirect_uri' => $redirectUri,
        ]);

        return redirect()->away($logoutUrl)->withCookie($cookie);
    }

    public function logout(Request $request)
    {
        $tenantId = config('services.azure.tenant_id');
        $redirectUri = route('teacher.login');

        $this->authService->logout($request);

        if ($tenantId) {
            $logoutUrl = "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/logout?".http_build_query([
                'post_logout_redirect_uri' => $redirectUri,
            ]);

            return redirect()->away($logoutUrl);
        }

        return redirect()->route('teacher.login');
    }
}
