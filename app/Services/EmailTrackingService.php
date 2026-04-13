<?php

namespace App\Services;

use App\Models\EmailLog;
use Illuminate\Support\Str;

class EmailTrackingService
{
    /**
     * Create a new email log entry
     */
    public function createLog(array $data): EmailLog
    {
        try {
            return EmailLog::create(array_merge($data, [
                'tracking_id' => Str::uuid()->toString(),
                'status' => 'pending',
            ]));
        } catch (\Exception $e) {
            \Log::error('Failed to create email log', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Add tracking pixel to HTML email body
     */
    public function addTrackingPixel(string $htmlBody, string $trackingId): string
    {
        $pixelUrl = route('email.track.open', ['id' => $trackingId]);

        $pixel = "<img src=\"{$pixelUrl}\" width=\"1\" height=\"1\" alt=\"\" style=\"display:block\" />";

        // Insert before closing </body> tag, or append if no body tag
        if (str_contains($htmlBody, '</body>')) {
            return str_replace('</body>', $pixel.'</body>', $htmlBody);
        }

        return $htmlBody.$pixel;
    }

    /**
     * Add click tracking to all links in HTML email
     */
    public function addLinkTracking(string $htmlBody, string $trackingId): string
    {
        // Match href attribute anywhere in the anchor tag
        $pattern = '/<a\s+([^>]*?)href=["\']([^"\']+)["\']([^>]*?)>/i';

        $replacementCount = 0;

        $result = preg_replace_callback($pattern, function ($matches) use ($trackingId, &$replacementCount) {
            $beforeHref = $matches[1];
            $originalUrl = $matches[2];
            $afterHref = $matches[3];

            // Skip tracking for certain links
            if ($this->shouldSkipTracking($originalUrl)) {
                \Log::debug('Skipping tracking for URL', ['url' => $originalUrl]);

                return $matches[0];
            }

            // Build tracking URL
            $trackingUrl = route('email.track.click', [
                'id' => $trackingId,
                'url' => base64_encode($originalUrl),
            ]);

            $replacementCount++;

            \Log::debug('Adding click tracking', [
                'tracking_id' => $trackingId,
                'original_url' => $originalUrl,
                'tracking_url' => $trackingUrl,
            ]);

            // Rebuild anchor tag with tracking URL
            return "<a {$beforeHref}href=\"{$trackingUrl}\"{$afterHref}>";
        }, $htmlBody);

        \Log::info('Link tracking applied', [
            'tracking_id' => $trackingId,
            'links_tracked' => $replacementCount,
        ]);

        return $result;
    }

    /**
     * Check if a URL should skip tracking
     */
    protected function shouldSkipTracking(string $url): bool
    {
        $skipPatterns = [
            'unsubscribe',
            'preferences',
            'mailto:',
            'tel:',
            '#',
        ];

        foreach ($skipPatterns as $pattern) {
            if (str_contains(strtolower($url), $pattern)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Record an email open event
     */
    public function recordOpen(string $trackingId, array $metadata = []): bool
    {
        try {
            $emailLog = EmailLog::where('tracking_id', $trackingId)->first();

            if (! $emailLog) {
                \Log::warning('Email log not found for tracking ID', ['tracking_id' => $trackingId]);

                return false;
            }

            // Throttle: Don't count opens within 30 seconds of last open (prevents double-counting from email client prefetch)
            if ($emailLog->last_opened_at && $emailLog->last_opened_at->diffInSeconds(now()) < 30) {
                return true; // Return true but don't increment
            }

            $updates = [
                'open_count' => $emailLog->open_count + 1,
                'last_opened_at' => now(),
            ];

            // Set status to opened if this is the first open
            if ($emailLog->status === 'sent' || $emailLog->status === 'delivered') {
                $updates['status'] = 'opened';
                $updates['opened_at'] = $emailLog->opened_at ?? now();
            }

            // Add metadata if provided
            if (isset($metadata['ip_address'])) {
                $updates['ip_address'] = $metadata['ip_address'];
            }

            if (isset($metadata['user_agent'])) {
                $updates['user_agent'] = $metadata['user_agent'];
            }

            $emailLog->update($updates);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to record email open event', [
                'tracking_id' => $trackingId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Record an email click event
     */
    public function recordClick(string $trackingId): bool
    {
        try {
            $emailLog = EmailLog::where('tracking_id', $trackingId)->first();

            if (! $emailLog) {
                \Log::warning('Email log not found for tracking ID', ['tracking_id' => $trackingId]);

                return false;
            }

            $updates = [
                'click_count' => $emailLog->click_count + 1,
                'last_clicked_at' => now(),
            ];

            // Set status to clicked if this is the first click
            if (in_array($emailLog->status, ['sent', 'delivered', 'opened'])) {
                $updates['status'] = 'clicked';
                $updates['clicked_at'] = $emailLog->clicked_at ?? now();
            }

            $emailLog->update($updates);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to record email click event', [
                'tracking_id' => $trackingId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Mark email as sent
     */
    public function markAsSent(string $trackingId, ?string $smtpResponse = null): bool
    {
        try {
            $emailLog = EmailLog::where('tracking_id', $trackingId)->first();

            if (! $emailLog) {
                \Log::warning('Email log not found for tracking ID', ['tracking_id' => $trackingId]);

                return false;
            }

            $emailLog->update([
                'status' => 'sent',
                'sent_at' => now(),
                'smtp_response' => $smtpResponse,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to mark email as sent', [
                'tracking_id' => $trackingId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Mark email as failed
     */
    public function markAsFailed(string $trackingId, string $errorMessage): bool
    {
        try {
            $emailLog = EmailLog::where('tracking_id', $trackingId)->first();

            if (! $emailLog) {
                \Log::warning('Email log not found for tracking ID', ['tracking_id' => $trackingId]);

                return false;
            }

            $emailLog->update([
                'status' => 'failed',
                'error_message' => $errorMessage,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to mark email as failed', [
                'tracking_id' => $trackingId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Mark email as bounced
     */
    public function markAsBounced(string $trackingId, string $bounceReason): bool
    {
        try {
            $emailLog = EmailLog::where('tracking_id', $trackingId)->first();

            if (! $emailLog) {
                \Log::warning('Email log not found for tracking ID', ['tracking_id' => $trackingId]);

                return false;
            }

            $emailLog->update([
                'status' => 'bounced',
                'bounced_at' => now(),
                'error_message' => $bounceReason,
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to mark email as bounced', [
                'tracking_id' => $trackingId,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get email statistics for the last N hours
     */
    public function getStats(?int $hours = 24): array
    {
        try {
            $query = EmailLog::query();

            if ($hours !== null) {
                $query->recentHours($hours);
            }

            $recentEmails = $query->get();

            $total = $recentEmails->count();
            $sent = $recentEmails->whereNotNull('sent_at')->count();
            $delivered = $recentEmails->whereIn('status', ['delivered', 'opened', 'clicked'])->count();
            $opened = $recentEmails->whereIn('status', ['opened', 'clicked'])->count();
            $clicked = $recentEmails->where('status', 'clicked')->count();
            $bounced = $recentEmails->where('status', 'bounced')->count();
            $failed = $recentEmails->where('status', 'failed')->count();

            return [
                'total' => $total,
                'sent' => $sent,
                'delivered' => $delivered,
                'opened' => $opened,
                'clicked' => $clicked,
                'bounced' => $bounced,
                'failed' => $failed,
                'delivery_rate' => $sent > 0 ? round(($delivered / $sent) * 100, 1) : 0,
                'open_rate' => $sent > 0 ? round(($opened / $sent) * 100, 1) : 0,
                'click_rate' => $sent > 0 ? round(($clicked / $sent) * 100, 1) : 0,
                'bounce_rate' => $sent > 0 ? round(($bounced / $sent) * 100, 1) : 0,
            ];
        } catch (\Exception $e) {
            \Log::error('Failed to calculate email statistics', [
                'error' => $e->getMessage(),
                'hours' => $hours,
            ]);

            // Return empty stats on failure
            return [
                'total' => 0,
                'sent' => 0,
                'delivered' => 0,
                'opened' => 0,
                'clicked' => 0,
                'bounced' => 0,
                'failed' => 0,
                'delivery_rate' => 0,
                'open_rate' => 0,
                'click_rate' => 0,
                'bounce_rate' => 0,
            ];
        }
    }
}
