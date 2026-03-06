<?php

namespace App\Console\Commands;

use App\Models\EmailLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class AuditEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:audit {id : Email log ID} 
                            {--preview : Show email preview instead of sending}
                            {--send-to= : Send a test copy to this email address}
                            {--raw : Show raw HTML}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit an email log - preview content or send test copy';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $emailId = $this->argument('id');
        $preview = $this->option('preview');
        $sendTo = $this->option('send-to');
        $showRaw = $this->option('raw');

        // Find the email log
        $emailLog = EmailLog::with(['user', 'booking'])->find($emailId);

        if (! $emailLog) {
            $this->error("❌ Email log #{$emailId} not found.");

            return 1;
        }

        $this->info("📧 Email Log #{$emailLog->id}");
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        // Display email metadata
        $this->table(
            ['Field', 'Value'],
            [
                ['Status', $this->getStatusIcon($emailLog->status).' '.ucfirst($emailLog->status)],
                ['To', $emailLog->to_email],
                ['CC', $emailLog->cc_email ?? '—'],
                ['Subject', $emailLog->subject],
                ['Sent At', $emailLog->sent_at ? $emailLog->sent_at->format('M j, Y g:i A T') : 'Not sent'],
                ['Delivered At', $emailLog->delivered_at ? $emailLog->delivered_at->format('M j, Y g:i A T') : '—'],
                ['Opened At', $emailLog->opened_at ? $emailLog->opened_at->format('M j, Y g:i A T') : '—'],
                ['Opens', $emailLog->open_count],
                ['Clicks', $emailLog->click_count],
                ['Tracking ID', $emailLog->tracking_id],
            ]
        );

        $this->newLine();

        // Show raw HTML if requested
        if ($showRaw) {
            $this->line('Raw HTML:');
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->line($emailLog->body);
            $this->newLine();

            return 0;
        }

        // Preview mode - show text-only version
        if ($preview) {
            $this->info('📄 Email Preview (Text-Only):');
            $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
            $this->newLine();

            // Strip HTML tags for preview
            $textContent = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $emailLog->body));
            $this->line($textContent);
            $this->newLine();

            $this->comment('💡 Tip: Use --raw to see the HTML source');
            $this->comment('💡 Tip: Use --send-to=your@email.com to send a test copy');

            return 0;
        }

        // Send test copy if requested
        if ($sendTo) {
            if (! filter_var($sendTo, FILTER_VALIDATE_EMAIL)) {
                $this->error("Invalid email address: {$sendTo}");

                return 1;
            }

            $confirm = $this->confirm("Send a test copy of this email to {$sendTo}?", true);

            if (! $confirm) {
                $this->info('Cancelled.');

                return 0;
            }

            try {
                $this->info('📤 Sending test email...');

                $testSubject = '[TEST COPY] '.$emailLog->subject;
                $testBody = "
                    <div style='background: #fef3c7; border-left: 4px solid #f59e0b; padding: 16px; margin: 20px 0; border-radius: 4px;'>
                        <p style='margin: 0; color: #92400e; font-weight: 600;'>
                            ⚠️ This is a TEST COPY of email log #{$emailLog->id}
                        </p>
                        <p style='margin: 8px 0 0 0; color: #92400e; font-size: 14px;'>
                            Original recipient: {$emailLog->to_email}<br>
                            Original sent: ".($emailLog->sent_at ? $emailLog->sent_at->format('M j, Y g:i A T') : 'Not sent').'
                        </p>
                    </div>
                    '.$emailLog->body;

                Mail::html($testBody, function ($message) use ($sendTo, $testSubject) {
                    $message->to($sendTo)
                        ->subject($testSubject);
                });

                $this->info("✅ Test email sent successfully to {$sendTo}");
                $this->newLine();
                $this->comment('Check your inbox for the test email.');

                return 0;

            } catch (\Exception $e) {
                $this->error('❌ Failed to send test email: '.$e->getMessage());

                return 1;
            }
        }

        // Default: show basic preview
        $this->info('📄 Email Preview:');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->newLine();

        $textContent = strip_tags(str_replace(['<br>', '<br/>', '<br />'], "\n", $emailLog->body));
        $this->line(substr($textContent, 0, 500));

        if (strlen($textContent) > 500) {
            $this->newLine();
            $this->comment('... (truncated)');
        }

        $this->newLine();
        $this->comment('💡 Options:');
        $this->line('  --preview          Show full text preview');
        $this->line('  --raw              Show raw HTML');
        $this->line('  --send-to=EMAIL    Send test copy to email address');

        return 0;
    }

    private function getStatusIcon(string $status): string
    {
        return match ($status) {
            'sent' => '📤',
            'delivered' => '✅',
            'opened' => '👀',
            'clicked' => '🖱️',
            'failed' => '❌',
            'bounced' => '⚠️',
            default => '📧',
        };
    }
}
