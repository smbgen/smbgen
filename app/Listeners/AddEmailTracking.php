<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Str;

class AddEmailTracking
{
    /**
     * Add tracking pixel and link tracking to ALL outgoing emails automatically.
     *
     * This listener runs BEFORE emails are sent (MessageSending event).
     * It injects a unique tracking ID into the email, adds a tracking pixel,
     * and wraps all links with click tracking—automatically for every email.
     *
     * No need to manually add tracking in individual Mailables or Mail::send() calls.
     */
    public function handle(MessageSending $event): void
    {
        try {
            $message = $event->message;
            $htmlBody = $message->getHtmlBody();

            // Only track HTML emails
            if (! $htmlBody) {
                return;
            }

            // Generate a unique tracking ID if not already present
            $trackingId = null;
            if (preg_match('/data-tracking-id="([a-f0-9-]{36})"/', $htmlBody, $matches)) {
                $trackingId = $matches[1];
            } else {
                $trackingId = Str::uuid()->toString();
            }

            // Store tracking ID in message for LogSentEmail listener to pick up
            $message->getHeaders()->addTextHeader('X-Tracking-ID', $trackingId);

            // Add tracking pixel (once at the end, before </body>)
            if (! str_contains($htmlBody, 'email/track/open')) {
                $pixelUrl = route('email.track.open', ['id' => $trackingId]);
                $pixel = "<img src=\"{$pixelUrl}\" width=\"1\" height=\"1\" alt=\"\" style=\"display:none;\" data-tracking-id=\"{$trackingId}\" />";

                if (str_contains($htmlBody, '</body>')) {
                    $htmlBody = str_replace('</body>', $pixel.'</body>', $htmlBody);
                } else {
                    $htmlBody .= $pixel;
                }
            }

            // Add click tracking to all links (only if not already tracked)
            $htmlBody = $this->addLinkTracking($htmlBody, $trackingId);

            // Update the message with tracked HTML
            $message->html($htmlBody);
        } catch (\Exception $e) {
            // Log error but don't break email sending
            \Log::warning('Failed to add email tracking', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Add click tracking to all links in email body.
     */
    private function addLinkTracking(string $htmlBody, string $trackingId): string
    {
        // Match href attributes that aren't already tracking URLs
        $pattern = '/<a\s+([^>]*?)href=["\']([^"\']+)["\']([^>]*?)>/i';

        return preg_replace_callback($pattern, function ($matches) use ($trackingId) {
            $beforeHref = $matches[1];
            $originalUrl = html_entity_decode($matches[2], ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $afterHref = $matches[3];

            // Skip if already a tracking URL
            if (str_contains($originalUrl, '/track/email/')) {
                return $matches[0];
            }

            // Skip untrackable URLs (anchors, mailto, tel, etc.)
            if (preg_match('/^(#|mailto:|tel:|sms:|javascript:)/i', $originalUrl)) {
                return $matches[0];
            }

            // Build tracking URL
            $trackingUrl = route('email.track.click', [
                'id' => $trackingId,
                'url' => base64_encode($originalUrl),
            ]);

            return "<a {$beforeHref}href=\"{$trackingUrl}\" {$afterHref}>";
        }, $htmlBody);
    }
}
