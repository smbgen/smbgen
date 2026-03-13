<?php

namespace App\Modules\CleanSlate\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    protected $table = 'extreme_profiles';

    protected $fillable = ['user_id', 'onboarding_complete'];

    protected $casts = ['onboarding_complete' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
