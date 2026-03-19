<?php

namespace App\Jobs;

use App\Models\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ScoreLeadJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Client $client)
    {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        $score = 0;

        // Engagement signals
        if ($this->client->account_activated_at) {
            $score += 20;
        }
        if ($this->client->last_login_at && $this->client->last_login_at->diffInDays(now()) < 7) {
            $score += 15;
        }

        // Data completeness
        if ($this->client->phone) {
            $score += 10;
        }
        if ($this->client->property_address) {
            $score += 5;
        }

        // Deal activity
        $openDeals = $this->client->deals()->whereNotIn('stage', ['closed_won', 'closed_lost'])->count();
        $score += min($openDeals * 15, 30);

        // File activity
        $fileCount = $this->client->files()->count();
        $score += min($fileCount * 5, 20);

        $this->client->update(['lead_score' => min($score, 100)]);
    }
}
