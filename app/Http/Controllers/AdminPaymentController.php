<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;

class AdminPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = Payment::with('applicant.user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $payments = $query->paginate(20);

        return view('admin.payments.index', compact('payments'));
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
}
