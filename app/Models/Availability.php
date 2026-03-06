<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    use HasFactory;

    protected $fillable = [
        'day_of_week',
        'start_time',
        'end_time',
        'duration',
        'break_period_minutes',
        'minimum_booking_notice_hours',
        'maximum_booking_days_ahead',
        'timezone',
        'is_active',
        'user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration' => 'integer',
        'break_period_minutes' => 'integer',
        'minimum_booking_notice_hours' => 'integer',
        'maximum_booking_days_ahead' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDayNameAttribute(): string
    {
        $names = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        return $names[$this->day_of_week] ?? 'Unknown';
    }
}
