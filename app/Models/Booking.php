<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_CONFIRMED = 'confirmed';

    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'customer_name',
        'customer_email',
        'customer_phone',
        'booking_date',
        'booking_time',
        'duration',
        'break_period_minutes',
        'property_address',
        'google_calendar_event_id',
        'google_meet_link',
        'status',
        'notes',
        'user_id',
        'staff_id',
        'custom_form_data',
    ];

    protected $casts = [
        'booking_date' => 'date',
        // 'time' is not a built-in cast in all Laravel versions; store as string and
        // parse as needed in accessors.
        'booking_time' => 'string',
        'duration' => 'integer',
        'custom_form_data' => 'array',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING,
        'duration' => 30,
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Returns a DateTime string combining booking_date and booking_time.
     */
    public function getStartsAtAttribute(): \DateTimeImmutable
    {
        $date = $this->booking_date?->format('Y-m-d') ?? now()->format('Y-m-d');

        // booking_time may be a string (e.g. '09:00:00') or a DateTime/Carbon instance.
        if ($this->booking_time instanceof \DateTimeInterface) {
            $time = $this->booking_time->format('H:i:s');
        } elseif (is_string($this->booking_time) && trim($this->booking_time) !== '') {
            $time = $this->booking_time;
        } else {
            $time = '00:00:00';
        }

        return new \DateTimeImmutable("{$date} {$time}");
    }
}
