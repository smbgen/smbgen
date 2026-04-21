<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SocialPublishAttempt extends Model
{
    const STATUS_PUBLISHED = 'published';

    const STATUS_FAILED = 'failed';

    protected $fillable = [
        'social_post_target_id',
        'status',
        'platform',
        'response_body',
        'error_code',
        'error_message',
        'idempotency_key',
        'attempted_at',
    ];

    protected function casts(): array
    {
        return [
            'attempted_at' => 'datetime',
        ];
    }

    public function target(): BelongsTo
    {
        return $this->belongsTo(SocialPostTarget::class, 'social_post_target_id');
    }

    public function wasSuccessful(): bool
    {
        return $this->status === self::STATUS_PUBLISHED;
    }
}
