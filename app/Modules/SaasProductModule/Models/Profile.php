<?php

namespace App\Modules\SaasProductModule\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Profile extends Model
{
    protected $table = 'saasproductmodule_profiles';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'emails',
        'phones',
        'addresses',
        'onboarding_complete',
        'exposure_score',
    ];

    protected $casts = [
        'date_of_birth'       => 'date',
        'emails'              => 'array',
        'phones'              => 'array',
        'addresses'           => 'array',
        'onboarding_complete' => 'boolean',
        'exposure_score'      => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scanJobs(): HasMany
    {
        return $this->hasMany(ScanJob::class);
    }

    public function removalRequests(): HasMany
    {
        return $this->hasMany(RemovalRequest::class);
    }

    public function fullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
