<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\EnrollmentApplicant;
use App\Models\StudentAccount;
use App\Models\StudentAccountPayment;
use Illuminate\Http\Request;

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

        return view('admin.payments.dashboard', compact('stats', 'recentPayments', 'recentSoaPayments', 'openAccounts', 'familyChildrenByPayment', 'familyLabelsByPayment'));
    }

    public function index(Request $request)
    {
        $query = Payment::with('applicant.user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(20);
        $familyChildrenByPayment = $this->familyChildrenByPayment($payments->getCollection());
        $familyLabelsByPayment = $this->familyLabelsByPayment($payments->getCollection(), $familyChildrenByPayment);

        return view('admin.payments.index', compact('payments', 'familyChildrenByPayment', 'familyLabelsByPayment'));
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


    public function verify(Payment $payment)
    {
        if (blank($payment->receipt_url)) {
            return back()->withErrors(['status' => 'Cannot verify: payment proof is missing.']);
        }

        $payment->update([
            'status'      => 'verified',
            'verified_at' => now(),
        ]);

        return back()->with('success', 'Payment verified successfully.');
    }

    public function reject(Request $request, Payment $payment)
    {
        $request->validate(['remarks' => 'required|string|max:500']);

        $payment->update([
            'status'  => 'rejected',
            'remarks' => $request->remarks,
        ]);

        return back()->with('success', 'Payment rejected.');
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
