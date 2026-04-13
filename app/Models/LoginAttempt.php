<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'email',
        'provider',
        'provider_user_id',
        'ip_address',
        'was_linked',
    ];

    protected $casts = [
        'was_linked' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
