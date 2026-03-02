<?php

namespace App\Services;

use App\Models\User;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Illuminate\Support\Carbon;

class GoogleCalendarService
{
    protected ?Google_Client $client = null;

    protected bool $available = false;

    public function __construct()
    {
        try {
            if (! class_exists(Google_Client::class)) {
                \Log::warning('Google API client not available');

                return;
            }

            $this->client = new Google_Client;
            $this->client->setClientId(config('services.google.client_id'));
            $this->client->setClientSecret(config('services.google.client_secret'));
            $this->client->setAccessType('offline');
            $this->client->setPrompt('consent');
            $this->client->setScopes([
                Google_Service_Calendar::CALENDAR_EVENTS,
            ]);
            $this->available = true;
        } catch (\Exception $e) {
            \Log::error('Failed to initialize Google Calendar service', ['error' => $e->getMessage()]);
            $this->available = false;
        }
    }

    public function isAvailable(): bool
    {
        return $this->available && $this->client !== null;
    }

    /**
     * Create an event on behalf of the given user. The user must have GoogleCredential
     * with a refresh_token and optionally a calendar_id. Returns array with event_id and meet_link.
     *
     * @return array{event_id:string|null, meet_link:string|null}
     */
    public function createEventForUser(User $user, \DateTimeInterface $startsAt, int $durationMinutes = 30, string $summary = 'Appointment', ?string $description = null, ?string $attendeeEmail = null): array
    {
        \Log::info('[GoogleCalendar] createEventForUser called', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'user_name' => $user->name,
            'starts_at' => $startsAt->format('Y-m-d H:i:s'),
            'starts_at_iso' => Carbon::instance($startsAt)->toIso8601String(),
            'duration_minutes' => $durationMinutes,
            'summary' => $summary,
            'description_length' => $description ? strlen($description) : 0,
            'attendee_email' => $attendeeEmail,
            'has_google_credential' => $user->googleCredential !== null,
            'has_refresh_token' => $user->googleCredential && ! empty($user->googleCredential->refresh_token),
            'app_timezone' => config('app.timezone'),
            'current_server_time' => now()->toIso8601String(),
        ]);

        if (! $user->googleCredential || empty($user->googleCredential->refresh_token)) {
            \Log::error('[GoogleCalendar] User missing credentials', [
                'user_id' => $user->id,
                'has_credential_model' => $user->googleCredential !== null,
                'has_refresh_token' => $user->googleCredential ? ! empty($user->googleCredential->refresh_token) : false,
            ]);
            throw new \InvalidArgumentException('User does not have Google Calendar credentials stored.');
        }

        // Refresh access token if expired or needs refresh
        if ($user->googleCredential->needsRefresh()) {
            \Log::info('Access token needs refresh before creating event', [
                'user_id' => $user->id,
                'expires_at' => $user->googleCredential->expires_at,
            ]);

            if (! $user->googleCredential->refreshAccessToken()) {
                throw new \RuntimeException('Failed to refresh Google access token. Calendar may be disconnected.');
            }

            // Reload the relationship to get fresh data
            $user->load('googleCredential');
        }

        // Exchange refresh token for access token
        $this->client->fetchAccessTokenWithRefreshToken($user->googleCredential->refresh_token);
        $token = $this->client->getAccessToken();

        if (empty($token)) {
            throw new \RuntimeException('Failed to fetch access token from Google.');
        }

        $calendarId = $user->googleCredential->calendar_id ?? 'primary';

        \Log::info('[GoogleCalendar] Setting up calendar service', [
            'calendar_id' => $calendarId,
            'user_id' => $user->id,
            'has_client' => $this->client !== null,
            'has_access_token' => ! empty($this->client->getAccessToken()),
        ]);

        $service = new Google_Service_Calendar($this->client);

        $end = (clone $startsAt)->modify("+{$durationMinutes} minutes");

