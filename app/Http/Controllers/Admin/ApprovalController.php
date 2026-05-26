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

    private function ensureApplicationReviewer(): void
    {
        abort_unless(auth()->user()?->canReviewEnrollmentApplications(), 403);
    }
}
