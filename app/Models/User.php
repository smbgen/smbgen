<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    // Role constants
    const ROLE_USER = 'user';

    const ROLE_CLIENT = 'client';

    const ROLE_ADMINISTRATOR = 'company_administrator';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'role',
        'notify_on_new_leads',
        'notify_on_new_bookings',
        'tenant_id',
        'is_super_admin',
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
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    /**
     * Get the tenant this user belongs to
     */
    public function tenant()
    {
        return $this->belongsTo(\Stancl\Tenancy\Database\Models\Tenant::class);
    }

    /**
     * Check if user is a super admin
     */
    public function isSuperAdmin(): bool
    {
        return $this->is_super_admin === true;
    }

    /**
     * Get the social accounts associated with the user.
     */
    public function socialAccounts()
    {
        return $this->hasMany(\App\Models\SocialAccount::class);
    }

    /**
     * Get the client record associated with this user (for client users)
     */
    public function client()
    {
        return $this->hasOne(\App\Models\Client::class, 'email', 'email');
    }

    /**
     * Google calendar credentials for this user (if connected)
     */
    public function googleCredential()
    {
        return $this->hasOne(\App\Models\GoogleCredential::class);
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
     * Check if user is an administrator
     */
    public function isAdministrator(): bool
    {
        return $this->role === 'company_administrator';
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
    public function availabilities()
    {
        return $this->hasMany(\App\Models\Availability::class);
    }

    /**
     * Get the bookings assigned to this staff member
     */
    public function staffBookings()
    {
        return $this->hasMany(\App\Models\Booking::class, 'staff_id');
    }

    /**
     * Get the files uploaded by this user
     */
    public function uploadedFiles()
    {
        return $this->hasMany(\App\Models\ClientFile::class, 'user_id');
    }

    /**
     * Get the blog posts authored by this user
     */
    public function blogPosts()
    {
        return $this->hasMany(\App\Models\BlogPost::class, 'author_id');
    }
}
