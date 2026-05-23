<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EnrollmentApplicant;
use App\Services\Admin\Enrollment\EnrollmentReviewService;
use Illuminate\Http\Request;

class RequirementController extends Controller
{
    public function __construct(
        private readonly EnrollmentReviewService $reviewService,
    ) {}

    public function update(Request $request, EnrollmentApplicant $applicant)
    {
        $this->reviewService->updateDocumentStatus($request, $applicant);

        return back()->with('success', 'Document status updated.');
    }
}
