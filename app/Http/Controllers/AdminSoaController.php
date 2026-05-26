<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentAccount;
use App\Models\StudentAccountPayment;
use Illuminate\Http\Request;

class AdminSoaController extends Controller
{
    public function index(Request $request)
    {
        $query = StudentAccount::with('student.applicant')->latest();

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

        $accounts = $query->paginate(20);

        return view('admin.soa.index', compact('accounts'));
    }

    public function show(StudentAccount $account)
    {
        $account->load('student.applicant.payment', 'monthlyBillings', 'payments');
        return view('admin.soa.show', compact('account'));
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
        $request->validate([
            'amount'                 => 'required|numeric|min:1|max:' . $account->remaining_balance,
            'method'                 => 'required|in:cash,gcash,maya,bdo',
            'reference_no'           => 'nullable|string|max:100',
            'or_number'              => 'nullable|string|max:100',
            'purpose'                => 'nullable|string|max:100',
            'checked_by'             => 'nullable|string|max:100',
            'account_received'       => 'nullable|string|max:100',
            'soa_monthly_billing_id' => 'nullable|exists:soa_monthly_billings,id',
            'receipt'                => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $receiptPath = null;
        if ($request->hasFile('receipt')) {
            $receiptPath = $request->file('receipt')->store('receipts/soa/' . $account->student_id, 'public');
        }

        $payment = \App\Models\StudentAccountPayment::create([
            'student_account_id'     => $account->id,
            'student_id'             => $account->student_id,
            'soa_monthly_billing_id' => $request->soa_monthly_billing_id,
            'method'                 => $request->method,
            'reference_no'           => $request->reference_no,
            'or_number'              => $request->or_number ?: ('7010' . rand(1000, 9999)),
            'checked_by'             => $request->checked_by,
            'account_received'       => $request->account_received,
            'amount'                 => $request->amount,
            'remarks'                => $request->purpose ?: 'Tuition Fee',
            'receipt_url'            => $receiptPath,
            'status'                 => 'verified',
            'verified_at'            => now(),
            'paid_at'                => now(),
        ]);

        // Mark monthly billing as paid if linked
        if ($request->soa_monthly_billing_id) {
            \App\Models\SoaMonthlyBilling::find($request->soa_monthly_billing_id)
                ?->update(['status' => 'paid', 'paid_at' => now()]);
        }

        // Recalculate SOA
        $account->recalculate();

        return back()->with('success', 'Payment of PHP ' . number_format($request->amount, 2) . ' recorded successfully.');
    }
}
