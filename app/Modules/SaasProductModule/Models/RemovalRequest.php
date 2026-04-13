<?php

namespace App\Modules\SaasProductModule\Models;

use App\Modules\SaasProductModule\Enums\RemovalStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RemovalRequest extends Model
{
    protected $table = 'saasproductmodule_removal_requests';

    protected $fillable = [
        'profile_id',
        'data_broker_id',
        'status',
        'submitted_at',
        'confirmed_at',
        'notes',
    ];

    protected $casts = [
        'status'       => RemovalStatus::class,
        'submitted_at' => 'datetime',
        'confirmed_at' => 'datetime',
    ];

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Profile::class);
    }

    public function dataBroker(): BelongsTo
    {
        return $this->belongsTo(DataBroker::class);
    }
}
