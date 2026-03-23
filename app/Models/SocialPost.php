<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialPost extends Model
{
    use HasFactory;
    protected $fillable = [
        'social_account_id',
        'user_id',
        'title',
        'content',
        'media_paths',
        'status',
        'scheduled_at',
        'published_at',
        'linkedin_post_id',
        'error_message',
    ];

    protected $casts = [
        'media_paths' => 'array',
        'scheduled_at' => 'datetime',
        'published_at' => 'datetime',
    ];

    const STATUS_DRAFT = 'draft';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_PUBLISHED = 'published';
    const STATUS_FAILED = 'failed';

    public function socialAccount()
    {
        return $this->belongsTo(SocialAccount::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function scopePublished($query)
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    public function scopeFailed($query)
    {
        return $query->where('status', self::STATUS_FAILED);
    }

    public function scopeDueForPublishing($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED)
            ->where('scheduled_at', '<=', now());
    }

    // Helpers
    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    public function isPublished(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }

    public function isFailed(): bool
    {
        return $this->status === self::STATUS_FAILED;
    }

    public function getStatusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_DRAFT => 'bg-gray-500',
            self::STATUS_SCHEDULED => 'bg-blue-500',
            self::STATUS_PUBLISHED => 'bg-green-500',
            self::STATUS_FAILED => 'bg-red-500',
            default => 'bg-gray-500',
        };
    }

    public function getCharacterCount(): int
    {
        return mb_strlen($this->content ?? '');
    }
}
