<?php

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSent;

class LogSentEmail
{
    /**
     * Handle the event - logs ALL emails sent through Laravel's mail system.
     *
     * This listener runs AFTER emails are sent (MessageSent event).
     * Combined with AddEmailTracking listener which runs BEFORE sending,
     * every email automatically gets a tracking ID, pixel, and link tracking.
     *
     * Tracked emails include:
     * - All Mailables (InvoiceMailable, ClientPortalAccessMail, etc.)
     * - Laravel notifications (password resets, email verification)
     * - Form submission emails
     * - Manual Mail::send() calls
     * - Email composer emails
     */
    public function handle(MessageSent $event): void
    {
        try {
            $message = $event->message;

            // Extract recipients - Symfony email format
            $toAddresses = $message->getTo();
            $to = null;

            if (! empty($toAddresses)) {
                // Get first recipient email address
                $firstRecipient = $toAddresses[0] ?? null;
                if ($firstRecipient) {
                    $to = $firstRecipient->getAddress();
                }
            }

            // Extract CC recipients
            $ccAddresses = $message->getCc();
            $cc = null;
            if (! empty($ccAddresses)) {
                $cc = collect($ccAddresses)->map(fn ($addr) => $addr->getAddress())->implode(', ');
            }

            // Get subject and body
            $subject = $message->getSubject();
            $body = $message->getHtmlBody() ?: $message->getTextBody();

            // Extract tracking ID from header (set by AddEmailTracking listener)
            $trackingId = null;
            $headers = $message->getHeaders();
            if ($headers->has('X-Tracking-ID')) {
                $trackingId = $headers->get('X-Tracking-ID')->getBodyAsString();
            }

            // Fallback: check if tracking ID already exists in body
            if (! $trackingId && preg_match('/data-tracking-id="([a-f0-9-]{36})"/', $body, $matches)) {
                $trackingId = $matches[1];
            }

            // Create log entry
            EmailLog::create([
                'user_id' => auth()->id(),
                'to_email' => $to,
                'cc_email' => $cc,
                'subject' => $subject,
                'body' => $body,
                'status' => 'sent',
                'sent_at' => now(),
                'tracking_id' => $trackingId,
            ]);
        } catch (\Exception $e) {
            // Log error but don't break email sending
            \Log::error('Failed to log sent email', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}
