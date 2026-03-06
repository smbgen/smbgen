<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'booking_id',
        'to_email',
        'cc_email',
        'subject',
        'body',
        'status',
        'sent_at',
        'delivered_at',
        'bounced_at',
        'opened_at',
        'clicked_at',
        'error_message',
        'smtp_response',
        'open_count',
        'click_count',
        'last_opened_at',
        'last_clicked_at',
        'tracking_id',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'bounced_at' => 'datetime',
        'opened_at' => 'datetime',
        'clicked_at' => 'datetime',
        'last_opened_at' => 'datetime',
        'last_clicked_at' => 'datetime',
        'open_count' => 'integer',
        'click_count' => 'integer',
    ];

    protected $appends = [
        'status_icon',
        'status_badge',
    ];

    /**
     * Get the user who sent the email
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the booking associated with this email
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Scope to get emails sent in the last N hours
     */
    public function scopeRecentHours($query, int $hours = 24)
    {
        return $query->where('sent_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope to get emails by status
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get delivery rate percentage
     */
    public static function getDeliveryRate(int $hours = 24): float
    {
        $sent = self::recentHours($hours)->whereNotNull('sent_at')->count();

        if ($sent === 0) {
            return 0;
        }

        $delivered = self::recentHours($hours)
            ->whereIn('status', ['delivered', 'opened', 'clicked'])
            ->count();

        return round(($delivered / $sent) * 100, 1);
    }

    /**
     * Get open rate percentage
     */
    public static function getOpenRate(int $hours = 24): float
    {
        $sent = self::recentHours($hours)->whereNotNull('sent_at')->count();

        if ($sent === 0) {
            return 0;
        }

        $opened = self::recentHours($hours)
            ->whereIn('status', ['opened', 'clicked'])
            ->count();

        return round(($opened / $sent) * 100, 1);
    }

    /**
     * Get click rate percentage
     */
    public static function getClickRate(int $hours = 24): float
    {
        $sent = self::recentHours($hours)->whereNotNull('sent_at')->count();

        if ($sent === 0) {
            return 0;
        }

        $clicked = self::recentHours($hours)->status('clicked')->count();

        return round(($clicked / $sent) * 100, 1);
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => '<span class="px-2 py-1 text-xs bg-gray-200 text-gray-800 rounded">Pending</span>',
            'sent' => '<span class="px-2 py-1 text-xs bg-blue-200 text-blue-800 rounded">📤 Sent</span>',
            'delivered' => '<span class="px-2 py-1 text-xs bg-green-200 text-green-800 rounded">✅ Delivered</span>',
            'opened' => '<span class="px-2 py-1 text-xs bg-green-200 text-green-800 rounded">✅ Opened</span>',
            'clicked' => '<span class="px-2 py-1 text-xs bg-green-200 text-green-800 rounded">✅ Clicked</span>',
            'bounced' => '<span class="px-2 py-1 text-xs bg-red-200 text-red-800 rounded">❌ Bounced</span>',
            'failed' => '<span class="px-2 py-1 text-xs bg-red-200 text-red-800 rounded">❌ Failed</span>',
            default => '<span class="px-2 py-1 text-xs bg-gray-200 text-gray-800 rounded">Unknown</span>',
        };
    }

    /**
     * Get status icon
     */
    public function getStatusIconAttribute(): string
    {
        return match ($this->status) {
            'pending' => '⏳',
            'sent' => '📤',
            'delivered' => '✅',
            'opened' => '👀',
            'clicked' => '🖱️',
            'bounced' => '❌',
            'failed' => '⚠️',
            default => '❓',
        };
    }

    /**
     * Format datetime in user's timezone
     */
    public function formatTimestamp($timestamp, $format = 'M j, Y g:i A'): string
    {
        if (! $timestamp) {
            return '—';
        }

        return $timestamp->setTimezone(config('app.timezone'))->format($format);
    }
}
