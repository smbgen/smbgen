<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmailController extends Controller
{
    public function index()
    {
        try {
            // Get all users and clients for recipient selection
            $users = User::orderBy('name')->get();
            $clients = Client::orderBy('name')->get();

            // Get email history (last 50 sent emails from logs)
            $emailHistory = $this->getRecentEmailsFromLogs();

            return view('admin.email.index', compact('users', 'clients', 'emailHistory'));
        } catch (\Exception $e) {
            \Log::error('Failed to load email composer', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Return view with empty data on failure
            return view('admin.email.index', [
                'users' => collect(),
                'clients' => collect(),
                'emailHistory' => [],
            ])->with('error', 'Unable to load email composer data. Please try again later.');
        }
    }

    public function send(Request $request)
    {
        $validated = $request->validate([
            'recipients' => 'required|string',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'include_signature' => 'boolean',
        ]);

        // Parse recipients (comma or semicolon separated)
        $recipients = array_map('trim', preg_split('/[,;]/', $validated['recipients']));

        // Validate all emails
        foreach ($recipients as $email) {
            if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return redirect()->back()
                    ->withInput()
                    ->with('error', "Invalid email address: {$email}");
            }
        }

        // Add signature if requested
        $message = $validated['message'];
        if ($request->has('include_signature')) {
            $signature = "\n\n---\n".auth()->user()->name."\n".config('mail.from.address');
            $message .= $signature;
        }

        // Convert plain text to HTML
        $htmlMessage = nl2br(e($message));

        $successCount = 0;
        $failedEmails = [];

        foreach ($recipients as $email) {
            try {
                // Send email (listeners automatically handle tracking)
                Mail::html($htmlMessage, function ($msg) use ($email, $validated) {
                    $msg->to($email)
                        ->subject($validated['subject']);
                });

                $successCount++;

                \Log::info('Email sent via composer', [
                    'to' => $email,
                    'subject' => $validated['subject'],
                    'from' => config('mail.from.address'),
                    'sent_by' => auth()->user()->email,
                ]);

            } catch (\Exception $e) {
                $failedEmails[] = $email;

                \Log::error('Failed to send email via composer', [
                    'to' => $email,
                    'subject' => $validated['subject'],
                    'error' => $e->getMessage(),
                    'sent_by' => auth()->user()->email,
                ]);

                // Mark as failed if we have a tracking ID
                if (isset($emailLog) && $emailLog) {
                    $trackingService->markAsFailed($emailLog->tracking_id, $e->getMessage());
                }
            }
        }

        if ($successCount > 0 && empty($failedEmails)) {
            return redirect()->route('admin.email.index')
                ->with('success', "✅ Successfully sent {$successCount} email(s) with tracking!");
        } elseif ($successCount > 0 && ! empty($failedEmails)) {
            return redirect()->route('admin.email.index')
                ->with('warning', "⚠️ Sent {$successCount} email(s), but failed to send to: ".implode(', ', $failedEmails));
        } else {
            return redirect()->route('admin.email.index')
                ->with('error', '❌ Failed to send all emails. Check logs for details.');
        }
    }

    public function getTemplate(Request $request)
    {
        $appName = config('app.name');
        $companyName = config('business.company_name', $appName);

        $templates = [
            'welcome' => [
                'subject' => 'Welcome to '.$appName,
                'body' => "Hello,\n\nWelcome to {$appName}! We're excited to have you on board.\n\nIf you have any questions, please don't hesitate to reach out.\n\nBest regards,\nThe Team",
            ],
            'login_assistance' => [
                'subject' => 'Login Help - {$appName}',
                'body' => "Hello,\n\nWe're here to help you access your {$appName} account.\n\n🔗 Login to Your Account:\n{$appName} Portal: ".route('login')."\n\n❓ Need help?\n• Check that you're using the correct email address\n• Ensure caps lock is off when entering your password\n• If you're having trouble logging in, we can help reset your password\n\nIf you continue to have issues, please reach out to our support team and we'll be happy to assist.\n\nBest regards,\nThe {$companyName} Team",
            ],
            'password_reset' => [
                'subject' => 'Reset Your Password - {$appName}',
                'body' => "Hello,\n\nWe received a request to reset your password. If you didn't make this request, you can ignore this email.\n\n🔐 Reset Your Password:\n".route('password.request')."\n\nThe password reset link will expire in 60 minutes for security reasons.\n\n⚠️ Important Security Notes:\n• Never share your password with anyone\n• We will never ask for your password via email\n• Always log in directly through our portal, not via email links\n\nIf you need additional help, please contact our support team.\n\nBest regards,\nThe {$companyName} Team",
            ],
            'booking_reminder' => [
                'subject' => 'Reminder: Your Upcoming Appointment',
                'body' => "Hello [Customer Name],\n\nThis is a friendly reminder about your upcoming appointment with {$companyName}.\n\n📅 Appointment Details:\n• Date: [Date]\n• Time: [Time]\n• Service: [Service Description]\n• Location: [Service Location]\n\nWhat to Expect:\nOur team will arrive on time and ready to provide excellent service. If you have any special requirements or questions, please let us know in advance.\n\nNeed to Make Changes?\nIf you need to reschedule or cancel, please contact us at least 24 hours in advance:\n• Phone: [Phone Number]\n• Email: [Email Address]\n\nWe look forward to serving you!\n\nBest regards,\nThe {$companyName} Team",
            ],
            'follow_up' => [
                'subject' => 'Following Up',
                'body' => "Hello,\n\nI wanted to follow up with you regarding our recent conversation.\n\nPlease let me know if you have any questions or if there's anything I can help you with.\n\nBest regards,",
            ],
            'thank_you' => [
                'subject' => 'Thank You!',
                'body' => "Hello,\n\nThank you for choosing {$appName}!\n\nWe appreciate your business and look forward to serving you again.\n\nBest regards,\nThe Team",
            ],
        ];

        $templateType = $request->input('template');

        if (isset($templates[$templateType])) {
            return response()->json($templates[$templateType]);
        }

        return response()->json(['error' => 'Template not found'], 404);
    }

    private function getRecentEmailsFromLogs($limit = 50)
    {
        $emails = [];

        try {
            $logFile = storage_path('logs/laravel.log');
            if (file_exists($logFile) && filesize($logFile) > 0) {
                // Read only the last 100KB of the log file to avoid memory issues
                $handle = fopen($logFile, 'r');
                $fileSize = filesize($logFile);
                $readSize = min($fileSize, 100000); // Read last 100KB

                fseek($handle, max(0, $fileSize - $readSize));
                $content = fread($handle, $readSize);
                fclose($handle);

                $lines = explode("\n", $content);
                $lines = array_reverse($lines);

                foreach ($lines as $line) {
                    if (strpos($line, 'Email sent via composer') !== false && count($emails) < $limit) {
                        // Parse log line for email info
                        preg_match('/\[(\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2})\]/', $line, $dateMatch);
                        preg_match('/"to":"([^"]+)"/', $line, $toMatch);
                        preg_match('/"subject":"([^"]+)"/', $line, $subjectMatch);

                        if (! empty($dateMatch) && ! empty($toMatch) && ! empty($subjectMatch)) {
                            $emails[] = [
                                'date' => $dateMatch[1],
                                'to' => $toMatch[1],
                                'subject' => $subjectMatch[1],
                            ];
                        }
                    }

                    if (count($emails) >= $limit) {
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::warning('Failed to read email history from logs', [
                'error' => $e->getMessage(),
                'limit' => $limit,
            ]);
            // Return empty array on failure
        }

        return $emails;
    }

    /**
     * Get unique customer emails from bookings for autocomplete
     */
    public function getBookingEmails(Request $request)
    {
        try {
            $search = $request->input('q', '');

            $emails = Booking::select('customer_email', 'customer_name')
                ->whereNotNull('customer_email')
                ->where('customer_email', '!=', '')
                ->when($search, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('customer_email', 'like', "%{$search}%")
                            ->orWhere('customer_name', 'like', "%{$search}%");
                    });
                })
                ->groupBy('customer_email', 'customer_name')
                ->orderBy('customer_email')
                ->limit(50)
                ->get()
                ->map(function ($booking) {
                    return [
                        'email' => $booking->customer_email,
                        'name' => $booking->customer_name,
                        'label' => "{$booking->customer_name} <{$booking->customer_email}>",
                        'source' => 'booking',
                    ];
                });

            return response()->json($emails);
        } catch (\Exception $e) {
            \Log::error('Failed to get booking emails for autocomplete', [
                'error' => $e->getMessage(),
                'search' => $request->input('q', ''),
            ]);

            // Return empty array on failure
            return response()->json([]);
        }
    }

    /**
     * Get all emails (clients, users, and bookings) for autocomplete
     */
    public function getAllEmails(Request $request)
    {
        try {
            $search = $request->input('q', '');

            if (strlen($search) < 2) {
                return response()->json([]);
            }

            $results = [];

            // Get clients
            $clients = Client::select('email', 'name', 'company')
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->where(function ($query) use ($search) {
                    $query->where('email', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('company', 'like', "%{$search}%");
                })
                ->orderBy('name')
                ->limit(25)
                ->get()
                ->map(function ($client) {
                    return [
                        'email' => $client->email,
                        'name' => $client->name,
                        'label' => $client->company
                            ? "{$client->name} ({$client->company}) <{$client->email}>"
                            : "{$client->name} <{$client->email}>",
                        'source' => 'client',
                        'company' => $client->company,
                    ];
                });

            // Get users
            $users = User::select('email', 'name')
                ->whereNotNull('email')
                ->where('email', '!=', '')
                ->where(function ($query) use ($search) {
                    $query->where('email', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%");
                })
                ->orderBy('name')
                ->limit(25)
                ->get()
                ->map(function ($user) {
                    return [
                        'email' => $user->email,
                        'name' => $user->name,
                        'label' => "{$user->name} <{$user->email}>",
                        'source' => 'user',
                    ];
                });

            // Get bookings (only if not already a client)
            $clientEmails = $clients->pluck('email')->toArray();
            $bookings = Booking::select('customer_email', 'customer_name')
                ->whereNotNull('customer_email')
                ->where('customer_email', '!=', '')
                ->whereNotIn('customer_email', $clientEmails)
                ->where(function ($query) use ($search) {
                    $query->where('customer_email', 'like', "%{$search}%")
                        ->orWhere('customer_name', 'like', "%{$search}%");
                })
                ->groupBy('customer_email', 'customer_name')
                ->orderBy('customer_name')
                ->limit(25)
                ->get()
                ->map(function ($booking) {
                    return [
                        'email' => $booking->customer_email,
                        'name' => $booking->customer_name,
                        'label' => "{$booking->customer_name} <{$booking->customer_email}>",
                        'source' => 'booking',
                    ];
                });

            // Merge and sort by relevance (clients first, then users, then bookings)
            $results = $clients->concat($users)->concat($bookings);

            return response()->json($results->values()->all());
        } catch (\Exception $e) {
            \Log::error('Failed to get all emails for autocomplete', [
                'error' => $e->getMessage(),
                'search' => $request->input('q', ''),
            ]);

            return response()->json([]);
        }
    }
}
