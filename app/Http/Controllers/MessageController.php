<?php

namespace App\Http\Controllers;

use App\Mail\NewMessageReceived;
use App\Models\Message;
use App\Models\User;
use App\Services\EmailTrackingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\View;

class MessageController extends Controller
{
    protected EmailTrackingService $trackingService;

    public function __construct(EmailTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    public function index()
    {
        $user = Auth::user();

        // Get all messages for the user
        $allMessages = Message::with(['sender', 'recipient'])
            ->where(function ($query) use ($user) {
                $query->where('sender_id', $user->id)
                    ->orWhere('recipient_id', $user->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        // Group messages by conversation (thread)
        $threads = [];
        $processedPairs = [];

        foreach ($allMessages as $message) {
            $otherUserId = $message->sender_id === $user->id ? $message->recipient_id : $message->sender_id;

            // Create a unique key for this conversation pair
            $pairKey = implode('-', [$user->id < $otherUserId ? $user->id : $otherUserId, $user->id > $otherUserId ? $user->id : $otherUserId]);

            if (! isset($processedPairs[$pairKey])) {
                // Get the latest message in this thread
                $latestMessage = Message::with(['sender', 'recipient'])
                    ->where(function ($query) use ($user, $otherUserId) {
                        $query->where(function ($q) use ($user, $otherUserId) {
                            $q->where('sender_id', $user->id)
                                ->where('recipient_id', $otherUserId);
                        })->orWhere(function ($q) use ($user, $otherUserId) {
                            $q->where('sender_id', $otherUserId)
                                ->where('recipient_id', $user->id);
                        });
                    })
                    ->orderBy('created_at', 'desc')
                    ->first();

                // Count unread messages in this thread
                $unreadCount = Message::where('recipient_id', $user->id)
                    ->where('sender_id', $otherUserId)
                    ->where('is_read', false)
                    ->count();

                // Count total messages in thread
                $messageCount = Message::where(function ($query) use ($user, $otherUserId) {
                    $query->where(function ($q) use ($user, $otherUserId) {
                        $q->where('sender_id', $user->id)
                            ->where('recipient_id', $otherUserId);
                    })->orWhere(function ($q) use ($user, $otherUserId) {
                        $q->where('sender_id', $otherUserId)
                            ->where('recipient_id', $user->id);
                    });
                })->count();

                if ($latestMessage) {
                    $latestMessage->thread_unread_count = $unreadCount;
                    $latestMessage->thread_message_count = $messageCount;
                    $threads[] = $latestMessage;
                }

                $processedPairs[$pairKey] = true;
            }
        }

        // Paginate manually
        $perPage = 20;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;

        $paginatedThreads = array_slice($threads, $offset, $perPage);
        $messages = new \Illuminate\Pagination\LengthAwarePaginator(
            $paginatedThreads,
            count($threads),
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('messages.index', compact('messages'));
    }

    public function create()
    {
        $user = Auth::user();

        if ($user->isAdministrator()) {
            // Admins can message:
            // 1. Any non-admin user
            $users = User::where('role', '!=', 'company_administrator')
                ->where('id', '!=', $user->id)
                ->get()
                ->map(function ($u) {
                    return [
                        'id' => 'user-'.$u->id,
                        'name' => $u->name,
                        'email' => $u->email,
                        'type' => 'User',
                    ];
                });

            // 2. Any client from the clients table
            $clients = \App\Models\Client::orderBy('name')
                ->get()
                ->map(function ($c) {
                    return [
                        'id' => 'client-'.$c->id,
                        'name' => $c->name,
                        'email' => $c->email,
                        'type' => 'Client',
                        'company' => $c->company,
                    ];
                });

            // Merge and sort by name
            $recipients = $users->concat($clients)->sortBy('name')->values();
        } else {
            // Non-admins can message administrators
            $recipients = User::where('role', 'company_administrator')
                ->orderBy('name')
                ->get()
                ->map(function ($u) {
                    return [
                        'id' => 'user-'.$u->id,
                        'name' => $u->name,
                        'email' => $u->email,
                        'type' => 'User',
                    ];
                });
        }

        return view('messages.create', compact('recipients'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|string',
            'subject' => 'nullable|string|max:255',
            'body' => 'required|string|max:5000',
        ], [
            'recipient_id.required' => 'Please select a recipient.',
            'body.required' => 'The message body is required.',
            'body.max' => 'The message body cannot exceed 5000 characters.',
        ]);

        $user = Auth::user();

        // Parse recipient type and ID (format: "user-123" or "client-456")
        [$recipientType, $recipientId] = explode('-', $validated['recipient_id'], 2);

        $recipientEmail = null;
        $recipientName = null;
        $message = null;

        // Handle messaging to a User (stored in messages table)
        if ($recipientType === 'user') {
            $recipient = User::find($recipientId);

            if (! $recipient) {
                return redirect()->back()->withErrors(['recipient_id' => 'The selected recipient is invalid.']);
            }

            // Authorization: Clients can only message admins
            if ($user->isClient() && $recipient->isClient()) {
                return redirect()->back()->withErrors(['recipient_id' => 'You can only message administrators.']);
            }

            $message = Message::create([
                'sender_id' => Auth::id(),
                'recipient_id' => $recipientId,
                'subject' => $validated['subject'],
                'body' => $validated['body'],
            ]);

            $recipientEmail = $recipient->email;
            $recipientName = $recipient->name;

        } elseif ($recipientType === 'client') {
            // Handle messaging to a Client - check if they have a user account
            $client = \App\Models\Client::find($recipientId);

            if (! $client) {
                return redirect()->back()->withErrors(['recipient_id' => 'The selected client is invalid.']);
            }

            $recipientEmail = $client->email;
            $recipientName = $client->name;

            // If client has a user account, create a message record
            $clientUser = $client->user;
            if ($clientUser) {
                $message = Message::create([
                    'sender_id' => Auth::id(),
                    'recipient_id' => $clientUser->id,
                    'subject' => $validated['subject'],
                    'body' => $validated['body'],
                ]);
            }
        } else {
            return redirect()->back()->withErrors(['recipient_id' => 'Invalid recipient type.']);
        }

        // Send email notification with tracking
        if ($recipientEmail) {
            try {
                $subject = 'New Message: '.($validated['subject'] ?? 'Message from '.$user->name);

                // Check if recipient has a user account (for client portal link)
                $recipientHasAccount = \App\Models\User::where('email', $recipientEmail)->exists();

                // Determine the appropriate URL for the recipient
                $messageUrl = $message ? route('messages.show', $message) : route('messages.index');

                // Create email log entry
                $emailLog = $this->trackingService->createLog([
                    'user_id' => Auth::id(),
                    'to_email' => $recipientEmail,
                    'subject' => $subject,
                    'body' => $validated['body'],
                ]);

                if ($emailLog) {
                    // Build email HTML
                    $emailHtml = View::make('emails.simple-message', [
                        'subject' => $validated['subject'],
                        'body' => nl2br(e($validated['body'])),
                        'senderName' => $user->name,
                        'senderEmail' => $user->email,
                        'recipientName' => $recipientName,
                        'hasAccount' => $recipientHasAccount,
                        'messageUrl' => $messageUrl,
                        'messagesUrl' => route('messages.index'),
                        'loginUrl' => route('login'),
                    ])->render();

                    // Add tracking pixel and link tracking
                    $emailHtml = $this->trackingService->addTrackingPixel($emailHtml, $emailLog->tracking_id);
                    $emailHtml = $this->trackingService->addLinkTracking($emailHtml, $emailLog->tracking_id);

                    // Send email with tracked HTML
                    Mail::html($emailHtml, function ($msg) use ($recipientEmail, $recipientName, $subject) {
                        $msg->to($recipientEmail, $recipientName)->subject($subject);
                    });

                    // Mark as sent
                    $this->trackingService->markAsSent($emailLog->tracking_id);
                }
            } catch (\Exception $e) {
                \Log::error('Message email failed: '.$e->getMessage());
            }
        }

        $successMessage = $message ? 'Message sent successfully.' : 'Email sent to client successfully.';

        return redirect()->route('messages.index')->with('success', $successMessage);
    }

    public function show(Message $message)
    {
        \Log::info('Show message called', [
            'user_id' => Auth::id(),
            'message_id' => $message->id,
            'message_sender_id' => $message->sender_id,
            'message_recipient_id' => $message->recipient_id,
        ]);

        $user = Auth::user();

        // Check if user can view this message
        if ($message->sender_id !== $user->id && $message->recipient_id !== $user->id) {
            \Log::error('Unauthorized message view attempt', [
                'user_id' => $user->id,
                'message_sender_id' => $message->sender_id,
                'message_recipient_id' => $message->recipient_id,
            ]);

            return response()->view('errors.403', [], 403);
        }

        // Get all messages in this thread (between these two users)
        $otherUserId = $message->sender_id === $user->id ? $message->recipient_id : $message->sender_id;

        $thread = Message::with(['sender', 'recipient'])
            ->where(function ($query) use ($user, $otherUserId) {
                $query->where(function ($q) use ($user, $otherUserId) {
                    $q->where('sender_id', $user->id)
                        ->where('recipient_id', $otherUserId);
                })->orWhere(function ($q) use ($user, $otherUserId) {
                    $q->where('sender_id', $otherUserId)
                        ->where('recipient_id', $user->id);
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark all unread messages in this thread as read
        Message::where('recipient_id', $user->id)
            ->where('sender_id', $otherUserId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return view('messages.show', compact('message', 'thread'));
    }

    public function reply(Request $request, Message $message)
    {
        \Log::info('Reply method called', [
            'user_id' => Auth::id(),
            'message_id' => $message->id,
            'message_sender_id' => $message->sender_id,
            'message_recipient_id' => $message->recipient_id,
            'request_data' => $request->all(),
            'method' => $request->method(),
            'url' => $request->url(),
        ]);

        $user = Auth::user();

        // Check if user can reply to this message
        if ($message->sender_id !== $user->id && $message->recipient_id !== $user->id) {
            \Log::error('Unauthorized reply attempt', [
                'user_id' => $user->id,
                'message_sender_id' => $message->sender_id,
                'message_recipient_id' => $message->recipient_id,
            ]);

            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $validated = $request->validate([
            'body' => 'required|string|max:5000',
        ], [
            'body.required' => 'The message body is required.',
            'body.max' => 'The message body cannot exceed 5000 characters.',
        ]);

        // Determine recipient (the other person in the conversation)
        $recipientId = $message->sender_id === $user->id
            ? $message->recipient_id
            : $message->sender_id;

        \Log::info('Creating reply message', [
            'sender_id' => $user->id,
            'recipient_id' => $recipientId,
            'subject' => 'Re: '.($message->subject ?? 'Message'),
        ]);

        $reply = Message::create([
            'sender_id' => $user->id,
            'recipient_id' => $recipientId,
            'subject' => 'Re: '.($message->subject ?? 'Message'),
            'body' => $validated['body'],
        ]);

        // Send email notification with tracking (guard recipient exists)
        try {
            $recipient = User::find($recipientId);
            if ($recipient && $recipient->email) {
                // Load the reply with its sender relationship for the email
                $reply->load('sender');

                // Create email log entry
                $emailLog = $this->trackingService->createLog([
                    'user_id' => $user->id,
                    'to_email' => $recipient->email,
                    'subject' => 'New Message: '.($reply->subject ?? 'Message from '.$reply->sender->name),
                    'body' => $reply->body,
                ]);

                if ($emailLog) {
                    // Render the email HTML
                    $mailable = new NewMessageReceived($reply);
                    $emailHtml = View::make('emails.new-message-received', [
                        'message' => $reply,
                        'sender' => $reply->sender,
                        'recipient' => $recipient,
                    ])->render();

                    // Add tracking pixel and link tracking
                    $emailHtml = $this->trackingService->addTrackingPixel($emailHtml, $emailLog->tracking_id);
                    $emailHtml = $this->trackingService->addLinkTracking($emailHtml, $emailLog->tracking_id);

                    // Send email with tracked HTML
                    Mail::to($recipient->email)->send($mailable->html($emailHtml));

                    // Mark as sent
                    $this->trackingService->markAsSent($emailLog->tracking_id);
                } else {
                    // Fallback: send without tracking if log creation failed
                    Mail::to($recipient->email)->send(new NewMessageReceived($reply));
                }
            }
        } catch (\Exception $e) {
            \Log::error('Message email failed: '.$e->getMessage());
        }

        \Log::info('Reply sent successfully', ['new_message_id' => $reply->id]);

        return redirect()->route('messages.show', $message)->with('success', 'Reply sent successfully.');
    }

    public function markAsRead(Message $message)
    {
        $user = Auth::user();

        // Check if user can mark this message as read
        if ($message->recipient_id !== $user->id) {
            return response()->json(['error' => 'Unauthorized action.'], 403);
        }

        $message->markAsRead();

        return redirect()->route('messages.show', $message)->with('success', 'Message marked as read.');
    }
}
