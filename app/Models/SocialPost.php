<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class SocialPost extends Model
{
    use HasFactory;

    const STATUS_DRAFT = 'draft';

    const STATUS_SCHEDULED = 'scheduled';

    const STATUS_PUBLISHING = 'publishing';

    const STATUS_PUBLISHED = 'published';

    const STATUS_FAILED = 'failed';

    const STATUS_CANCELLED = 'cancelled';

    const ALL_STATUSES = [
        self::STATUS_DRAFT,
        self::STATUS_SCHEDULED,
        self::STATUS_PUBLISHING,
        self::STATUS_PUBLISHED,
        self::STATUS_FAILED,
        self::STATUS_CANCELLED,
    ];

    protected $fillable = [
        'user_id',
        'caption',
        'status',
        'scheduled_at',
        'published_at',
        'source_type',
        'source_id',
        'requires_approval',
        'approved_at',
        'approved_by',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
            'approved_at' => 'datetime',
            'requires_approval' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /** The originating record (CmsImage, ClientFile, InspectionReport). */
    public function source(): MorphTo
    {
        return $this->morphTo('source');
    }

    /** @return HasMany<SocialPostTarget, SocialPost> */
    public function targets(): HasMany
    {
        return $this->hasMany(SocialPostTarget::class);
    }

    /** @return HasMany<SocialPostMedia, SocialPost> */
    public function media(): HasMany
    {
        return $this->hasMany(SocialPostMedia::class)->orderBy('sort_order');
    }

    public function attempts(): \Illuminate\Database\Eloquent\Relations\HasManyThrough
    {
        return $this->hasManyThrough(SocialPublishAttempt::class, SocialPostTarget::class);
    }

    /** Whether the post is ready to be dispatched to the queue. */
    public function isReadyToPublish(): bool
    {
        if ($this->status !== self::STATUS_SCHEDULED) {
            return false;
        }

        if ($this->requires_approval && ! $this->approved_at) {
            return false;
        }

        // @phpstan-ignore method.nonObject
        return $this->scheduled_at === null || $this->scheduled_at->lte(now());
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function scopeDue($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED)
            ->where(function ($q) {
                $q->whereNull('scheduled_at')->orWhere('scheduled_at', '<=', now());
            });
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }
}
