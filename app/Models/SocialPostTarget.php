<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialPostTarget extends Model
{
    use HasFactory;

    const STATUS_PENDING = 'pending';

    const STATUS_PUBLISHING = 'publishing';

    const STATUS_PUBLISHED = 'published';

    const STATUS_FAILED = 'failed';

    const STATUS_SKIPPED = 'skipped';

    protected $fillable = [
        'social_post_id',
        'social_account_id',
        'status',
        'platform_post_id',
        'platform_post_url',
        'last_error',
        'attempt_count',
        'last_attempted_at',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'last_attempted_at' => 'datetime',
            'published_at' => 'datetime',
            'attempt_count' => 'integer',
        ];
    }

    /** @return BelongsTo<SocialPost, SocialPostTarget> */
    public function socialPost(): BelongsTo
    {
        return $this->belongsTo(SocialPost::class);
    }

    /** @return BelongsTo<SocialAccount, SocialPostTarget> */
    public function socialAccount(): BelongsTo
    {
        return $this->belongsTo(SocialAccount::class);
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(SocialPublishAttempt::class);
    }

    /** Whether this target can be retried. Max 3 attempts. */
    public function canRetry(): bool
    {
        return $this->status === self::STATUS_FAILED && $this->attempt_count < 3;
    }

    public function scopePending($query)
    {
        return $query->where('status', self::STATUS_PENDING);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }
}
