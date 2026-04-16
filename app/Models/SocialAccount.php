<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SocialAccount extends Model
{
    use HasFactory;

    /** Supported platform identifiers. */
    const PLATFORM_FACEBOOK = 'facebook';

    const PLATFORM_INSTAGRAM = 'instagram';

    const PLATFORM_LINKEDIN = 'linkedin';

    const STATUS_CONNECTED = 'connected';

    const STATUS_ERROR = 'error';

    const STATUS_REVOKED = 'revoked';

    protected $fillable = [
        'user_id',
        'platform',
        'account_name',
        'account_url',
        'credentials',
        'active',
        'access_token',
        'refresh_token',
        'token_expires_at',
        'platform_user_id',
        'platform_page_id',
        'platform_page_name',
        'page_access_token',
        'scopes',
        'connection_status',
        'last_error',
        'last_used_at',
    ];

    protected $hidden = [
        'access_token',
        'refresh_token',
        'page_access_token',
        'credentials',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
            'token_expires_at' => 'datetime',
            'last_used_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function targets(): HasMany
    {
        return $this->hasMany(SocialPostTarget::class);
    }

    /** Whether the OAuth token appears valid (not expired and not revoked). */
    public function isConnected(): bool
    {
        return $this->active
            && $this->connection_status === self::STATUS_CONNECTED
            && ! $this->isTokenExpired();
    }

    /** Whether the stored access token is past its expiry. */
    public function isTokenExpired(): bool
    {
        if (! $this->token_expires_at) {
            return false; // long-lived tokens (Meta pages) have no expiry stored
        }

        return $this->token_expires_at->isPast();
    }

    /** Mark the account as having an error. */
    public function markError(string $message): void
    {
        $this->update([
            'connection_status' => self::STATUS_ERROR,
            'last_error' => $message,
        ]);
    }

    /** Returns display label for the platform. */
    public function platformLabel(): string
    {
        return match ($this->platform) {
            self::PLATFORM_FACEBOOK => 'Facebook',
            self::PLATFORM_INSTAGRAM => 'Instagram',
            self::PLATFORM_LINKEDIN => 'LinkedIn',
            default => ucfirst($this->platform),
        };
    }

    /** FontAwesome icon class for the platform. */
    public function platformIcon(): string
    {
        return match ($this->platform) {
            self::PLATFORM_FACEBOOK => 'fab fa-facebook',
            self::PLATFORM_INSTAGRAM => 'fab fa-instagram',
            self::PLATFORM_LINKEDIN => 'fab fa-linkedin',
            default => 'fas fa-share-alt',
        };
    }
}