        \Log::info('[GoogleCalendar] Calculated event times', [
            'start_time' => Carbon::instance($startsAt)->toIso8601String(),
            'end_time' => Carbon::instance($end)->toIso8601String(),
            'duration_minutes' => $durationMinutes,
            'time_difference_seconds' => $end->getTimestamp() - $startsAt->getTimestamp(),
            'is_positive_duration' => ($end->getTimestamp() - $startsAt->getTimestamp()) > 0,
            'timezone' => config('app.timezone') ?: date_default_timezone_get(),
        ]);

        $event = new Google_Service_Calendar_Event([
            'summary' => $summary,
            'description' => $description,
            'start' => [
                'dateTime' => Carbon::instance($startsAt)->toAtomString(),
                'timeZone' => config('app.timezone') ?: date_default_timezone_get(),
            ],
            'end' => [
                'dateTime' => Carbon::instance($end)->toAtomString(),
                'timeZone' => config('app.timezone') ?: date_default_timezone_get(),
            ],
        ]);

        // Add attendee if provided
        if ($attendeeEmail) {
            $event->attendees = [
                ['email' => $attendeeEmail],
            ];
        }

        // Request Google Meet conference
        $conferenceRequest = new \Google_Service_Calendar_ConferenceSolutionKey;
        $conferenceRequest->setType('hangoutsMeet');

        $createRequest = new \Google_Service_Calendar_CreateConferenceRequest;
        $createRequest->setRequestId(uniqid('meet_', true));
        $createRequest->setConferenceSolutionKey($conferenceRequest);

        $conferenceData = new \Google_Service_Calendar_ConferenceData;
        $conferenceData->setCreateRequest($createRequest);

        $event->setConferenceData($conferenceData);

        \Log::info('[GoogleCalendar] About to call Google Calendar API', [
            'calendar_id' => $calendarId,
            'user_id' => $user->id,
            'event_summary' => $summary,
            'event_start' => $event->getStart()->dateTime,
            'event_end' => $event->getEnd()->dateTime,
            'has_attendees' => ! empty($event->attendees),
            'attendee_count' => $event->attendees ? count($event->attendees) : 0,
            'has_conference_data' => ! empty($event->conferenceData),
            'timezone' => $event->getStart()->timeZone,
        ]);

