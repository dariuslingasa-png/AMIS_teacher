<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminAuditLog extends Model
{
    protected $fillable = [
        'user_id',
        'event',
        'email',
        'ip_address',
        'user_agent',
        'successful',
        'message',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'successful' => 'boolean',
            'metadata' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
