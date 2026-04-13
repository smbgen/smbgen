<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BookingCancellation extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Booking $booking,
        public string $recipientType, // 'customer' or 'staff'
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $date = $this->booking->booking_date?->format('M j, Y') ?? 'TBD';
        $time = is_string($this->booking->booking_time)
            ? \Carbon\Carbon::parse($this->booking->booking_time)->format('g:i A')
            : $this->booking->booking_time?->format('g:i A') ?? 'TBD';

        $replyTo = config('business.contact.email');

        $envelope = new Envelope(
            subject: 'Appointment Cancelled - '.$date.' at '.$time,
        );

        // Add reply-to if business contact email is configured
        if ($replyTo) {
            $envelope->replyTo($replyTo);
        }

        return $envelope;
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.booking-cancellation',
            with: [
                'booking' => $this->booking,
                'recipientType' => $this->recipientType,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
