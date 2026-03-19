<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class EmailSequenceEnrollment extends Model
{
    /** @use HasFactory<\Database\Factories\EmailSequenceEnrollmentFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'email_sequence_id',
        'email',
        'contact_name',
        'current_step',
        'status',
        'started_at',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'current_step' => 'integer',
            'started_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function sequence(): BelongsTo
    {
        return $this->belongsTo(EmailSequence::class, 'email_sequence_id');
    }
}
