<?php

namespace App\Services;

use App\DTOs\TeacherChangePasswordData;
use App\DTOs\TeacherLoginData;
use App\Enums\AccountStatus;
use App\Models\AdminAuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class TeacherAuthService
{
    protected MicrosoftGraphService $graph;

    public function __construct(MicrosoftGraphService $graph)
    {
        $this->graph = $graph;
    }

    public function attemptLogin(Request $request, TeacherLoginData $data): array
    {
        $user = User::where('email', $data->teacherId)->first();

        if (
            ! $user ||
            $user->role !== 'teacher' ||
            ($user->account_status ?? AccountStatus::VERIFIED->value) !== AccountStatus::VERIFIED->value
        ) {
            throw ValidationException::withMessages([
                'teacher_id' => 'Invalid teacher login credentials.',
            ]);
        }

        $isMock = ($data->teacherId === 'teacher@amis.edu.ph' && ($data->password === '123' || $data->password === 'teacher123'));

        $isMsAuthed = false;
        $msData = null;

        if (! $isMock) {
            $msData = $this->authenticateWithMicrosoft($data->teacherId, $data->password);
            if ($msData['authenticated']) {
                $isMsAuthed = true;
            }
        }

        $overridesPath = base_path('../amis_admin/storage/app/academic_teacher_overrides.json');
        $foundTeacher = null;
        $overrides = [];

        try {
            if (file_exists($overridesPath)) {
                $overrides = json_decode(file_get_contents($overridesPath), true) ?: [];
                foreach ($overrides as $teacherData) {
                    if (isset($teacherData['email']) && strtolower($teacherData['email']) === strtolower($user->email)) {
                        $foundTeacher = $teacherData;
                        break;
                    }
                }
            }
        } catch (\Throwable $e) {
            Log::error('Error reading teacher overrides file: '.$e->getMessage());
        }

        $isTempPassword = ($foundTeacher && ($foundTeacher['password_changed'] ?? 'No') === 'No' && $data->password === ($foundTeacher['temporary_password'] ?? ''));

        $isValid = false;
        if ($isMsAuthed || $isMock || $isTempPassword || Hash::check($data->password, $user->password)) {
            $isValid = true;
        }

        if (! $isValid) {
            throw ValidationException::withMessages([
                'teacher_id' => 'Invalid teacher login credentials.',
            ]);
        }

        if ($isMsAuthed) {
            $user->update([
                'password' => Hash::make($data->password),
                'microsoft_id' => $msData['microsoft_id'] ?? $user->microsoft_id,
                'microsoft_email' => $msData['microsoft_email'] ?? $user->microsoft_email ?? $user->email,
                'microsoft_linked_at' => $user->microsoft_linked_at ?? now(),
            ]);

            if ($foundTeacher && ($foundTeacher['password_changed'] ?? 'No') === 'No') {
                try {
                    foreach ($overrides as $tId => $teacherData) {
                        if (isset($teacherData['email']) && strtolower($teacherData['email']) === strtolower($user->email)) {
                            $overrides[$tId]['password_changed'] = 'Yes';
                            $overrides[$tId]['temporary_password'] = null;
                            break;
                        }
                    }
                    file_put_contents($overridesPath, json_encode($overrides, JSON_PRETTY_PRINT));
                } catch (\Throwable $e) {
                    Log::error('Error updating overrides after Microsoft auth: '.$e->getMessage());
                }
            }
        }

        $needsPasswordChange = ($foundTeacher && ($foundTeacher['password_changed'] ?? 'No') === 'No');

        if ($needsPasswordChange && ! $isMsAuthed) {
            return [
                'status' => 'FORCE_CHANGE_PASSWORD',
                'email' => $user->email,
                'temp_password' => $data->password,
            ];
        }

        $this->loginSession($request, $user);

        return [
            'status' => 'SUCCESS',
        ];
    }

    public function authenticateWithMicrosoft(string $email, string $password): array
    {
        $tenantId = config('services.azure.tenant_id');
        $clientId = config('services.azure.client_id');
        $clientSecret = config('services.azure.client_secret');

        if (empty($tenantId) || empty($clientId)) {
            Log::warning('Microsoft Azure credentials are not fully configured in config/services.php. Skipping Microsoft ROPC authentication.');

            return ['authenticated' => false];
        }

        try {
            $response = Http::asForm()->post(
                "https://login.microsoftonline.com/{$tenantId}/oauth2/v2.0/token",
                [
                    'grant_type' => 'password',
                    'client_id' => $clientId,
                    'client_secret' => $clientSecret,
                    'username' => $email,
                    'password' => $password,
                    'scope' => 'openid profile email User.Read',
                ]
            );

            if ($response->successful()) {
                $accessToken = $response->json('access_token');
                $graphUserResponse = Http::withToken($accessToken)->get('https://graph.microsoft.com/v1.0/me');
                $graphUser = $graphUserResponse->successful() ? $graphUserResponse->json() : null;

                return [
                    'authenticated' => true,
                    'microsoft_id' => $graphUser['id'] ?? null,
                    'microsoft_email' => $graphUser['mail'] ?? $graphUser['userPrincipalName'] ?? $email,
                ];
            }

            Log::warning("Microsoft ROPC authentication failed for {$email}: ".$response->body());

            return ['authenticated' => false];
        } catch (\Throwable $exception) {
            Log::error("Microsoft ROPC authentication error for {$email}: ".$exception->getMessage());

            return ['authenticated' => false];
        }
    }

    public function changePassword(Request $request, TeacherChangePasswordData $data): void
    {
        $user = User::where('email', $data->email)->first();
        if (! $user || $user->role !== 'teacher') {
            throw ValidationException::withMessages([
                'teacher_id' => 'Teacher account not found.',
            ]);
        }

        $overridesPath = base_path('../amis_admin/storage/app/academic_teacher_overrides.json');
        $foundTeacherId = null;
        $foundTeacher = null;
        $overrides = [];

        if (file_exists($overridesPath)) {
            $overrides = json_decode(file_get_contents($overridesPath), true) ?: [];
            foreach ($overrides as $tId => $teacherData) {
                if (isset($teacherData['email']) && strtolower($teacherData['email']) === strtolower($user->email)) {
                    $foundTeacherId = $tId;
                    $foundTeacher = $teacherData;
                    break;
                }
            }
        }

        if (! $foundTeacher || ($foundTeacher['password_changed'] ?? 'No') !== 'No' || $data->tempPassword !== ($foundTeacher['temporary_password'] ?? '')) {
            throw ValidationException::withMessages([
                'teacher_id' => 'Invalid temporary password session. Please log in again.',
            ]);
        }

        try {
            $this->graph->resetPassword($user->email, $data->password, false);
        } catch (\Throwable $exception) {
            Log::warning('Microsoft password sync failed for teacher '.$user->email.': '.$exception->getMessage());
        }

        $user->update([
            'password' => Hash::make($data->password),
        ]);

        $overrides[$foundTeacherId]['password_changed'] = 'Yes';
        $overrides[$foundTeacherId]['temporary_password'] = null;
        file_put_contents($overridesPath, json_encode($overrides, JSON_PRETTY_PRINT));

        try {
            AdminAuditLog::record('teacher_password_changed_onboarding', true, "Teacher {$user->email} changed password successfully during initial login");
        } catch (\Throwable $e) {
            Log::error('Failed to log admin audit for teacher password change: '.$e->getMessage());
        }

        $this->loginSession($request, $user);
    }

    public function loginSession(Request $request, User $user): void
    {
        $resolved = $this->resolveTeacherDetails($user->email);
        $name = $resolved['name'] ?? $user->name;
        $dept = $resolved['dept'] ?? 'Islamic School and Arabic Language Department';

        $request->session()->put('teacher_portal_authenticated', true);
        $request->session()->put('teacher_name', $name);
        $request->session()->put('teacher_email', $user->email);
        $request->session()->put('teacher_dept', $dept);
        $request->session()->regenerate();
    }

    public function resolveTeacherDetails(string $email): array
    {
        $email = strtolower(trim($email));

        $overridesPath = base_path('../amis_admin/storage/app/academic_teacher_overrides.json');
        if (file_exists($overridesPath)) {
            try {
                $overrides = json_decode(file_get_contents($overridesPath), true) ?: [];
                foreach ($overrides as $teacherData) {
                    if (isset($teacherData['email']) && strtolower(trim($teacherData['email'])) === $email) {
                        return [
                            'name' => $teacherData['name'] ?? null,
                            'dept' => $teacherData['dept'] ?? null,
                        ];
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Error reading overrides in resolveTeacherDetails: '.$e->getMessage());
            }
        }

        $advisoriesPath = base_path('../amis_admin/config/class_advisories.php');
        if (file_exists($advisoriesPath)) {
            try {
                $advisories = include $advisoriesPath;
                if (is_array($advisories)) {
                    foreach (['elementary' => 'Elementary Department', 'high_school' => 'High School Department'] as $key => $deptName) {
                        if (isset($advisories[$key]) && is_array($advisories[$key])) {
                            foreach ($advisories[$key] as $row) {
                                if (isset($row['teacher'])) {
                                    $computedEmail = $this->computeTeacherEmail($row['teacher']);
                                    if ($computedEmail === $email) {
                                        return [
                                            'name' => $row['teacher'],
                                            'dept' => $deptName,
                                        ];
                                    }
                                }
                            }
                        }
                    }
                }
            } catch (\Throwable $e) {
                Log::error('Error reading class advisories in resolveTeacherDetails: '.$e->getMessage());
            }
        }

        $islamicTeachers = [
            [
                'name' => 'Ust. Raffy Lingasa',
                'email' => 'tr.rlingasa@amis.edu.ph',
                'dept' => 'Islamic School and Arabic Language Department',
            ],
            [
                'name' => 'Ust. Ahmad Al-Jamil',
                'email' => 'tr.ajamil@amis.edu.ph',
                'dept' => 'Islamic School and Arabic Language Department',
            ],
            [
                'name' => 'Ust. Omar Mukhtar',
                'email' => 'tr.omukhtar@amis.edu.ph',
                'dept' => 'Islamic School and Arabic Language Department',
            ],
        ];

        foreach ($islamicTeachers as $t) {
            if (strtolower($t['email']) === $email) {
                return [
                    'name' => $t['name'],
                    'dept' => $t['dept'],
                ];
            }
        }

        if ($email === 'teacher@amis.edu.ph') {
            return [
                'name' => 'Ust. Raffy Lingasa',
                'dept' => 'Islamic School and Arabic Language Department',
            ];
        }

        return [
            'name' => null,
            'dept' => null,
        ];
    }

    protected function computeTeacherEmail(string $name): string
    {
        $cleanName = preg_replace('/^(teacher|ust\.|ustadz\.?|ustadh\.?|sir\.?|ma\'am\.?|maam\.?|ms\.?|mrs\.?|mr\.?)\s+/i', '', trim($name));
        $cleanName = preg_replace('/[^a-zA-Z\s]/', '', $cleanName);
        $cleanName = preg_replace('/\s+/', ' ', trim($cleanName));
        $cleanName = strtolower($cleanName);

        $parts = explode(' ', $cleanName);
        if (count($parts) >= 2) {
            return 'tr.'.substr($parts[0], 0, 1).end($parts).'@amis.edu.ph';
        }

        return 'tr.'.$cleanName.'@amis.edu.ph';
    }

    public function autoConfirmPasswordChanged(string $email): void
    {
        try {
            $overridesPath = base_path('../amis_admin/storage/app/academic_teacher_overrides.json');
            if (file_exists($overridesPath)) {
                $overrides = json_decode(file_get_contents($overridesPath), true) ?: [];
                $updated = false;
                foreach ($overrides as $tId => $teacherData) {
                    if (isset($teacherData['email']) && strtolower($teacherData['email']) === strtolower($email)) {
                        $overrides[$tId]['password_changed'] = 'Yes';
                        $updated = true;
                        break;
                    }
                }
                if ($updated) {
                    file_put_contents($overridesPath, json_encode($overrides, JSON_PRETTY_PRINT));
                }
            }
        } catch (\Throwable $e) {
            Log::error('Failed to auto-update password_changed on teacher login: '.$e->getMessage());
        }
    }

    public function logout(Request $request): void
    {
        $request->session()->forget(['teacher_portal_authenticated', 'teacher_name', 'teacher_email', 'teacher_portal_data']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }
}
