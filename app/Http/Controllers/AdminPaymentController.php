<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\AdminAuditLog;
use App\Models\EnrollmentApplicant;
use App\Models\StudentAccount;
use App\Models\StudentAccountPayment;
use App\Services\Admin\Enrollment\EnrollmentApprovalService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminPaymentController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'pending' => Payment::where('status', 'pending')->whereNotNull('receipt_url')->count(),
            'verified' => Payment::where('status', 'verified')->count(),
            'rejected' => Payment::where('status', 'rejected')->count(),
            'missing' => EnrollmentApplicant::whereNotIn('status', ['draft'])
                ->where(function ($query) {
                    $query->whereDoesntHave('payment')
                        ->orWhereHas('payment', fn ($payment) => $payment->whereNull('receipt_url'));
                })
                ->count(),
            'soa_balance' => StudentAccount::sum('remaining_balance'),
            'soa_paid' => StudentAccount::sum('amount_paid'),
            'soa_partial' => StudentAccount::where('status', 'partial')->count(),
            'soa_unpaid' => StudentAccount::where('status', 'unpaid')->count(),
        ];

        $recentPayments = Payment::with('applicant.user')
            ->latest()
            ->take(8)
            ->get();
        $familyChildrenByPayment = $this->familyChildrenByPayment($recentPayments);
        $familyLabelsByPayment = $this->familyLabelsByPayment($recentPayments, $familyChildrenByPayment);

        $recentSoaPayments = StudentAccountPayment::with('student.applicant', 'studentAccount')
            ->latest()
            ->take(6)
            ->get();

        $openAccounts = StudentAccount::with('student.applicant')
            ->where('remaining_balance', '>', 0)
            ->latest()
            ->take(6)
            ->get();

        $financeCharts = $this->financeCharts($stats);

        return view('admin.payments.dashboard', compact('stats', 'financeCharts', 'recentPayments', 'recentSoaPayments', 'openAccounts', 'familyChildrenByPayment', 'familyLabelsByPayment'));
    }

    public function index(Request $request)
    {
        $query = Payment::with('applicant.user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $familyRows = $this->paymentFamilyRows($query->get());
        $page = max((int) $request->input('page', 1), 1);
        $perPage = 20;

        $paymentFamilies = new LengthAwarePaginator(
            $familyRows->forPage($page, $perPage)->values(),
            $familyRows->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('admin.payments.index', compact('paymentFamilies'));
    }

    public function show(Payment $payment)
    {
        $payment->load('applicant.user');
        $applicant = $payment->applicant;
        $familyChildren = collect();
        $familyLabel = 'FAMILY';

        if ($applicant) {
            $familyChildren = EnrollmentApplicant::with('payment')
                ->where(function ($query) use ($applicant) {
                    if ($applicant->family_application_id) {
                        $query->where('family_application_id', $applicant->family_application_id);
                    } else {
                        $query->where('user_id', $applicant->user_id);
                    }
                })
                ->orderBy('id')
                ->get();
            $familyLabel = $this->familyLabel($familyChildren, $applicant);
        }

        return view('admin.payments.show', compact('payment', 'applicant', 'familyChildren', 'familyLabel'));
    }


    public function verify(Request $request, Payment $payment)
    {
        $this->ensurePaymentReviewer();

        if (blank($payment->receipt_url)) {
            return back()->withErrors(['status' => 'Cannot verify: payment proof is missing.']);
        }

        $orNumber = $request->input('or_number');
        if (blank($orNumber)) {
            $orNumber = '7010' . rand(1000, 9999);
        }

        $payment->update([
            'status'      => 'verified',
            'or_number'   => $orNumber,
            'verified_at' => now(),
        ]);

        AdminAuditLog::record('payment_approved', true, 'Payment proof approved.', [
            'payment_id' => $payment->id,
            'applicant_id' => $payment->enrollment_applicant_id,
            'amount' => $payment->amount,
            'method' => $payment->method,
        ]);

        $approvalMessage = null;
        $payment->loadMissing('applicant.student');

        if ($payment->applicant) {
            $payment->applicant->setRelation('payment', $payment);
            
            $applicant = $payment->applicant;
            $familyChildren = collect();
            if ($applicant->family_application_id) {
                $familyChildren = EnrollmentApplicant::where('family_application_id', $applicant->family_application_id)->get();
            } else {
                $familyChildren = EnrollmentApplicant::where('user_id', $applicant->user_id)->get();
            }

            $approvedNames = [];
            foreach ($familyChildren as $sibling) {
                if ($sibling->status !== 'approved' && in_array($sibling->status, ['submitted', 'under_review', 'pending', 'ready_for_submission'], true)) {
                    $sibling->setRelation('payment', $payment);
                    try {
                        app(EnrollmentApprovalService::class)->approve($sibling);
                        $approvedNames[] = trim($sibling->first_name . ' ' . $sibling->last_name);
                    } catch (\Throwable $e) {
                        \Illuminate\Support\Facades\Log::error("Failed to auto-approve sibling {$sibling->id}: " . $e->getMessage());
                    }
                }
            }

            if (count($approvedNames) > 0) {
                $approvalMessage = "Payment verified successfully. Auto-approved: " . implode(', ', $approvedNames) . ".";
            } else if ($payment->applicant->status === 'approved') {
                $approvalMessage = 'Payment verified. Student already onboarded.';
            }
        }

        return back()->with('success', $approvalMessage ?: 'Payment verified successfully.');
    }

    public function reject(Request $request, Payment $payment)
    {
        $this->ensurePaymentReviewer();

        $request->validate(['remarks' => 'required|string|max:500']);

        $payment->update([
            'status'  => 'rejected',
            'remarks' => $request->remarks,
        ]);

        $payment->loadMissing('applicant');
        $payment->applicant?->update([
            'status' => 'rejected',
            'review_remarks' => $request->remarks,
        ]);

        AdminAuditLog::record('payment_rejected', true, 'Payment proof rejected.', [
            'payment_id' => $payment->id,
            'applicant_id' => $payment->enrollment_applicant_id,
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'Payment rejected.');
    }

    private function ensurePaymentReviewer(): void
    {
        abort_unless(auth()->user()?->canReviewEnrollmentPayments(), 403);
    }

    private function financeCharts(array $stats): array
    {
        $startDate = Carbon::today()->subDays(6);
        $labels = collect(range(0, 6))
            ->map(fn ($day) => $startDate->copy()->addDays($day)->format('M d'))
            ->values();

        $enrollmentPayments = Payment::whereNotNull('receipt_url')
            ->where('created_at', '>=', $startDate->copy()->startOfDay())
            ->get(['amount', 'created_at'])
            ->groupBy(fn ($payment) => $payment->created_at?->format('M d'));

        $soaPayments = StudentAccountPayment::where('created_at', '>=', $startDate->copy()->startOfDay())
            ->get(['amount', 'created_at'])
            ->groupBy(fn ($payment) => $payment->created_at?->format('M d'));

        return [
            'paymentStatus' => [
                'labels' => ['Pending Proofs', 'Approved', 'Rejected', 'Missing Proof'],
                'data' => [
                    (int) $stats['pending'],
                    (int) $stats['verified'],
                    (int) $stats['rejected'],
                    (int) $stats['missing'],
                ],
            ],
            'soaStatus' => [
                'labels' => ['Paid', 'Partial', 'Unpaid'],
                'data' => [
                    StudentAccount::where('status', 'paid')->count(),
                    (int) $stats['soa_partial'],
                    (int) $stats['soa_unpaid'],
                ],
            ],
            'collectionTrend' => [
                'labels' => $labels,
                'enrollment' => $labels
                    ->map(fn ($label) => (float) ($enrollmentPayments->get($label, collect())->sum('amount')))
                    ->values(),
                'soa' => $labels
                    ->map(fn ($label) => (float) ($soaPayments->get($label, collect())->sum('amount')))
                    ->values(),
            ],
            'soaMoney' => [
                'labels' => ['SOA Paid', 'SOA Balance'],
                'data' => [
                    (float) $stats['soa_paid'],
                    (float) $stats['soa_balance'],
                ],
            ],
        ];
    }

    private function familyChildrenByPayment($payments): array
    {
        $applicants = $payments->pluck('applicant')->filter();

        if ($applicants->isEmpty()) {
            return [];
        }

        $familyIds = $applicants
            ->pluck('family_application_id')
            ->filter()
            ->unique()
            ->values();

        $userIds = $applicants
            ->filter(fn ($applicant) => blank($applicant->family_application_id) && filled($applicant->user_id))
            ->pluck('user_id')
            ->unique()
            ->values();

        if ($familyIds->isEmpty() && $userIds->isEmpty()) {
            return $payments->mapWithKeys(fn ($payment) => [
                $payment->id => collect([$payment->applicant])->filter(),
            ])->all();
        }

        $children = EnrollmentApplicant::with('payment')
            ->where(function ($query) use ($familyIds, $userIds) {
                if ($familyIds->isNotEmpty()) {
                    $query->whereIn('family_application_id', $familyIds);
                }

                if ($userIds->isNotEmpty()) {
                    $method = $familyIds->isNotEmpty() ? 'orWhereIn' : 'whereIn';
                    $query->{$method}('user_id', $userIds);
                }
            })
            ->orderBy('id')
            ->get()
            ->groupBy(fn ($child) => $child->family_application_id ? 'family:'.$child->family_application_id : 'user:'.$child->user_id);

        return $payments->mapWithKeys(function ($payment) use ($children) {
            $applicant = $payment->applicant;

            if (!$applicant) {
                return [$payment->id => collect()];
            }

            $key = $applicant->family_application_id ? 'family:'.$applicant->family_application_id : 'user:'.$applicant->user_id;

            return [$payment->id => $children->get($key, collect([$applicant]))];
        })->all();
    }

    private function familyLabelsByPayment($payments, array $familyChildrenByPayment): array
    {
        return $payments->mapWithKeys(function ($payment) use ($familyChildrenByPayment) {
            $children = $familyChildrenByPayment[$payment->id] ?? collect([$payment->applicant])->filter();

            return [$payment->id => $this->familyLabel($children, $payment->applicant)];
        })->all();
    }

    private function paymentFamilyRows($payments)
    {
        $familyChildrenByPayment = $this->familyChildrenByPayment($payments);

        return $payments
            ->filter(fn ($payment) => $payment->applicant)
            ->groupBy(fn ($payment) => $this->paymentFamilyKey($payment))
            ->map(function ($familyPayments) use ($familyChildrenByPayment) {
                $representative = $familyPayments
                    ->sortByDesc(fn ($payment) => optional($payment->updated_at)->timestamp ?? 0)
                    ->first();
                $applicant = $representative->applicant;
                $children = $familyChildrenByPayment[$representative->id] ?? collect([$applicant])->filter();
                $paymentsForTotal = $children
                    ->pluck('payment')
                    ->filter(fn ($payment) => $payment && filled($payment->receipt_url));
                $paymentsForStatus = $paymentsForTotal->isNotEmpty() ? $paymentsForTotal : $familyPayments;
                $statuses = $paymentsForStatus
                    ->pluck('status')
                    ->map(fn ($status) => strtolower((string) ($status ?: 'pending')))
                    ->filter()
                    ->values();

                return [
                    'key' => $this->paymentFamilyKey($representative),
                    'payment' => $representative,
                    'payments' => $familyPayments->values(),
                    'children' => $children,
                    'family_no' => $applicant?->family_application_id ?: $applicant?->id,
                    'family_label' => $this->familyLabel($children, $applicant),
                    'amount' => $paymentsForTotal->isNotEmpty()
                        ? $paymentsForTotal->sum(fn ($payment) => (float) ($payment->amount ?? 0))
                        : $familyPayments->sum(fn ($payment) => (float) ($payment->amount ?? 0)),
                    'methods' => $paymentsForStatus
                        ->pluck('method')
                        ->filter()
                        ->map(fn ($method) => strtoupper((string) $method))
                        ->unique()
                        ->values(),
                    'status' => $this->familyPaymentStatus($statuses),
                    'updated_at' => $paymentsForStatus
                        ->sortByDesc(fn ($payment) => optional($payment->updated_at)->timestamp ?? 0)
                        ->first()?->updated_at,
                ];
            })
            ->sortByDesc(fn ($row) => optional($row['updated_at'])->timestamp ?? 0)
            ->values();
    }

    private function paymentFamilyKey(Payment $payment): string
    {
        $applicant = $payment->applicant;

        if (!$applicant) {
            return 'payment:'.$payment->id;
        }

        if (filled($applicant->family_application_id)) {
            return 'family:'.$applicant->family_application_id;
        }

        return filled($applicant->user_id) ? 'user:'.$applicant->user_id : 'applicant:'.$applicant->id;
    }

    private function familyPaymentStatus($statuses): string
    {
        if ($statuses->isEmpty()) {
            return 'pending';
        }

        if ($statuses->contains('rejected')) {
            return 'rejected';
        }

        if ($statuses->every(fn ($status) => $status === 'verified')) {
            return 'verified';
        }

        return 'pending';
    }

    private function familyLabel($children, ?EnrollmentApplicant $fallback = null): string
    {
        $representative = $children->first() ?: $fallback;

        if (!$representative) {
            return 'FAMILY';
        }

        $lastName = $representative->father_last_name
            ?: $representative->mother_last_name
            ?: $representative->last_name;

        $firstName = $representative->father_first_name
            ?: $representative->mother_first_name
            ?: $representative->emergency_name
            ?: $representative->user?->name
            ?: $representative->first_name;

        $labelName = trim($lastName.' '.$firstName);

        return 'FAMILY OF '.strtoupper($labelName ?: $representative->full_name ?: 'GUARDIAN');
    }
}
