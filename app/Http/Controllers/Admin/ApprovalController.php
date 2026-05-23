<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
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
        $this->reviewService->updateStatus($request, $applicant);

        return back()->with('success', 'Application status updated.');
    }

    public function approve(EnrollmentApplicant $applicant)
    {
        return back()->with('success', $this->approvalService->approve($applicant));
    }
}
