<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'client_name',
        'client_phone',
        'client_email',
        'client_address',
        'consult_date',
        'summary_title',
        'body_explanation',
        'body_suggested_actions',
        'created_by',
        'google_drive_file_id',
        'google_drive_link',
    ];

    protected $casts = [
        'consult_date' => 'datetime',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
