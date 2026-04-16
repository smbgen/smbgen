<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use Billable, HasFactory, Notifiable;

    // Role constants
    const ROLE_USER = 'user';

    const ROLE_CLIENT = 'client';

    const ROLE_ADMINISTRATOR = 'company_administrator';

    const ROLE_ADMINISTRATOR_LEGACY = 'administrator';

    const ROLE_TENANT_ADMIN = 'tenant_admin';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'google_id',
        'role',
        'is_super_admin',
        'notify_on_new_leads',
        'notify_on_new_bookings',
        'trial_ends_at',
        'account_tier',
        'enabled_services',
        // Note: google_refresh_token and google_calendar_id removed - now using GoogleCredential table
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_super_admin' => 'boolean',
            'trial_ends_at' => 'datetime',
            'enabled_services' => 'array',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the social accounts associated with the user.
     */
    public function socialAccounts(): HasMany
    {
        return $this->hasMany(\App\Models\SocialAccount::class);
    }

    /**
     * Tenant associated with this user in multi-tenant mode.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    /**
     * Get the client record associated with this user (for client users)
     */
    public function client(): HasOne
    {
        return $this->hasOne(\App\Models\Client::class, 'email', 'email');
    }

    /**
     * Google calendar credentials for this user (if connected)
     */
    public function googleCredential(): HasOne
    {
        return $this->hasOne(\App\Models\GoogleCredential::class);
    }

    /**
     * Ensure this user has an associated client record.
     */
    public function ensureClientRecord(): Client
    {
        return Client::updateOrCreate(
            ['email' => $this->email],
            [
                'name' => $this->name,
                'is_active' => true,
                'user_provisioned_at' => now(),
            ]
        );
    }

    /**
     * Check if user has Google Calendar connected (checks both new and legacy)
     */
    public function hasGoogleCalendar(): bool
    {
        // Check new google_credentials table first
        if ($this->googleCredential && $this->googleCredential->refresh_token) {
            return true;
        }

        // Fallback to legacy columns on users table
        return ! empty($this->google_refresh_token);
    }

    /**
     * Get the refresh token from either new or legacy location
     */
    public function getGoogleRefreshToken(): ?string
    {
        // Try new table first
        if ($this->googleCredential && $this->googleCredential->refresh_token) {
            return $this->googleCredential->refresh_token;
        }

        // Fallback to legacy column
        return $this->google_refresh_token;
    }

    /**
     * Get calendar ID from either new or legacy location
     */
    public function getGoogleCalendarId(): string
    {
        // Try new table first
        if ($this->googleCredential && $this->googleCredential->calendar_id) {
            return $this->googleCredential->calendar_id;
        }

        // Fallback to legacy column
        return $this->google_calendar_id ?? 'primary';
    }

    /**
     * Check if user is an administrator (company_administrator, legacy administrator, or tenant_admin).
     */
    public function isAdministrator(): bool
    {
        return in_array($this->role, [
            self::ROLE_ADMINISTRATOR,
            self::ROLE_ADMINISTRATOR_LEGACY,
            self::ROLE_TENANT_ADMIN,
        ], true);
    }

    /**
     * Check if user is a platform-level super administrator (company_administrator or legacy administrator).
     * Super admins have cross-tenant visibility; the access restriction itself is enforced
     * by the {@see \App\Http\Middleware\EnsureTenantUserMatchesContext} middleware.
     */
    public function isSuperAdmin(): bool
    {
        return (bool) $this->is_super_admin;
    }

    /**
     * Check if user is a tenant admin (self-serve business owner).
     */
    public function isTenantAdmin(): bool
    {
        return $this->role === self::ROLE_TENANT_ADMIN;
    }

    /**
     * Check if user is a client
     */
    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    /**
     * Check if user is a regular user
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Get the availability rules for this user
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(\App\Models\Availability::class);
    }

    /**
     * Get the bookings assigned to this staff member
     */
    public function staffBookings(): HasMany
    {
        return $this->hasMany(\App\Models\Booking::class, 'staff_id');
    }

    /**
     * Get the files uploaded by this user
     */
    public function uploadedFiles(): HasMany
    {
        return $this->hasMany(\App\Models\ClientFile::class, 'user_id');
    }

    /**
     * Get the blog posts authored by this user
     */
    public function blogPosts(): HasMany
    {
        return $this->hasMany(\App\Models\BlogPost::class, 'author_id');
    }
}
