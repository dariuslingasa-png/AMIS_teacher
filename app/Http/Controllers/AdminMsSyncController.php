<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use App\Services\MicrosoftGraphService;
use App\Services\MsTeamsEnrollmentService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class AdminMsSyncController extends Controller
{
    /**
     * Fetch ALL @amis.edu.ph users from Azure AD.
     * Show which ones exist in portal DB and which are missing (Azure-only).
     */
    public function index()
    {
        $azureUsers = [];
        $azureError = null;

        try {
            $graph      = new MicrosoftGraphService();
            $azureUsers = $graph->listTenantStudents();
        } catch (\Exception $e) {
            $azureError = $e->getMessage();
            Log::error('MS Sync fetch failed: ' . $e->getMessage());
        }

        // Index DB students by school_email and ms_user_id
        $dbStudents   = Student::with('applicant', 'studentSection')->get();
        $dbByEmail    = $dbStudents->keyBy(fn($s) => strtolower($s->school_email ?? ''));
        $dbByMsUserId = $dbStudents->keyBy('ms_user_id');

        $rows = [];
        $testAccounts = [];
        $currentYear = date('y'); // Current year (26 for 2026)

        foreach ($azureUsers as $azUser) {
            $upn      = strtolower($azUser['userPrincipalName'] ?? '');
            $azId     = $azUser['id'] ?? null;
            $prefix   = explode('@', $upn)[0];
            $dbStudent = $dbByEmail->get($upn) ?? $dbByMsUserId->get($azId);

            // Check if this is a test account
            $isTestAccount = str_starts_with($prefix, $currentYear) && str_contains($upn, 'apelyido');
            
            if ($isTestAccount) {
                $testAccounts[] = [
                    'upn' => $upn,
                    'display_name' => $azUser['displayName'] ?? '—',
                    'azure_id' => $azId,
                ];
            }

            $rows[] = [
                // Azure data
                'upn'            => $upn,
                'display_name'   => $azUser['displayName'] ?? '—',
                'azure_id'       => $azId,
                'azure_type'     => $azUser['userType'] ?? 'Unknown',
                'azure_enabled'  => $azUser['accountEnabled'] ?? false,
                'is_test'        => $isTestAccount,
                // Portal data
                'in_portal'      => !is_null($dbStudent),
                'student'        => $dbStudent,
                'teams_status'   => $dbStudent?->studentSection?->ms_status ?? 'not_enrolled',
            ];
        }

        // Sort: test accounts first, then missing in portal, then by UPN
        usort($rows, function($a, $b) {
            if ($a['is_test'] !== $b['is_test']) return $b['is_test'] <=> $a['is_test'];
            if ($a['in_portal'] !== $b['in_portal']) return $a['in_portal'] <=> $b['in_portal'];
            return strcmp($a['upn'], $b['upn']);
        });

        $stats = [
            'azure_total'    => count($rows),
            'in_portal'      => collect($rows)->where('in_portal', true)->count(),
            'missing_portal' => collect($rows)->where('in_portal', false)->count(),
            'guest_users'    => collect($rows)->where('azure_type', 'Guest')->count(),
            'teams_enrolled' => collect($rows)->where('teams_status', 'enrolled')->count(),
            'teams_failed'   => collect($rows)->where('teams_status', 'failed')->count(),
            'test_accounts'  => count($testAccounts),
        ];

        return view('admin.ms-sync.index', compact('rows', 'stats', 'azureError', 'testAccounts'));
    }

    /**
     * Delete an Azure AD user (remove test/duplicate accounts).
     */
    public function deleteFromAzure(\Illuminate\Http\Request $request)
    {
        $request->validate(['azure_id' => 'required|string']);

        try {
            $graph = new MicrosoftGraphService();
            $graph->deleteAzureUser($request->azure_id);
            return back()->with('success', "Azure account deleted successfully.");
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Failed to delete: ' . $e->getMessage()]);
        }
    }

    /**
     * Import a single Azure user into the portal students table.
     * Extracts student number from UPN (e.g. 260001santos → 260001).
     */
    public function importFromAzure(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'azure_id'    => 'required|string',
            'upn'         => 'required|email',
            'display_name'=> 'required|string',
        ]);

        $upn         = strtolower($request->upn);
        $azureId     = $request->azure_id;
        $displayName = $request->display_name;

        // Extract student number from UPN prefix (e.g. "260001santos" → "260001")
        $prefix = explode('@', $upn)[0]; // "260001santos"
        preg_match('/^(\d+)/', $prefix, $m);
        $studentNumber = $m[1] ?? null;

        if (!$studentNumber) {
            return back()->withErrors(['error' => "Cannot extract student number from UPN: {$upn}"]);
        }

        // Check if already exists
        if (Student::where('school_email', $upn)->orWhere('ms_user_id', $azureId)->exists()) {
            return back()->withErrors(['error' => "Student {$upn} already exists in portal."]);
        }

        // Find or create a portal user account
        $user = User::where('email', $upn)->first();
        if (!$user) {
            $nameParts = explode(' ', $displayName);
            $user = User::create([
                'name'              => $displayName,
                'email'             => $upn,
                'username'          => $prefix,
                'password'          => Hash::make(\Illuminate\Support\Str::random(32)),
                'role'              => 'student',
                'account_status'    => 'verified',
                'email_verified_at' => now(),
            ]);
        } else {
            $user->update(['role' => 'student', 'account_status' => 'verified']);
        }

        // Create student record
        Student::create([
            'user_id'               => $user->id,
            'enrollment_applicant_id' => null, // no enrollment form — imported from Azure
            'student_number'        => $studentNumber,
            'school_email'          => $upn,
            'ms_email'              => $upn,
            'ms_user_id'            => $azureId,
            'ms_account_created_at' => now(),
            'grade_level'           => 'Unknown', // admin can update later
            'school_year'           => '2026-2027',
            'credentials_sent_at'   => now(),
        ]);

        return back()->with('success', "Imported {$displayName} ({$upn}) into portal.");
    }

    /**
     * Import ALL Azure users not yet in portal.
     */
    public function importAll()
    {
        $graph      = new MicrosoftGraphService();
        $azureUsers = $graph->listTenantStudents();

        $dbByEmail    = Student::pluck('school_email')->map('strtolower')->flip();
        $dbByMsUserId = Student::pluck('ms_user_id')->flip();

        $imported = 0; $skipped = 0; $failed = 0;

        foreach ($azureUsers as $azUser) {
            $upn    = strtolower($azUser['userPrincipalName'] ?? '');
            $azId   = $azUser['id'] ?? null;

            if ($dbByEmail->has($upn) || $dbByMsUserId->has($azId)) {
                $skipped++;
                continue;
            }

            $prefix = explode('@', $upn)[0];
            preg_match('/^(\d+)/', $prefix, $m);
            $studentNumber = $m[1] ?? null;

            if (!$studentNumber) { $failed++; continue; }

            try {
                $user = User::firstOrCreate(
                    ['email' => $upn],
                    [
                        'name'              => $azUser['displayName'] ?? $prefix,
                        'username'          => $prefix,
                        'password'          => Hash::make(\Illuminate\Support\Str::random(32)),
                        'role'              => 'student',
                        'account_status'    => 'verified',
                        'email_verified_at' => now(),
                    ]
                );
                $user->update(['role' => 'student']);

                Student::create([
                    'user_id'                 => $user->id,
                    'enrollment_applicant_id' => null,
                    'student_number'          => $studentNumber,
                    'school_email'            => $upn,
                    'ms_email'                => $upn,
                    'ms_user_id'              => $azId,
                    'ms_account_created_at'   => now(),
                    'grade_level'             => 'Unknown',
                    'school_year'             => '2026-2027',
                    'credentials_sent_at'     => now(),
                ]);

                $imported++;
            } catch (\Exception $e) {
                Log::error("importAll failed for {$upn}: " . $e->getMessage());
                $failed++;
            }
        }

        return back()->with('success', "Import complete: {$imported} imported, {$skipped} already existed, {$failed} failed.");
    }

    /**
     * Convert all Guest students to Member in Azure AD.
     */
    public function fixGuests()
    {
        $graph   = new MicrosoftGraphService();
        $azUsers = $graph->listTenantStudents();
        $fixed = 0; $failed = 0;

        foreach ($azUsers as $u) {
            if (($u['userType'] ?? '') !== 'Guest') continue;
            try {
                $graph->convertGuestToMember($u['id']);
                $fixed++;
                sleep(1);
            } catch (\Exception $e) {
                Log::warning("fixGuests failed for {$u['userPrincipalName']}: " . $e->getMessage());
                $failed++;
            }
        }

        return back()->with('success', "Converted {$fixed} Guest → Member. {$failed} failed.");
    }

    /**
     * Retry Teams enrollment for all failed students.
     */
    public function retryFailed()
    {
        $failed  = \App\Models\StudentSection::where('ms_status', 'failed')->with('student')->get();
        $graph   = new MicrosoftGraphService();
        $service = new MsTeamsEnrollmentService($graph);
        $ok = 0; $err = 0;

        foreach ($failed as $ss) {
            try {
                $result = $service->enrollStudent($ss->student);
                if ($result['enrolled'] > 0) $ok++;
                else $err++;
            } catch (\Exception $e) {
                Log::error("Retry failed for {$ss->student->student_number}: " . $e->getMessage());
                $err++;
            }
        }

        return back()->with('success', "Retry complete: {$ok} enrolled, {$err} still failed.");
    }

    /**
     * Identify and delete test accounts with pattern: 26+random+@apelyido
     */
    public function cleanupTestAccounts()
    {
        try {
            $graph = new MicrosoftGraphService();
            $azureUsers = $graph->listTenantStudents();
            
            $testAccounts = [];
            $currentYear = date('y'); // Get current year (26 for 2026)
            
            foreach ($azureUsers as $user) {
                $upn = strtolower($user['userPrincipalName'] ?? '');
                $prefix = explode('@', $upn)[0];
                
                // Check if it matches test pattern: starts with current year + has @apelyido
                if (str_starts_with($prefix, $currentYear) && str_contains($upn, 'apelyido')) {
                    $testAccounts[] = [
                        'id' => $user['id'],
                        'upn' => $upn,
                        'display_name' => $user['displayName'] ?? '—'
                    ];
                }
            }
            
            $deleted = 0;
            $failed = 0;
            
            foreach ($testAccounts as $account) {
                try {
                    $graph->deleteAzureUser($account['id']);
                    $deleted++;
                    Log::info("Deleted test account: {$account['upn']}");
                } catch (\Exception $e) {
                    $failed++;
                    Log::error("Failed to delete test account {$account['upn']}: " . $e->getMessage());
                }
                
                // Small delay to avoid rate limiting
                usleep(500000); // 0.5 seconds
            }
            
            return back()->with('success', "Cleanup complete: {$deleted} test accounts deleted, {$failed} failed.");
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Cleanup failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove test student data from portal database only (keep Azure accounts)
     */
    public function cleanupPortalTestData()
    {
        try {
            $currentYear = date('y'); // Current year (26 for 2026)
            
            // Find test students in portal database
            $testStudents = Student::with('user', 'studentSection')
                ->where(function($query) use ($currentYear) {
                    $query->where('school_email', 'like', $currentYear . '%@%apelyido%')
                          ->orWhere('ms_email', 'like', $currentYear . '%@%apelyido%');
                })
                ->get();
            
            $deletedStudents = 0;
            $deletedUsers = 0;
            $deletedSections = 0;
            $failed = 0;
            
            foreach ($testStudents as $student) {
                try {
                    // Delete student sections first (foreign key constraint)
                    if ($student->studentSection) {
                        $student->studentSection->delete();
                        $deletedSections++;
                    }
                    
                    // Store user reference before deleting student
                    $user = $student->user;
                    
                    // Delete student record
                    $student->delete();
                    $deletedStudents++;
                    
                    // Delete associated user account if it exists and is a test account
                    if ($user && str_contains($user->email, 'apelyido')) {
                        $user->delete();
                        $deletedUsers++;
                    }
                    
                    Log::info("Removed test student from portal: {$student->school_email}");
                    
                } catch (\Exception $e) {
                    $failed++;
                    Log::error("Failed to remove test student {$student->school_email}: " . $e->getMessage());
                }
            }
            
            return back()->with('success', "Portal cleanup complete: {$deletedStudents} students, {$deletedUsers} users, {$deletedSections} sections removed. {$failed} failed.");
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Portal cleanup failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Sync a single student to Teams.
     */
    public function syncStudent(Student $student)
    {
        if (!$student->ms_user_id) {
            return back()->withErrors(['error' => 'Student has no Microsoft account.']);
        }

        $graph   = new MicrosoftGraphService();
        $service = new MsTeamsEnrollmentService($graph);

        try {
            $result = $service->enrollStudent($student);
            $msg = "Synced {$student->student_number}: {$result['enrolled']} enrolled.";
            if ($result['failed'] > 0) $msg .= " {$result['failed']} failed.";
            return back()->with('success', $msg);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}
