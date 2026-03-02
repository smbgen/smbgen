<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Schema;

class AIGeneration extends Model
{
    protected $table = 'ai_generations';

    protected $fillable = [
        'user_id',
        'type',
        'prompt',
        'generated_content',
        'model',
        'input_tokens',
        'output_tokens',
        'total_tokens',
        'status',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'input_tokens' => 'integer',
            'output_tokens' => 'integer',
            'total_tokens' => 'integer',
        ];
    }

    /**
     * Get the user who requested this AI generation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Calculate total cost based on token usage.
     * Pricing for Claude 3.5 Sonnet (as of Dec 2024):
     * Input: $3 per million tokens
     * Output: $15 per million tokens
     */
    public function calculateCost(): float
    {
        if (! $this->input_tokens || ! $this->output_tokens) {
            return 0.0;
        }

        $inputCost = ($this->input_tokens / 1_000_000) * 3.00;
        $outputCost = ($this->output_tokens / 1_000_000) * 15.00;

        return round($inputCost + $outputCost, 6);
    }

    /**
     * Scope to get generations for a specific user.
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get generations of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get successful generations only.
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'success');
    }

    /**
     * Scope to get failed generations only.
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'error']);
    }

    /**
     * Get total tokens used in a time period.
     */
    public static function getTotalTokensUsed(int $userId, ?string $period = 'today'): int
    {
        if (! Schema::hasColumn('ai_generations', 'user_id')) {
            return 0;
        }

        $query = self::forUser($userId)->successful();

        if ($period === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($period === 'this_week') {
            $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($period === 'this_month') {
            $query->whereMonth('created_at', now()->month);
        }

        return $query->sum('total_tokens') ?? 0;
    }

    /**
     * Get generation count in a time period.
     */
    public static function getGenerationCount(int $userId, ?string $period = 'today'): int
    {
        if (! Schema::hasColumn('ai_generations', 'user_id')) {
            return 0;
        }

        $query = self::forUser($userId)->successful();

        if ($period === 'today') {
            $query->whereDate('created_at', today());
        } elseif ($period === 'hour') {
            $query->where('created_at', '>=', now()->subHour());
        }

        return $query->count();
    }
}
