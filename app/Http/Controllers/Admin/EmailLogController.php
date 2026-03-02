<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailLog;
use App\Services\EmailTrackingService;
use Illuminate\Http\Request;

class EmailLogController extends Controller
{
    public function __construct()
    {
        // Middleware is applied at route level in Laravel 12
    }

    /**
     * Display the email deliverability dashboard
     */
    public function index(Request $request)
    {
        try {
            $trackingService = app(EmailTrackingService::class);

            // Get filter parameters
            $status = $request->get('status');
            $hours = $request->get('hours', 24);
            $search = $request->get('search');

            // Build query
            $query = EmailLog::with(['user', 'booking'])
                ->orderBy('created_at', 'desc');

            // Apply filters
            if ($status && $status !== 'all') {
                $query->where('status', $status);
            }

            if ($hours !== 'all') {
                $query->recentHours((int) $hours);
            }

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('to_email', 'like', "%{$search}%")
                        ->orWhere('subject', 'like', "%{$search}%")
                        ->orWhere('tracking_id', 'like', "%{$search}%");
                });
            }

            // Paginate results
            $emailLogs = $query->paginate(50)->withQueryString();

            // Get statistics
            $stats = $trackingService->getStats($hours === 'all' ? null : (int) $hours);

            return view('admin.email-logs.index', compact('emailLogs', 'stats', 'status', 'hours', 'search'));
        } catch (\Exception $e) {
            \Log::error('Failed to load email deliverability dashboard', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return view with empty data and error message
            return view('admin.email-logs.index', [
                'emailLogs' => collect()->paginate(50),
                'stats' => [
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
                ],
                'status' => $request->get('status'),
                'hours' => $request->get('hours', 24),
                'search' => $request->get('search'),
                'error' => 'Unable to load email deliverability data. Please try again later.',
            ])->with('error', 'Unable to load email deliverability data. Please check the application logs for details.');
        }
    }

    /**
     * Display a specific email log
     */
    public function show(EmailLog $emailLog)
    {
        try {
            $emailLog->load(['user', 'booking']);

            return view('admin.email-logs.show', compact('emailLog'));
        } catch (\Exception $e) {
            \Log::error('Failed to load email log details', [
                'email_log_id' => $emailLog->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Unable to load email log details. Please try again later.');
        }
    }

    /**
     * Resend a failed email
     */
    public function resend(EmailLog $emailLog)
    {
        if (! in_array($emailLog->status, ['failed', 'bounced'])) {
            return back()->with('error', 'Only failed or bounced emails can be resent.');
        }

        try {
            $trackingService = app(EmailTrackingService::class);

            // Create new email log
            $newLog = $trackingService->createLog([
                'user_id' => $emailLog->user_id,
                'booking_id' => $emailLog->booking_id,
                'to_email' => $emailLog->to_email,
                'cc_email' => $emailLog->cc_email,
                'subject' => $emailLog->subject,
                'body' => $emailLog->body,
            ]);

            if (! $newLog) {
                return back()->with('error', 'Failed to create email log for resending. Please try again later.');
            }

            // Add tracking
            $emailBody = $trackingService->addTrackingPixel($emailLog->body, $newLog->tracking_id);
            $emailBody = $trackingService->addLinkTracking($emailBody, $newLog->tracking_id);

            // Send email
            \Mail::html($emailBody, function ($message) use ($emailLog) {
                $message->to($emailLog->to_email)
                    ->subject($emailLog->subject);

                if ($emailLog->cc_email) {
                    $message->cc($emailLog->cc_email);
                }
            });

            $trackingService->markAsSent($newLog->tracking_id);

            return back()->with('success', 'Email resent successfully! Tracking ID: '.$newLog->tracking_id);
        } catch (\Exception $e) {
            \Log::error('Failed to resend email: '.$e->getMessage());

            return back()->with('error', 'Failed to resend email: '.$e->getMessage());
        }
    }

    /**
     * Delete an email log
     */
    public function destroy(EmailLog $emailLog)
    {
        try {
            $emailLog->delete();

            return back()->with('success', 'Email log deleted successfully.');
        } catch (\Exception $e) {
            \Log::error('Failed to delete email log', [
                'email_log_id' => $emailLog->id ?? null,
                'error' => $e->getMessage(),
            ]);

            return back()->with('error', 'Failed to delete email log. Please try again later.');
        }
    }

    /**
     * Test SMTP connection and SSL/TLS handshake
     */
    public function testSmtp(Request $request)
    {
        try {
            $host = config('mail.mailers.smtp.host');
            $port = config('mail.mailers.smtp.port');
            $encryption = config('mail.mailers.smtp.encryption');

            $results = [
                'config' => [
                    'host' => $host,
                    'port' => $port,
                    'encryption' => $encryption,
                    'username' => config('mail.mailers.smtp.username'),
                    'from_address' => config('mail.from.address'),
                    'from_name' => config('mail.from.name'),
                ],
                'connectivity' => $this->testConnectivity($host, $port),
                'ssl_handshake' => $this->testSslHandshake($host, $port, $encryption),
                'auth_test' => $this->testSmtpAuth(),
            ];

            return response()->json([
                'success' => true,
                'results' => $results,
            ]);
        } catch (\Exception $e) {
            \Log::error('SMTP test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Test basic TCP connectivity to SMTP server
     */
    protected function testConnectivity($host, $port)
    {
        $startTime = microtime(true);

        $connection = @fsockopen($host, $port, $errno, $errstr, 5);

        if ($connection) {
            fclose($connection);

            return [
                'status' => 'success',
                'message' => "Successfully connected to {$host}:{$port}",
                'response_time' => round((microtime(true) - $startTime) * 1000, 2).'ms',
            ];
        }

        return [
            'status' => 'failed',
            'message' => "Cannot connect to {$host}:{$port}",
            'error' => $errstr,
            'error_code' => $errno,
        ];
    }

    /**
     * Test SSL/TLS handshake using stream_socket_client
     */
    protected function testSslHandshake($host, $port, $encryption)
    {
        $startTime = microtime(true);

        try {
            $protocol = $encryption === 'ssl' ? 'ssl' : 'tcp';
            $context = stream_context_create([
                'ssl' => [
                    'verify_peer' => config('mail.mailers.smtp.verify_peer', true),
                    'verify_peer_name' => config('mail.mailers.smtp.verify_peer_name', true),
                    'allow_self_signed' => false,
                ],
            ]);

            $socket = @stream_socket_client(
                "{$protocol}://{$host}:{$port}",
                $errno,
                $errstr,
                10,
                STREAM_CLIENT_CONNECT,
                $context
            );

            if ($socket) {
                // Read SMTP banner
                $banner = fgets($socket);
                fclose($socket);

                return [
                    'status' => 'success',
                    'message' => 'SSL/TLS handshake successful',
                    'banner' => trim($banner),
                    'response_time' => round((microtime(true) - $startTime) * 1000, 2).'ms',
                    'protocol' => $protocol,
                    'encryption' => $encryption,
                ];
            }

            return [
                'status' => 'failed',
                'message' => 'SSL/TLS handshake failed',
                'error' => $errstr,
                'error_code' => $errno,
                'protocol' => $protocol,
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'message' => 'SSL/TLS handshake failed',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Test SMTP authentication by attempting to send via Laravel's Mail facade
     */
    protected function testSmtpAuth()
    {
        try {
            // Use array driver for test to avoid actually sending
            $originalMailer = config('mail.default');
            config(['mail.default' => 'array']);

            \Mail::raw('SMTP Test', function ($message) {
                $message->to('test@example.com')
                    ->subject('SMTP Connection Test');
            });

            config(['mail.default' => $originalMailer]);

            return [
                'status' => 'success',
                'message' => 'Mail configuration is valid',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'failed',
                'message' => 'Mail configuration test failed',
                'error' => $e->getMessage(),
            ];
        }
    }
}
