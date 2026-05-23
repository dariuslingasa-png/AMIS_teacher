<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Services\MicrosoftGraphService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AdminStudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with('applicant.user')->latest();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('student_number', 'like', "%{$s}%")
                  ->orWhere('school_email', 'like', "%{$s}%")
                  ->orWhereHas('applicant', fn($a) =>
                      $a->where('first_name', 'like', "%{$s}%")
                        ->orWhere('last_name', 'like', "%{$s}%")
                  );
            });
        }

        if ($request->filled('grade')) {
            $query->where('grade_level', $request->grade);
        }

        if ($request->filled('mode')) {
            $mode = $request->mode;
            $query->whereHas('applicant', fn($q) =>
                $q->where('learning_mode', 'like', "%{$mode}%")
            );
        }

        $students = $query->paginate(20);

        return view('admin.students.index', compact('students'));
    }

    public function show(Student $student)
    {
        $student->load([
            'applicant.user',
            'applicant.payment',
            'studentSection.section.subjects',
            'account.monthlyBillings',
            'account.payments'
        ]);

        $siblings = \App\Models\EnrollmentApplicant::where('user_id', $student->applicant->user_id)
            ->where('id', '!=', $student->enrollment_applicant_id)
            ->whereNotIn('status', ['draft'])
            ->get();

        $statusLabels = \App\Services\Admin\Enrollment\EnrollmentReviewService::STATUS_LABELS;

        return view('admin.students.show', [
            'student'      => $student,
            'siblings'     => $siblings,
            'statusLabels' => $statusLabels,
        ]);
    }

    public function resendCredentials(Student $student)
    {
        $applicant = $student->applicant;

        // Generate new temp password
        $tempPassword = 'Amis@' . strtoupper(Str::random(5)) . rand(10, 99);
        $student->update([
            'temp_password'       => Hash::make($tempPassword),
            'credentials_sent_at' => now(),
        ]);

        // Try to reset Microsoft password
        try {
            $graph = new MicrosoftGraphService();
            $token = (new \ReflectionMethod($graph, 'getAccessToken'))->invoke($graph);
            \Illuminate\Support\Facades\Http::withToken($token)
                ->patch("https://graph.microsoft.com/v1.0/users/{$student->school_email}", [
                    'passwordProfile' => [
                        'password'                      => $tempPassword,
                        'forceChangePasswordNextSignIn' => true,
                    ],
                ]);
        } catch (\Exception $e) {
            Log::error('Failed to reset Microsoft password: ' . $e->getMessage());
        }

        // Resend email
        $parentEmail = $applicant->parent_email ?: $applicant->email;
        if ($parentEmail && $parentEmail !== 'NA') {
            $this->sendCredentialsEmail($applicant, $student, $tempPassword);
        }

        return back()->with('success', 'Credentials resent to ' . ($parentEmail ?? 'parent') . '.');
    }

    private function sendCredentialsEmail($applicant, Student $student, string $tempPassword): void
    {
        $parentEmail = $applicant->parent_email ?: $applicant->email;

        $html = '<!DOCTYPE html><html><body style="font-family:Inter,Arial,sans-serif;background:#f3f4f6;padding:40px 20px;">
        <table width="520" style="background:white;border-radius:16px;overflow:hidden;margin:0 auto;box-shadow:0 4px 12px rgba(0,0,0,0.08);">
        <tr><td style="background:linear-gradient(135deg,#059669,#047857);padding:28px;text-align:center;">
            <img src="' . asset('images/AMIS_Logo.png') . '" width="56" height="56" style="margin-bottom:10px;">
            <h2 style="color:white;margin:0;font-size:18px;">Student Credentials</h2>
            <p style="color:rgba(255,255,255,0.85);font-size:13px;margin:4px 0 0;">Al Munawwara Islamic School — SY ' . $student->school_year . '</p>
        </td></tr>
        <tr><td style="padding:28px 36px;">
            <p style="color:#374151;font-size:14px;margin:0 0 20px;">Here are the updated credentials for <strong>' . $applicant->first_name . ' ' . $applicant->last_name . '</strong>:</p>
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:18px;margin-bottom:20px;">
                <table width="100%">
                    <tr><td style="font-size:13px;color:#6b7280;padding:5px 0;width:140px;">Student Number</td><td style="font-size:15px;font-weight:800;color:#059669;">' . $student->student_number . '</td></tr>
                    <tr><td style="font-size:13px;color:#6b7280;padding:5px 0;">School Email</td><td style="font-size:14px;font-weight:600;color:#111827;">' . $student->school_email . '</td></tr>
                    <tr><td style="font-size:13px;color:#6b7280;padding:5px 0;">Password</td><td style="font-size:14px;font-weight:600;color:#111827;letter-spacing:0.05em;">' . $tempPassword . '</td></tr>
                </table>
            </div>
            <p style="color:#6b7280;font-size:13px;">Login at <a href="https://portal.office.com" style="color:#059669;">portal.office.com</a> and change your password on first login.</p>
        </td></tr>
        </table></body></html>';

        try {
            Mail::html($html, fn($m) => $m->to($parentEmail)->subject('AMIS — Student Credentials'));
        } catch (\Exception $e) {
            Log::error('Failed to resend credentials: ' . $e->getMessage());
        }
    }
}
