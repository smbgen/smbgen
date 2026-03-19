<?php

namespace App\Models;

use App\Enums\SocialPlatform;
use App\Enums\SocialPostStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class SocialPost extends Model
{
    /** @use HasFactory<\Database\Factories\SocialPostFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'client_id',
        'user_id',
        'platform',
        'content',
        'status',
        'ai_generated',
        'scheduled_at',
        'published_at',
        'failure_reason',
    ];

    protected function casts(): array
    {
        return [
            'platform' => SocialPlatform::class,
            'status' => SocialPostStatus::class,
            'ai_generated' => 'boolean',
            'scheduled_at' => 'datetime',
            'published_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
