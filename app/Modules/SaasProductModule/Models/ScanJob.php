<?php

namespace App\Modules\SaasProductModule\Models;

use App\Modules\SaasProductModule\Enums\ScanStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScanJob extends Model
{
    protected $table = 'saasproductmodule_scan_jobs';

    protected $fillable = [
        'profile_id',
        'data_broker_id',
        'status',
        'result',
        'listings_found',
        'scanned_at',
    ];

    protected $casts = [
        'status'        => ScanStatus::class,
        'result'        => 'array',
        'listings_found' => 'integer',
        'scanned_at'    => 'datetime',
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
