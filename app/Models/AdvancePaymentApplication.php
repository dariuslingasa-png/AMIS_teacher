<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdvancePaymentApplication extends Model
{
    protected $fillable = [
        'advance_payment_id',
        'target_invoice_id',
        'amount_applied',
    ];

    protected $casts = [
        'amount_applied' => 'decimal:2',
    ];

    public function advancePayment(): BelongsTo
    {
        return $this->belongsTo(AdvancePayment::class);
    }

    public function targetInvoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'target_invoice_id');
    }
}