        try {
            $created = $service->events->insert($calendarId, $event, [
                'conferenceDataVersion' => 1,
                'sendUpdates' => 'all', // Send email invites to attendees
            ]);

            \Log::info('[GoogleCalendar] Google Calendar API call succeeded', [
                'calendar_id' => $calendarId,
                'created_event_id' => $created->getId(),
                'created_summary' => $created->getSummary(),
                'created_start' => $created->getStart()->dateTime ?? 'N/A',
                'created_end' => $created->getEnd()->dateTime ?? 'N/A',
                'created_status' => $created->getStatus(),
                'has_conference_data' => ! empty($created->conferenceData),
            ]);

            $meetLink = null;
            if (! empty($created->conferenceData) && ! empty($created->conferenceData->entryPoints)) {
                foreach ($created->conferenceData->entryPoints as $entry) {
                    if ($entry->entryPointType === 'video') {
                        $meetLink = $entry->uri;
                        break;
                    }
                }
            }

            \Log::info('Google Calendar event created', [
                'event_id' => $created->getId(),
                'calendar_id' => $calendarId,
                'has_conference_data' => ! empty($created->conferenceData),
                'meet_link' => $meetLink,
            ]);

            return [
                'event_id' => $created->getId(),
                'meet_link' => $meetLink,
            ];
        } catch (\Google_Service_Exception $e) {
            // Google API specific errors with detailed response
            \Log::error('[GoogleCalendar] Google Calendar API Exception', [
                'error_message' => $e->getMessage(),
                'status_code' => $e->getCode(),
                'errors' => $e->getErrors(),
                'calendar_id' => $calendarId,
                'user_id' => $user->id,
                'summary' => $summary,
                'description' => $description,
                'attendee_email' => $attendeeEmail,
                'start_time' => $startsAt->format('c'),
                'start_time_iso' => Carbon::instance($startsAt)->toIso8601String(),
                'duration_minutes' => $durationMinutes,
                'timezone' => config('app.timezone') ?: date_default_timezone_get(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        } catch (\Exception $e) {
            \Log::error('[GoogleCalendar] Generic Exception during event creation', [
                'error_message' => $e->getMessage(),
                'error_class' => get_class($e),
                'calendar_id' => $calendarId,
                'user_id' => $user->id,
                'summary' => $summary,
                'start_time' => $startsAt->format('c'),
                'duration_minutes' => $durationMinutes,
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Delete an event from the user's calendar
     *
     * @param  string  $eventId  The Google Calendar event ID
     * @param  int  $userId  The user ID who owns the calendar
     *
     * @throws \Exception
     */
    public function deleteEvent(string $eventId, int $userId): void
    {
        // Check if Google Calendar service is available
        if (! $this->isAvailable()) {
            \Log::warning('Google Calendar service not available, skipping event deletion', [
                'event_id' => $eventId,
                'user_id' => $userId,
            ]);

            return;
        }

        $user = User::find($userId);

        if (! $user) {
            \Log::warning('User not found for event deletion', ['user_id' => $userId]);

            return;
        }

        if (! $user->googleCredential || empty($user->googleCredential->refresh_token)) {
            \Log::warning('User does not have Google Calendar credentials, skipping event deletion', [
                'user_id' => $userId,
                'event_id' => $eventId,
            ]);

            return;
        }

        try {
            // Refresh access token if expired or needs refresh
            if ($user->googleCredential->needsRefresh()) {
                \Log::info('Access token needs refresh before deleting event', [
                    'user_id' => $userId,
                    'expires_at' => $user->googleCredential->expires_at,
                ]);

                if (! $user->googleCredential->refreshAccessToken()) {
                    \Log::warning('Failed to refresh token, skipping event deletion', [
                        'user_id' => $userId,
                        'event_id' => $eventId,
                    ]);

                    return;
                }

                // Reload the relationship to get fresh data
                $user->load('googleCredential');
            }

            // Exchange refresh token for access token
            $this->client->fetchAccessTokenWithRefreshToken($user->googleCredential->refresh_token);
            $token = $this->client->getAccessToken();

            if (empty($token)) {
                \Log::warning('Failed to fetch access token from Google, skipping event deletion', [
                    'user_id' => $userId,
                    'event_id' => $eventId,
                ]);

                return;
            }

            $calendarId = $user->googleCredential->calendar_id ?? 'primary';
            $service = new Google_Service_Calendar($this->client);

            try {
                $service->events->delete($calendarId, $eventId);

                \Log::info('Google Calendar event deleted', [
                    'event_id' => $eventId,
                    'calendar_id' => $calendarId,
                    'user_id' => $userId,
                ]);
            } catch (\Google_Service_Exception $e) {
                // Handle specific Google API errors gracefully
                $statusCode = $e->getCode();

                if ($statusCode == 404) {
                    // Event already deleted or never existed
                    \Log::info('Google Calendar event not found (already deleted or never created)', [
                        'event_id' => $eventId,
                        'user_id' => $userId,
                    ]);
                } elseif ($statusCode == 410) {
                    // Event was cancelled/deleted previously
                    \Log::info('Google Calendar event was already cancelled', [
                        'event_id' => $eventId,
                        'user_id' => $userId,
                    ]);
                } else {
                    // Other Google API errors - log but don't fail
                    \Log::warning('Google API error when deleting event', [
                        'error' => $e->getMessage(),
                        'status_code' => $statusCode,
                        'event_id' => $eventId,
                        'user_id' => $userId,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Catch any other errors (network issues, invalid tokens, etc.)
            \Log::warning('Exception while deleting Google Calendar event', [
                'error' => $e->getMessage(),
                'event_id' => $eventId,
                'user_id' => $userId,
            ]);
            // Don't throw - allow booking deletion to proceed
        }
    }
}
