<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendResendTestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'resend:test-email {email?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email using Resend API';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email') ?? 'alex@smbgen.com';

        $this->info('Sending test email via Resend to: '.$email);

        // Temporarily set the mailer to resend for this test
        config(['mail.default' => 'resend']);

        try {
            Mail::html(
                '<p>Congrats on setting up <strong>Resend</strong> with Laravel!</p><p>This email was sent using the Resend API.</p>',
                function ($message) use ($email) {
                    $message->to($email)
                        ->subject('Hello from Resend + Laravel!')
                        ->from(config('mail.from.address', 'hello@example.com'), config('mail.from.name', 'Laravel App'));
                }
            );

            $this->info('✅ Test email sent successfully!');
        } catch (\Exception $e) {
            $this->error('❌ Failed to send email: '.$e->getMessage());

            return 1;
        }

        return 0;
    }
}
