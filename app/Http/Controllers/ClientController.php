<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');

        $clients = Client::query()
            ->withCount('files')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        // Get unlinked Google accounts (users with google_id but no matching client google_id)
        $unlinkedGoogleAccounts = \App\Models\User::where('role', 'client')
            ->whereNotNull('google_id')
            ->whereDoesntHave('client', function ($query) {
                $query->whereNotNull('google_id');
            })
            ->get()
            ->map(function ($user) {
                // Find matching client by email
                return (object) [
                    'user_id' => $user->id,
                    'google_id' => $user->google_id,
                    'email' => $user->email,
                    'name' => $user->name,
                    'client' => Client::where('email', $user->email)->first(),
                ];
            })
            ->filter(function ($item) {
                return $item->client !== null;
            });

        return view('admin.clients.index', compact('clients', 'search', 'unlinkedGoogleAccounts'));
    }

    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone' => 'nullable|string|max:20',
            'property_address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        // Clean up notes if they contain inappropriate content
        if (! empty($validated['notes'])) {
            $validated['notes'] = trim($validated['notes']);
            // Remove inappropriate content patterns
            $inappropriatePatterns = [
                '/\bdump\b/i',
                '/\bstahl\b/i',
                '/\b60minutes\b/i',
                '/\bbig\s+dump\b/i',
            ];
            foreach ($inappropriatePatterns as $pattern) {
                $validated['notes'] = preg_replace($pattern, '[REDACTED]', $validated['notes']);
            }
        }

        $client = Client::create($validated);

        // Log the client creation
        \App\Services\ActivityLogger::logClientCreate($client, [
            'email' => $client->email,
            'phone' => $client->phone,
        ]);

        // Provision user account and send initial credentials when appropriate
        try {
            \App\Services\ClientProvisionService::provision($client);
        } catch (\Exception $e) {
            \Log::error('Client provisioning failed after client create', ['client_id' => $client->id, 'error' => $e->getMessage()]);

            return redirect()->route('clients.index')->with('success', 'Client created, but provisioning failed. Please check logs.');
        }

        // Handle meeting creation if requested (from dashboard quick action)
        if ($request->has('create_meet') || $request->action === 'meeting') {
            return $this->createMeetingForClient($client);
        }

        // Check if request came from dashboard modal (AJAX-style redirect)
        if ($request->has('from_dashboard')) {
            return redirect()->route('admin.dashboard')->with('success', 'Client "'.$client->name.'" created successfully!');
        }

        return redirect()->route('clients.index')->with('success', 'Client created successfully.');
    }

    public function show(Client $client)
    {
        // Get client's messages (as sender or recipient). Use linked user id if available.
        $userId = $client->user?->id ?? null;

        if ($userId) {
            $messages = \App\Models\Message::with(['sender', 'recipient'])
                ->where(function ($q) use ($userId) {
                    $q->where('sender_id', $userId)->orWhere('recipient_id', $userId);
                })
                ->latest()
                ->get();
        } else {
            // No linked user account; return empty collection
            $messages = collect();
        }

        return view('admin.clients.show', compact('client', 'messages'));
    }

    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'property_address' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:500',
        ]);

        // Clean up notes if they contain inappropriate content
        if (! empty($validated['notes'])) {
            $validated['notes'] = trim($validated['notes']);
            // Remove inappropriate content patterns
            $inappropriatePatterns = [
                '/\bdump\b/i',
                '/\bstahl\b/i',
                '/\b60minutes\b/i',
                '/\bbig\s+dump\b/i',
            ];
            foreach ($inappropriatePatterns as $pattern) {
                $validated['notes'] = preg_replace($pattern, '[REDACTED]', $validated['notes']);
            }
        }

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']); // Don't update it
        }

        // Track changes for logging
        $changes = [];
        foreach ($validated as $key => $value) {
            if ($value != $client->$key) {
                $changes[$key] = [
                    'old' => $client->$key,
                    'new' => $value,
                ];
            }
        }

        $client->update($validated);

        // Log the client update with changes
        if (! empty($changes)) {
            \App\Services\ActivityLogger::logClientUpdate($client, $changes);
        }

        return redirect()->route('clients.index')->with('success', 'Client updated successfully.');
    }

    public function destroy(Client $client)
    {
        // Log the client deletion before deleting
        \App\Services\ActivityLogger::logClientDelete($client, [
            'email' => $client->email,
            'files_count' => $client->files()->count(),
        ]);

        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client deleted.');
    }

    public function exportCsv()
    {
        $fileName = 'clients_list_'.now()->format('Y-m-d_H-i-s').'.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ];

        $callback = function () {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, ['ID', 'Name', 'Email', 'Phone', 'Notification Email', 'Notes', 'Created At']);

            \App\Models\Client::orderBy('created_at', 'desc')->chunk(500, function ($rows) use ($handle) {
                foreach ($rows as $row) {
                    fputcsv($handle, [
                        $row->id,
                        $row->name,
                        $row->email,
                        $row->phone,
                        $row->notification_email ?? '',
                        $row->notes ?? '',
                        optional($row->created_at)->toDateTimeString(),
                    ]);
                }
            });

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function createMeetingForClient(Client $client)
    {
        try {
            // Check if admin has Google Calendar connected
            $admin = auth()->user();
            if (! $admin->googleCredential) {
                return redirect()->route('admin.dashboard')->with('warning', 'Client created successfully, but Google Calendar is not connected. Please connect Google Calendar first to create meetings.');
            }

            // Create a Google Meet link
            $meetLink = $this->createGoogleMeetEvent($client, $admin);

            if ($meetLink) {
                return redirect()->away($meetLink)->with('success', 'Client created and meeting started! Opening Google Meet...');
            } else {
                return redirect()->route('admin.dashboard')->with('warning', 'Client created successfully, but failed to create meeting. Please try manually.');
            }
        } catch (\Exception $e) {
            \Log::error('Failed to create meeting for client', [
                'client_id' => $client->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('admin.dashboard')->with('warning', 'Client created successfully, but meeting creation failed. Please try manually.');
        }
    }

    private function createGoogleMeetEvent(Client $client, $admin)
    {
        try {
            if (! class_exists('\Google_Client')) {
                throw new \RuntimeException('Google API client not installed');
            }

            $clientGoogle = new \Google_Client;
            $clientGoogle->setClientId(config('services.google.client_id'));
            $clientGoogle->setClientSecret(config('services.google.client_secret'));
            $clientGoogle->setAccessType('offline');

            // Ensure token is fresh
            if ($admin->googleCredential->needsRefresh()) {
                $admin->googleCredential->refreshAccessToken();
            }

            // Get access token
            $clientGoogle->setAccessToken($admin->googleCredential->access_token);

            $service = new \Google_Service_Calendar($clientGoogle);

            // Create event for next hour
            $event = new \Google_Service_Calendar_Event([
                'summary' => 'Meeting with '.$client->name,
                'description' => 'Client meeting created from dashboard',
                'start' => [
                    'dateTime' => now()->toISOString(),
                    'timeZone' => 'UTC',
                ],
                'end' => [
                    'dateTime' => now()->addHour()->toISOString(),
                    'timeZone' => 'UTC',
                ],
                'attendees' => [
                    ['email' => $client->email],
                    ['email' => $admin->email],
                ],
                'conferenceData' => [
                    'createRequest' => [
                        'conferenceSolutionKey' => ['type' => 'hangoutsMeet'],
                        'requestId' => uniqid(),
                    ],
                ],
            ]);

            $calendarId = $admin->google_calendar_id ?? 'primary';
            $event = $service->events->insert($calendarId, $event, ['conferenceDataVersion' => 1]);

            // Extract Meet link
            $meetLink = null;
            if ($event->getConferenceData() && $event->getConferenceData()->getConferenceSolution()) {
                $solution = $event->getConferenceData()->getConferenceSolution();
                if ($solution->getKey() && $solution->getKey()->getType() === 'hangoutsMeet') {
                    foreach ($solution->getEntryPoints() as $entryPoint) {
                        if ($entryPoint->getEntryPointType() === 'video') {
                            $meetLink = $entryPoint->getUri();
                            break;
                        }
                    }
                }
            }

            return $meetLink;

        } catch (\Exception $e) {
            \Log::error('Failed to create Google Meet event', [
                'client_id' => $client->id,
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Provision a user account for an existing client
     */
    public function provision(Client $client)
    {
        // Check if client already has a user account
        if ($client->user) {
            return back()->with('error', 'This client already has a user account.');
        }

        try {
            \App\Services\ClientProvisionService::provision($client);

            return back()->with('success', 'User account provisioned successfully for '.$client->name.'. Magic link sent to '.$client->email);
        } catch (\Exception $e) {
            \Log::error('Client provisioning failed', [
                'client_id' => $client->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Failed to provision user account: '.$e->getMessage());
        }
    }

    /**
     * Link a Google ID to a client account.
     */
    public function linkGoogleId(Request $request, Client $client)
    {
        $request->validate([
            'google_id' => 'required|string',
        ]);

        // Check if this Google ID is already linked to another client
        $existing = Client::where('google_id', $request->google_id)
            ->where('id', '!=', $client->id)
            ->first();

        if ($existing) {
            return back()->with('error', 'This Google ID is already linked to another client account.');
        }

        // Link the Google ID
        $client->update([
            'google_id' => $request->google_id,
            'google_linked_at' => now(),
        ]);

        // Also update the user account if it exists
        if ($client->user) {
            $client->user->update([
                'google_id' => $request->google_id,
            ]);
        }

        \Log::info('Google ID linked to client account', [
            'client_id' => $client->id,
            'client_email' => $client->email,
            'google_id' => $request->google_id,
        ]);

        return back()->with('success', 'Google ID has been successfully linked to '.$client->name.'\'s account.');
    }

    /**
     * Toggle portal access for a client
     */
    public function toggleAccess(Client $client)
    {
        $newStatus = ! $client->is_active;
        $client->update(['is_active' => $newStatus]);

        // Log the access toggle activity
        \App\Services\ActivityLogger::log(
            action: 'client_access_toggled',
            description: ($newStatus ? 'Enabled' : 'Disabled')." portal access for {$client->name}",
            subject: $client,
            properties: ['is_active' => $newStatus, 'previous_status' => ! $newStatus]
        );

        $message = $newStatus
            ? "Portal access enabled for {$client->name}. They can now login."
            : "Portal access disabled for {$client->name}. They cannot login.";

        return back()->with('success', $message);
    }
}
