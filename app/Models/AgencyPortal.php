<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AgencyPortal extends Model
{
    /** @use HasFactory<\Database\Factories\AgencyPortalFactory> */
    use HasFactory;

    protected $fillable = [
        'owner_user_id',
        'name',
        'slug',
        'status',
        'max_client_sites',
        'branding',
    ];

    protected function casts(): array
    {
        return [
            'branding' => 'array',
            'max_client_sites' => 'integer',
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_user_id');
    }

    public function sites(): HasMany
    {
        return $this->hasMany(ManagedSite::class);
    }
}
