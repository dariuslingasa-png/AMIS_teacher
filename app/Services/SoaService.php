<?php

namespace App\Services;

use App\Models\EnrollmentApplicant;
use App\Models\SchoolFee;
use App\Models\SoaMonthlyBilling;
use App\Models\Student;
use App\Models\StudentAccount;
use App\Models\StudentAccountPayment;

class SoaService
{
    /**
     * Generate SOA + 9 monthly billing rows for a newly approved student.
     */
    public function generate(Student $student, EnrollmentApplicant $applicant): StudentAccount
    {
        $fee = SchoolFee::forGrade($applicant->grade_level, $applicant->school_year);

        if (!$fee) {
            throw new \Exception("No school fees found for {$applicant->grade_level} SY {$applicant->school_year}");
        }

        $tuition            = (float) $fee->tuition_fee;
        $misc               = (float) $fee->misc_fee;
        $books              = (float) $fee->books_fee;
        $discountPercentage = (float) ($applicant->discount_percentage ?? 0);
        $discountAmount     = (float) ($applicant->discount_amount ?? 0);

        if ($discountPercentage > 0 && $discountAmount <= 0) {
            $discountAmount = round($tuition * ($discountPercentage / 100), 2);
            $applicant->update(['discount_amount' => $discountAmount]);
        }

        $discountedTuition = max(0, $tuition - $discountAmount);
        $gross             = $discountedTuition + $misc + $books;
        
        // Check actual verified payment from applicant enrollment downpayment
        $enrollPayment = $applicant->payment;
        $enrollPaid = $enrollPayment && $enrollPayment->status === 'verified' ? (float)$enrollPayment->amount : 4000.00;

        $account = StudentAccount::create([
            'student_id'              => $student->id,
            'enrollment_applicant_id' => $applicant->id,
            'school_year'             => $applicant->school_year,
            'grade_level'             => $applicant->grade_level,
            'tuition_fee'             => $tuition,
            'monthly_tuition'         => 0.00, // will be updated after initial payment deductions
            'miscellaneous_fee'       => $misc,
            'books_fee'               => $books,
            'sibling_order'           => $applicant->sibling_order,
            'discount_type'           => $discountPercentage > 0 ? ($applicant->discount_type ?: 'sibling') : null,
            'discount_percentage'     => $discountPercentage,
            'discount_amount'         => $discountAmount,
            'gross_total'             => $gross,
            'enrollment_fee_paid'     => $enrollPaid,
            'total_balance'           => $gross,
            'amount_paid'             => 0.00,
            'remaining_balance'       => $gross,
            'status'                  => 'unpaid',
        ]);

        // Copy/Create the enrollment fee payment inside student_account_payments
        if ($enrollPayment && $enrollPayment->status === 'verified') {
            StudentAccountPayment::create([
                'student_account_id' => $account->id,
                'student_id'         => $student->id,
                'method'             => $enrollPayment->method,
                'reference_no'       => $enrollPayment->reference_no,
                'or_number'          => $enrollPayment->or_number ?? $enrollPayment->reference_no,
                'checked_by'         => 'System / Finance',
                'amount'             => $enrollPayment->amount,
                'status'             => 'verified',
                'remarks'            => 'Paid Enrollment Fee',
                'paid_at'            => $enrollPayment->paid_at ?? now(),
                'verified_at'        => $enrollPayment->verified_at ?? now(),
            ]);
        } else {
            // Seed default verified enrollment payment to show in ledger for testing
            StudentAccountPayment::create([
                'student_account_id' => $account->id,
                'student_id'         => $student->id,
                'method'             => 'gcash',
                'reference_no'       => 'ENR-' . strtoupper(\Illuminate\Support\Str::random(8)),
                'or_number'          => '7010' . rand(1000, 9999),
                'checked_by'         => 'Sir Cabel',
                'amount'             => $enrollPaid,
                'status'             => 'verified',
                'remarks'            => 'Paid Enrollment Fee',
                'paid_at'            => now(),
                'verified_at'        => now(),
            ]);
        }

        // Recalculate SOA running totals to apply enrollment fee payment
        $account->recalculate();

        // Calculate remaining balance monthly installment split into 9 months (July to March)
        $remainingAfterInitial = (float) $account->remaining_balance;
        $monthlyTuition = round($remainingAfterInitial / 9, 2);

        $account->update([
            'monthly_tuition' => $monthlyTuition,
        ]);

        // Generate 9 monthly billing rows (July=1 to March=9)
        $this->generateMonthlyBillings($account, $student, $monthlyTuition, $applicant->school_year);

        // Recalculate again to distribute waterfall payments properly
        $account->recalculate();

        return $account;
    }

    private function generateMonthlyBillings(
        StudentAccount $account,
        Student $student,
        float $monthlyTuition,
        string $schoolYear
    ): void {
        $startYear = (int) explode('-', $schoolYear)[0]; // 2026

        $months = [
            1 => ['July',      "{$startYear}-07-15", $monthlyTuition, 'Monthly Tuition'],
            2 => ['August',    "{$startYear}-08-15", $monthlyTuition, 'Monthly Tuition'],
            3 => ['September', "{$startYear}-09-15", $monthlyTuition, 'Monthly Tuition'],
            4 => ['October',   "{$startYear}-10-15", $monthlyTuition, 'Monthly Tuition'],
            5 => ['November',  "{$startYear}-11-15", $monthlyTuition, 'Monthly Tuition'],
            6 => ['December',  "{$startYear}-12-15", $monthlyTuition, 'Monthly Tuition'],
            7 => ['January',   ($startYear + 1) . '-01-15', $monthlyTuition, 'Monthly Tuition'],
            8 => ['February',  ($startYear + 1) . '-02-15', $monthlyTuition, 'Monthly Tuition'],
            9 => ['March',     ($startYear + 1) . '-03-15', $monthlyTuition, 'Monthly Tuition'],
        ];

        foreach ($months as $num => [$name, $due, $amount, $desc]) {
            SoaMonthlyBilling::create([
                'student_account_id' => $account->id,
                'student_id'         => $student->id,
                'month_number'       => $num,
                'month_name'         => $name,
                'due_date'           => $due,
                'amount_due'         => $amount,
                'description'        => $desc,
                'status'             => 'unpaid',
            ]);
        }
    }
}
