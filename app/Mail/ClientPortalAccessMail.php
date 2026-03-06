<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ClientPortalAccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $clientName;

    public $emailAddress;

    public $password;

    /**
     * Create a new message instance.
     */
    public function __construct(string $clientName, string $emailAddress, string $resetUrl)
    {
        $this->clientName = $clientName;
        $this->emailAddress = $emailAddress;
        $this->password = $resetUrl; // Keeping property name for backwards compat with view
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $companyName = config('business.company_name') ?: config('app.company_name', 'smbgen');

        return $this->subject('Account Setup for '.$companyName)
            ->view('emails.client_portal_access');
    }
}
