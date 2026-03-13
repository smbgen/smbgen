<?php

namespace App\Modules\CleanSlate\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Generation extends Model
{
    protected $table = 'extreme_generations';

    protected $fillable = [
        'user_id', 'prompt', 'status', 'config',
        'file_count', 'test_count', 'zip_path', 'error_message',
    ];

    protected $casts = ['config' => 'array'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isComplete(): bool
    {
        return $this->status === 'complete';
    }
}
