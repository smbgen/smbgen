<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    protected $fillable = [
        'user_id',
        'invoice_id',
        'amount',
        'currency',
        'description',
        'stripe_payment_intent_id',
        'stripe_session_id',
        'status', // pending, completed, failed, cancelled
        'payment_type', // invoice, product, subscription
        'metadata',
    ];

    protected $casts = [
        'amount' => 'integer', // Store in cents
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getFormattedAmountAttribute(): string
    {
        return '$'.number_format($this->amount / 100, 2);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('payment_type', $type);
    }
}
