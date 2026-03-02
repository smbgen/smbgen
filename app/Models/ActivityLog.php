<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'model_type',
        'model_id',
        'properties',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'properties' => 'array',
    ];

    /**
     * Get the user who performed the activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that the activity was performed on
     */
    public function subject(): MorphTo
    {
        return $this->morphTo('model');
    }

    /**
     * Scope to filter by action type
     */
    public function scopeOfAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate = null)
    {
        $query->whereDate('created_at', '>=', $startDate);

        if ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }

        return $query;
    }

    /**
     * Get formatted action name for display
     */
    public function getFormattedActionAttribute(): string
    {
        return ucwords(str_replace('_', ' ', $this->action));
    }

    /**
     * Get icon for action type
     */
    public function getActionIconAttribute(): string
    {
        return match ($this->action) {
            'login' => '🔓',
            'login_google' => '🔓',
            'logout' => '🔒',
            'file_upload' => '📤',
            'file_download' => '📥',
            'file_delete' => '🗑️',
            'client_create' => '➕',
            'client_update' => '✏️',
            'client_delete' => '❌',
            'account_provisioned' => '📧',
            'account_activated' => '✅',
            'message_send' => '✉️',
            'booking_create' => '📅',
            'booking_update' => '📝',
            'profile_update' => '⚙️',
            'password_change' => '🔑',
            default => 'ℹ️',
        };
    }

    /**
     * Get color class for action type
     */
    public function getActionColorAttribute(): string
    {
        return match ($this->action) {
            'login', 'login_google' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'logout' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
            'file_upload' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            'file_download' => 'bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-300',
            'file_delete', 'client_delete' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            'client_create', 'booking_create' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            'client_update', 'booking_update', 'profile_update' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            'account_provisioned' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-300',
            'account_activated' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-300',
            'message_send' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
            'password_change' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
        };
    }
}
