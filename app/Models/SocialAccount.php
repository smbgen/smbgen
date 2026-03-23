<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialAccount extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'platform',
        'account_name',
        'account_url',
        'page_id',
        'page_name',
        'credentials',
        'access_token_expires_at',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
        'credentials' => 'encrypted:array',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function socialPosts()
    {
        return $this->hasMany(\App\Models\SocialPost::class);
    }

    public function isTokenExpired(): bool
    {
        if (! $this->access_token_expires_at) {
            return false;
        }

        return now()->isAfter($this->access_token_expires_at);
    }

    public function getAccessToken(): ?string
    {
        return $this->credentials['access_token'] ?? null;
    }

    public function scopeLinkedIn($query)
    {
        return $query->where('platform', 'linkedin');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}

