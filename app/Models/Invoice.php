<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'currency',
        'memo',
        'due_date',
        'total_amount',
        'paid_at',
        'stripe_payment_intent_id',
        'stripe_checkout_session_id',
        'stripe_client_secret',
        'stripe_payment_url',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'paid_at' => 'datetime',
        'total_amount' => 'integer',
    ];

    public const STATUS_DRAFT = 'draft';

    public const STATUS_SENT = 'sent';

    public const STATUS_PAID = 'paid';

    public const STATUS_VOID = 'void';

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return '$'.number_format(($this->total_amount ?? 0) / 100, 2);
    }

    public function recalculateTotals(): void
    {
        $sum = $this->items()->sum('total_amount');
        $this->total_amount = (int) $sum;
        $this->save();
    }

    public function hasStripePaymentUrl(): bool
    {
        return ! empty($this->stripe_payment_url);
    }

    public function isStripeProcessed(): bool
    {
        return ! empty($this->stripe_payment_intent_id);
    }
}
