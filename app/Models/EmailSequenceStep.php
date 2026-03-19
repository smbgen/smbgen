<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class EmailSequenceStep extends Model
{
    /** @use HasFactory<\Database\Factories\EmailSequenceStepFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'email_sequence_id',
        'position',
        'subject',
        'body',
        'delay_days',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'delay_days' => 'integer',
        ];
    }

    public function sequence(): BelongsTo
    {
        return $this->belongsTo(EmailSequence::class, 'email_sequence_id');
    }
}
