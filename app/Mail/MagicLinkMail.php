<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MagicLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userName;

    public $linkUrl;

    public $expiresAt;

    public function __construct(string $userName, string $linkUrl, $expiresAt)
    {
        $this->userName = $userName;
        $this->linkUrl = $linkUrl;
        $this->expiresAt = $expiresAt;
    }

    public function build()
    {
        return $this->subject('Your Magic Login Link')
            ->view('emails.magic_link');
    }
}
