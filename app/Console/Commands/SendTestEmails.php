<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\EmailTrackingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:test {--count=10} {--to=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send test emails with tracking for deliverability testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $count = (int) $this->option('count');
        $recipient = $this->option('to');

        // If no recipient specified, use first admin user
        if (! $recipient) {
            $admin = User::where('role', 'administrator')->first();
            if (! $admin) {
                $admin = User::first();
            }

            if (! $admin) {
                $this->error('No users found in database. Please specify --to=email@example.com');

                return 1;
            }

            $recipient = $admin->email;
        }

        // Validate email
        if (! filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            $this->error("Invalid email address: {$recipient}");

            return 1;
        }

        $this->info("🚀 Sending {$count} test emails to: {$recipient}");
        $this->newLine();

        $trackingService = app(EmailTrackingService::class);
        $successCount = 0;
        $failedCount = 0;

        $progressBar = $this->output->createProgressBar($count);
        $progressBar->start();

        for ($i = 1; $i <= $count; $i++) {
            try {
                $subject = "Test Email #{$i} - ".now()->format('H:i:s');
                $timestamp = now()->format('Y-m-d H:i:s');

                $htmlMessage = "
                    <html>
                    <body style='font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5;'>
                        <div style='max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'>
                            <h1 style='color: #2563eb; margin: 0 0 20px 0;'>📧 Test Email #{$i}</h1>
                            <p style='color: #374151; font-size: 16px; line-height: 1.6;'>
                                This is a test email sent at <strong>{$timestamp}</strong>
                            </p>
                            <div style='background: #eff6ff; padding: 15px; border-radius: 6px; margin: 20px 0;'>
                                <p style='margin: 0; color: #1e40af;'>
                                    <strong>Test Details:</strong><br>
                                    Email: {$i} of {$count}<br>
                                    Environment: ".app()->environment().'<br>
                                    Mailer: '.config('mail.default')."
                                </p>
                            </div>
                            <p style='color: #374151;'>
                                This email includes tracking to test deliverability monitoring:
                            </p>
                            <ul style='color: #6b7280;'>
                                <li>Open tracking pixel (automatically tracked when images load)</li>
                                <li>Click tracking on all links below</li>
                            </ul>
                            
                            <div style='margin: 30px 0;'>
                                <h3 style='color: #374151; margin-bottom: 15px;'>Test Click Tracking:</h3>
                                
                                <div style='margin-bottom: 15px;'>
                                    <a href='https://example.com/primary-test-link-{$i}' 
                                       style='display: inline-block; background: #2563eb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold;'>
                                        🔗 Primary Test Link
                                    </a>
                                </div>
                                
                                <div style='margin-bottom: 15px;'>
                                    <a href='https://github.com/test-repo/issues/{$i}' 
                                       style='display: inline-block; background: #059669; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px;'>
                                        🐙 GitHub Link Test
                                    </a>
                                </div>
                                
                                <div style='margin-bottom: 15px;'>
                                    <a href='https://docs.example.com/page/{$i}' 
                                       style='display: inline-block; background: #7c3aed; color: white; padding: 10px 20px; text-decoration: none; border-radius: 6px;'>
                                        📚 Documentation Link
                                    </a>
                                </div>
                                
                                <p style='color: #6b7280; font-size: 14px; margin-top: 20px;'>
                                    Try clicking each link above. Each click should be tracked separately in the deliverability dashboard.
                                </p>
                            </div>
                            
                            <div style='background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0;'>
                                <p style='margin: 0; color: #92400e; font-size: 14px;'>
                                    <strong>⚠️ Note:</strong> These are test links. Clicking them will increment the click counter 
                                    and redirect to the destination URL.
                                </p>
                            </div>
                            
                            <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;'>
                            <p style='color: #9ca3af; font-size: 14px; margin: 0;'>
                                Test email #{$i} sent by CLIENTBRIDGE Email Testing System
                            </p>
                        </div>
                    </body>
                    </html>
                ";

                // Create email log with tracking
                $emailLog = $trackingService->createLog([
                    'user_id' => auth()->id(),
                    'to_email' => $recipient,
                    'subject' => $subject,
                    'body' => $htmlMessage,
                ]);

                if (! $emailLog) {
                    throw new \Exception('Failed to create email log');
                }

                // Add tracking pixel and link tracking
                $trackedHtml = $trackingService->addTrackingPixel($htmlMessage, $emailLog->tracking_id);
                $trackedHtml = $trackingService->addLinkTracking($trackedHtml, $emailLog->tracking_id);

                // Send email
                Mail::html($trackedHtml, function ($msg) use ($recipient, $subject) {
                    $msg->to($recipient)->subject($subject);
                });

                // Mark as sent
                $trackingService->markAsSent($emailLog->tracking_id);

                $successCount++;

                // Small delay to avoid rate limiting
                if ($i < $count) {
                    usleep(100000); // 100ms delay
                }

            } catch (\Exception $e) {
                $failedCount++;

                if (isset($emailLog) && $emailLog) {
                    $trackingService->markAsFailed($emailLog->tracking_id, $e->getMessage());
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Display results
        $this->info("✅ Successfully sent: {$successCount} emails");

        if ($failedCount > 0) {
            $this->error("❌ Failed to send: {$failedCount} emails");
        }

        $this->newLine();

        // Show tracking info
        $this->info('📊 Tracking Information:');
        $this->comment('   • Each email has 3 clickable links');
        $this->comment('   • Total trackable clicks: '.($successCount * 3));
        $this->comment('   • Open tracking pixel included in all emails');

        $this->newLine();
        $this->info('🔍 View deliverability dashboard:');
        $this->comment('   Visit: '.config('app.url').'/admin/email-logs');
        $this->newLine();
        $this->comment('💡 Tip: Open the debug panel to see tracking URLs and statistics');

        return 0;
    }
}
