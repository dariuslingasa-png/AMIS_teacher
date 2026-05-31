<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\AdminAuditLog;
use App\Models\EnrollmentApplicant;
use App\Models\StudentAccount;
use App\Models\StudentAccountPayment;
use App\Http\Controllers\Traits\PaymentHelperTrait;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminPaymentController extends Controller
{
    use PaymentHelperTrait;

    /**
     * Display the Finance Dashboard statistics and charts.
     */
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

        return view('admin.payments.dashboard', compact(
            'stats',
            'financeCharts',
            'recentPayments',
            'recentSoaPayments',
            'openAccounts',
            'familyChildrenByPayment',
            'familyLabelsByPayment'
        ));
    }

    /**
     * Display the list of parent enrollment payments grouped by family batch.
     */
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

    /**
     * Display details and invoice review worksheet for a specific enrollment payment.
     */
    public function show(Payment $payment)
    {
        $payment->load('applicant.user');
        $applicant = $payment->applicant;
        $familyChildren = collect();
        $familyLabel = 'FAMILY';
        $invoice = null;

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

            // Fetch or lazily auto-generate the single family Invoice record!
            $invoice = \App\Models\Invoice::getOrCreateForFamily($applicant);
            if (!$payment->invoice_id) {
                $payment->update(['invoice_id' => $invoice->id]);
            }
            // Trigger auto-recalculation to retrospectively convert old ORs in DB!
            $invoice->recalculate();
        }

        return view('admin.payments.show', compact('payment', 'applicant', 'familyChildren', 'familyLabel', 'invoice'));
    }

    /**
     * Verify and approve a parent's enrollment payment proof.
     */
    public function verify(Request $request, Payment $payment)
    {
        $this->ensurePaymentReviewer();

        if (blank($payment->receipt_url)) {
            return back()->withErrors(['status' => 'Cannot verify: payment proof is missing.']);
        }

        $invoice = \App\Models\Invoice::getOrCreateForFamily($payment->applicant);
        if (!$payment->invoice_id) {
            $payment->invoice_id = $invoice->id;
        }

        $orNumber = $request->input('or_number');
        if (blank($orNumber)) {
            // Count verified payments already existing under this invoice
            $verifiedCount = $invoice->payments()->where('status', 'verified')->count();
            
            // Suffix the invoice number directly: e.g. INV-000204 -> OR-000204
            $baseOr = str_replace('INV-', 'OR-', $invoice->invoice_no);
            
            if ($verifiedCount === 0) {
                // First payment! Check if this payment is a full payment or partial payment.
                $isFullPayment = ((float)$payment->amount >= (float)$invoice->total_amount);
                if ($isFullPayment) {
                    $orNumber = $baseOr;
                } else {
                    $orNumber = $baseOr . '-1';
                }
            } else {
                // This is a subsequent installment payment!
                $orNumber = $baseOr . '-' . ($verifiedCount + 1);
            }
        }

        $payment->update([
            'status'      => 'verified',
            'or_number'   => $orNumber,
            'verified_at' => now(),
        ]);

        // Sync and recalculate the single Family Invoice totals & status!
        $invoice->recalculate();

        AdminAuditLog::record('payment_approved', true, 'Payment proof approved.', [
            'payment_id' => $payment->id,
            'applicant_id' => $payment->enrollment_applicant_id,
            'amount' => $payment->amount,
            'method' => $payment->method,
        ]);

        $payment->loadMissing('applicant.student');

        $approvalMessage = 'Payment verified successfully.';
        if ($payment->applicant && $payment->applicant->status === 'approved') {
            $approvalMessage = 'Payment verified. Student already onboarded.';
        }

        return back()->with('success', $approvalMessage);
    }

    /**
     * Reject a parent's enrollment payment proof with custom remarks.
     */
    public function reject(Request $request, Payment $payment)
    {
        $this->ensurePaymentReviewer();

        $request->validate(['remarks' => 'required|string|max:500']);

        $payment->update([
            'status'  => 'rejected',
            'remarks' => $request->remarks,
        ]);

        // Sync and recalculate the single Family Invoice totals & status!
        if ($payment->invoice_id) {
            $payment->invoice->recalculate();
        } else {
            $invoice = \App\Models\Invoice::getOrCreateForFamily($payment->applicant);
            $payment->update(['invoice_id' => $invoice->id]);
            $invoice->recalculate();
        }

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

    /**
     * Abort if the user doesn't have finance administrative reviewer clearance.
     */
    private function ensurePaymentReviewer(): void
    {
        abort_unless(auth()->user()?->canReviewEnrollmentPayments(), 403);
    }

    /**
     * Display discount and fee settings management view.
     */
    public function fees()
    {
        return view('admin.payments.fees');
    }
}
