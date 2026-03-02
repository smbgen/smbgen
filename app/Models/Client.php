<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'property_address',
        'notes',
        'message',
        'source_site',
        'notification_email',
        'stripe_customer_id',
        'is_active',
        'user_provisioned_at',
        'account_activated_at',
        'last_login_at',
        'google_id',
        'google_linked_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'user_provisioned_at' => 'datetime',
        'account_activated_at' => 'datetime',
        'last_login_at' => 'datetime',
        'google_linked_at' => 'datetime',
    ];

    /**
     * Get the user account associated with this client record
     */
    public function user()
    {
        return $this->hasOne(\App\Models\User::class, 'email', 'email');
    }

    /**
     * Client files uploaded for this client
     */
    public function files()
    {
        return $this->hasMany(\App\Models\ClientFile::class);
    }

    /**
     * Check if client's user account has been provisioned
     */
    public function isProvisioned(): bool
    {
        return $this->user_provisioned_at !== null;
    }

    /**
     * Check if client has activated their account (logged in at least once)
     */
    public function isActivated(): bool
    {
        return $this->account_activated_at !== null;
    }

    /**
     * Get provisioning status for display
     */
    public function getProvisioningStatusAttribute(): string
    {
        if (! $this->is_active) {
            return 'inactive';
        }

        if (! $this->isProvisioned()) {
            return 'not_provisioned';
        }

        if (! $this->isActivated()) {
            return 'provisioned_not_activated';
        }

        return 'active';
    }

    /**
     * Get provisioning status label for display
     */
    public function getProvisioningStatusLabelAttribute(): string
    {
        return match ($this->provisioning_status) {
            'inactive' => 'Inactive',
            'not_provisioned' => 'Not Provisioned',
            'provisioned_not_activated' => 'Provisioned',
            'active' => 'Active',
            default => 'Unknown',
        };
    }

    /**
     * Check if client has Google ID linked
     */
    public function hasGoogleLinked(): bool
    {
        return $this->google_id !== null && $this->google_linked_at !== null;
    }

    /**
     * Get provisioning status badge color for display
     */
    public function getProvisioningStatusBadgeAttribute(): string
    {
        return match ($this->provisioning_status) {
            'inactive' => 'bg-gray-100 text-gray-800',
            'not_provisioned' => 'bg-yellow-100 text-yellow-800',
            'provisioned_not_activated' => 'bg-blue-100 text-blue-800',
            'active' => 'bg-green-100 text-green-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }
}
