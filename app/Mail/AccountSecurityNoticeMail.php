<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountSecurityNoticeMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  User    $user
     * @param  string  $type  'password_reset_requested' | 'google_login_required'
     */
    public function __construct(
        public User $user,
        public string $type,
    ) {}

    public function build(): static
    {
        $subject = match ($this->type) {
            'google_login_required' => 'Account access notice',
            default                 => 'Security notice: password reset requested',
        };

        return $this->subject($subject)
            ->view('emails.account-security-notice');
    }
}
