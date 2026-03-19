<?php

namespace App\Jobs;

use App\Models\EmailSequenceEnrollment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class ProcessEmailSequenceStepJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public function __construct(public readonly EmailSequenceEnrollment $enrollment)
    {
        $this->onQueue('relay');
    }

    public function handle(): void
    {
        $enrollment = $this->enrollment->fresh();

        if (! $enrollment || $enrollment->status !== 'active') {
            return;
        }

        $step = $enrollment->sequence->steps->get($enrollment->current_step);

        if (! $step) {
            $enrollment->update(['status' => 'completed', 'completed_at' => now()]);

            return;
        }

        // TODO: send step email via Mail::raw / Mailable
        // Mail::raw($step->body, fn ($m) => $m->to($enrollment->email)->subject($step->subject));

        $nextStep = $enrollment->current_step + 1;
        $hasMore = $enrollment->sequence->steps->has($nextStep);

        $enrollment->update(['current_step' => $nextStep]);

        if ($hasMore) {
            $nextStepModel = $enrollment->sequence->steps->get($nextStep);
            $delay = now()->addDays($nextStepModel->delay_days);

            self::dispatch($enrollment->fresh())->delay($delay);
        } else {
            $enrollment->update(['status' => 'completed', 'completed_at' => now()]);
        }
    }
}
