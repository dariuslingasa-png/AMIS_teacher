<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\PaymentHelperTrait;
use App\Models\Payment;
use App\Models\StudentAccount;
use App\Models\StudentAccountPayment;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class AdminSoaController extends Controller
{
    use PaymentHelperTrait;

    public function index(Request $request)
    {
        $query = StudentAccount::with(['student.applicant', 'applicant'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('grade')) {
            $query->where('grade_level', $request->grade);
        }
        if ($request->filled('search')) {
            $s = $request->search;
            $query->whereHas('student', fn($q) =>
                $q->where('student_number', 'like', "%{$s}%")
                  ->orWhereHas('applicant', fn($a) =>
                      $a->where('first_name', 'like', "%{$s}%")
                        ->orWhere('last_name', 'like', "%{$s}%")
                  )
            );
        }

        $accounts = $query->get();

        // Group accounts by family robustly
        $familyRows = $accounts->groupBy(function ($account) {
            $applicant = $account->applicant;
            if (!$applicant) {
                return 'single:' . $account->id;
            }
            if ($applicant->family_application_id) {
                return 'family:' . $applicant->family_application_id;
            }
            return 'user:' . $applicant->user_id;
        })->map(function ($familyAccounts, $key) {
            $first = $familyAccounts->first();
            $applicant = $first->applicant;

            // Sort sibling accounts
            $children = $familyAccounts->sortBy('id');

            // Sum totals
            $totalAmount = $children->sum(fn($a) => (float) ($a->total_balance ?? 0));
            $paidAmount = $children->sum(fn($a) => (float) ($a->amount_paid ?? 0));
            $remainingBalance = $children->sum(fn($a) => (float) ($a->remaining_balance ?? 0));

            $familyLabel = $this->familyLabel($children->map(fn($a) => $a->applicant)->filter(), $applicant);
            $familyNo = $applicant?->family_application_id ?: $applicant?->id;

            return [
                'key' => $key,
                'family_no' => $familyNo,
                'family_label' => $familyLabel,
                'accounts' => $children,
                'total_amount' => $totalAmount,
                'amount_paid' => $paidAmount,
                'remaining_balance' => $remainingBalance,
                'status' => $remainingBalance <= 0 ? 'paid' : ($paidAmount > 0 ? 'partial' : 'unpaid'),
            ];
        })->values();

        // Chronologically order families with active balances first
        $familyRows = $familyRows->sortByDesc(fn($f) => $f['remaining_balance'])->values();

        $page = max((int) $request->input('page', 1), 1);
        $perPage = 15;

        $groupedFamilies = new \Illuminate\Pagination\LengthAwarePaginator(
            $familyRows->forPage($page, $perPage)->values(),
            $familyRows->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );

        return view('admin.soa.index', compact('groupedFamilies'));
    }

    public function show(StudentAccount $account)
    {
        $account->load('student.applicant.payment', 'applicant.payment', 'monthlyBillings', 'payments');

        $studentName = $account->student?->applicant?->full_name ?: ($account->applicant?->full_name ?: 'Student');

        $breadcrumbs = [
            ['label' => 'Soa', 'href' => route('admin.soa.index')],
            ['label' => $studentName, 'href' => null],
        ];

        return view('admin.soa.show', compact('account', 'breadcrumbs'));
    }

    public function verifyPayment(StudentAccountPayment $payment)
    {
        $payment->update(['status' => 'verified', 'verified_at' => now()]);

        // Mark the monthly billing as paid if linked
        if ($payment->soa_monthly_billing_id) {
            $payment->monthlyBilling?->update(['status' => 'paid', 'paid_at' => now()]);
        }

        // Recalculate SOA totals
        $payment->studentAccount->recalculate();

        return back()->with('success', 'Payment verified.');
    }

    public function rejectPayment(Request $request, StudentAccountPayment $payment)
    {
        $request->validate(['remarks' => 'required|string|max:500']);
        $payment->update(['status' => 'rejected', 'remarks' => $request->remarks]);
        return back()->with('success', 'Payment rejected.');
    }

    public function addPayment(Request $request, StudentAccount $account)
    {
        $validated = $request->validate([
            'amount'                 => 'required|numeric|min:1|max:' . $account->remaining_balance,
            'method'                 => 'required|in:cash,gcash,maya,bdo',
            'reference_no'           => 'nullable|string|max:100',
            'or_number'              => 'required|string|max:100',
            'purpose'                => 'nullable|string|max:100',
            'checked_by'             => 'nullable|string|max:100',
            'account_received'       => 'nullable|string|max:100',
            'soa_monthly_billing_id' => 'nullable|exists:soa_monthly_billings,id',
            'receipt'                => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $orNumber = trim($validated['or_number']);
        $orExistsForStudent = StudentAccountPayment::where('student_id', $account->student_id)
            ->whereRaw('LOWER(or_number) = ?', [mb_strtolower($orNumber)])
            ->exists();
        $orExistsOnEnrollmentPayment = Payment::where('enrollment_applicant_id', $account->enrollment_applicant_id)
            ->whereRaw('LOWER(or_number) = ?', [mb_strtolower($orNumber)])
            ->exists();

        if ($orExistsForStudent || $orExistsOnEnrollmentPayment) {
            throw ValidationException::withMessages([
                'or_number' => 'This OR number already exists for this student.',
            ]);
        }

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts/soa/' . $account->student_id, 'public');
        }

        \App\Models\StudentAccountPayment::create([
            'student_account_id'     => $account->id,
            'student_id'             => $account->student_id,
            'soa_monthly_billing_id' => $validated['soa_monthly_billing_id'] ?? null,
            'method'                 => $validated['method'],
            'reference_no'           => $validated['reference_no'] ?? null,
            'or_number'              => $orNumber,
            'checked_by'             => $validated['checked_by'] ?? null,
            'account_received'       => $validated['account_received'] ?? null,
            'amount'                 => $validated['amount'],
            'remarks'                => $validated['purpose'] ?? 'Tuition Fee',
            'receipt_url'            => $receiptPath,
            'status'                 => 'verified',
            'verified_at'            => now(),
            'paid_at'                => now(),
        ]);

        // Mark monthly billing as paid if linked
        if (!empty($validated['soa_monthly_billing_id'])) {
            \App\Models\SoaMonthlyBilling::find($validated['soa_monthly_billing_id'])
                ?->update(['status' => 'paid', 'paid_at' => now()]);
        }

        // Recalculate SOA
        $account->recalculate();

        return back()->with('success', 'Payment of PHP ' . number_format((float) $validated['amount'], 2) . ' recorded successfully.');
    }
}
