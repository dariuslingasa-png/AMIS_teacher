<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAuditLog;
use App\Models\EnrollmentApplicant;
use App\Services\Admin\Enrollment\EnrollmentApprovalService;
use App\Services\Admin\Enrollment\EnrollmentReviewService;
use Illuminate\Http\Request;

class ApprovalController extends Controller
{
    public function __construct(
        private readonly EnrollmentApprovalService $approvalService,
        private readonly EnrollmentReviewService $reviewService,
    ) {}

    public function updateStatus(Request $request, EnrollmentApplicant $applicant)
    {
        $this->ensureApplicationReviewer();

        if ($request->input('status') === 'approved') {
            $message = $this->approvalService->approve($applicant);
            AdminAuditLog::record('application_approved', true, 'Enrollment application approved.', [
                'applicant_id' => $applicant->id,
            ]);

            return back()->with('success', $message);
        }

        $this->reviewService->updateStatus($request, $applicant);
        AdminAuditLog::record('application_status_updated', true, 'Application review status updated.', [
            'applicant_id' => $applicant->id,
            'status' => $request->input('status'),
        ]);

        return back()->with('success', 'Application status updated.');
    }

    public function approve(EnrollmentApplicant $applicant)
    {
        $this->ensureApplicationReviewer();

        $message = $this->approvalService->approve($applicant);
        AdminAuditLog::record('application_approved', true, 'Enrollment application approved.', [
            'applicant_id' => $applicant->id,
        ]);

        return back()->with('success', $message);
    }

    public function approveFamily(Request $request, EnrollmentApplicant $applicant)
    {
        $this->ensureApplicationReviewer();

        $familyEnrollees = EnrollmentApplicant::where(function ($query) use ($applicant) {
            if ($applicant->family_application_id) {
                $query->where('family_application_id', $applicant->family_application_id);
            } else {
                $query->where('user_id', $applicant->user_id);
            }
        })
        ->get();

        $approvedCount = 0;
        $messages = [];

        foreach ($familyEnrollees as $child) {
            if ($child->status !== 'approved') {
                try {
                    $msg = $this->approvalService->approve($child);
                    AdminAuditLog::record('application_approved', true, 'Enrollment application approved (family batch).', [
                        'applicant_id' => $child->id,
                    ]);
                    $messages[] = "{$child->full_name}: {$msg}";
                    $approvedCount++;
                } catch (\Throwable $e) {
                    $messages[] = "{$child->full_name} failed: " . $e->getMessage();
                }
            }
        }

        if ($approvedCount === 0) {
            return back()->with('success', 'All enrollees in this family are already approved.');
        }

        return back()->with('success', 'Family enrollees approved successfully: ' . implode(' | ', $messages));
    }

    private function ensureApplicationReviewer(): void
    {
        abort_unless(auth()->user()?->canReviewEnrollmentApplications(), 403);
    }
}
