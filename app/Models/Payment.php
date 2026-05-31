<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'enrollment_applicant_id',
        'invoice_id',
        'method',
        'reference_no',
        'or_number',
        'amount',
        'receipt_url',
        'status',
        'remarks',
        'paid_at',
        'verified_at',
    ];

    protected $casts = [
        'paid_at'     => 'datetime',
        'verified_at' => 'datetime',
        'amount'      => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(EnrollmentApplicant::class, 'enrollment_applicant_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function advancePayment(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(AdvancePayment::class, 'source_payment_id');
    }

    public function getMethodLabelAttribute(): string
    {
        return match($this->method) {
            'gcash' => 'GCash',
            'bdo'   => 'BDO Bank Transfer',
            default => ucfirst($this->method),
        };
    }
}
