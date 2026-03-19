<?php

namespace App\Models;

use App\Enums\ManagedSiteStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Stancl\Tenancy\Database\Concerns\BelongsToTenant;

class ManagedSite extends Model
{
    /** @use HasFactory<\Database\Factories\ManagedSiteFactory> */
    use BelongsToTenant, HasFactory;

    protected $fillable = [
        'client_id',
        'agency_portal_id',
        'name',
        'domain',
        'status',
        'notes',
        'launched_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => ManagedSiteStatus::class,
            'launched_at' => 'datetime',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function agencyPortal(): BelongsTo
    {
        return $this->belongsTo(AgencyPortal::class);
    }
}
