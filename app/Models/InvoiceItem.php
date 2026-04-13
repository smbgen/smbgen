<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_amount',
        'total_amount',
        'metadata',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_amount' => 'integer',
        'total_amount' => 'integer',
        'metadata' => 'array',
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function getFormattedUnitAttribute(): string
    {
        return '$'.number_format(($this->unit_amount ?? 0) / 100, 2);
    }

    public function getFormattedTotalAttribute(): string
    {
        return '$'.number_format(($this->total_amount ?? 0) / 100, 2);
    }
}
