<?php

namespace App\Models;

use App\Enums\EmailSequenceTrigger;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class EmailSequence extends Model
{
    /** @use HasFactory<\Database\Factories\EmailSequenceFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'name',
        'description',
        'trigger',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'trigger' => EmailSequenceTrigger::class,
        ];
    }

    public function steps(): HasMany
    {
        return $this->hasMany(EmailSequenceStep::class)->orderBy('position');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(EmailSequenceEnrollment::class);
    }
}
