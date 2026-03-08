<?php

namespace App\Modules\CleanSlate\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DataBroker extends Model
{
    protected $table = 'cleanslate_data_brokers';

    protected $fillable = [
        'name',
        'domain',
        'opt_out_method',
        'opt_out_url',
        'min_tier',
        'active',
    ];

    protected $casts = [
        'active'   => 'boolean',
        'min_tier' => 'integer',
    ];

    public function scanJobs(): HasMany
    {
        return $this->hasMany(ScanJob::class);
    }

    public function removalRequests(): HasMany
    {
        return $this->hasMany(RemovalRequest::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeForTier($query, int $tier)
    {
        return $query->where('min_tier', '<=', $tier);
    }
}
