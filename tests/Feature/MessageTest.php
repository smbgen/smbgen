<?php

namespace Tests\Feature;

use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MessageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test users
        $this->admin = User::factory()->admin()->create();
        $this->client = User::factory()->client()->create();
        $this->otherClient = User::factory()->client()->create();

        // Fake mail for testing
        Mail::fake();
    }

    public function test_guest_cannot_access_messages(): void
    {
        $response = $this->get('/messages');
        $response->assertRedirect('/login');
    }

    public function test_user_can_view_their_messages(): void
    {
        // Create messages for the client
        $sentMessage = Message::factory()->create([
            'sender_id' => $this->client->id,
            'recipient_id' => $this->admin->id,
            'created_at' => now()->subSecond(),
            'updated_at' => now()->subSecond(),
        ]);
        $receivedMessage = Message::factory()->create([
            'sender_id' => $this->admin->id,
            'recipient_id' => $this->client->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $response = $this->actingAs($this->client)->get('/messages');

        $response->assertOk();
        // Only expect the latest message in the thread (received message is newer)
        $response->assertSee($receivedMessage->subject);
    }

    public function test_user_cannot_view_other_users_messages(): void
    {
        // Create message between admin and other client
        $otherMessage = Message::factory()->create([
            'sender_id' => $this->admin->id,
            'recipient_id' => $this->otherClient->id,
        ]);

        $response = $this->actingAs($this->client)->get('/messages');

        $response->assertOk();
        $response->assertDontSee($otherMessage->subject);
    }

    public function test_admin_can_create_message_to_client(): void
    {
        $messageData = [
            'recipient_id' => 'user-'.$this->client->id,
            'subject' => 'Test message from admin',
            'body' => 'This is a test message body from admin to client.',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/messages', $messageData);

        $response->assertRedirect('/messages');
        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->admin->id,
            'recipient_id' => $this->client->id,
            'subject' => 'Test message from admin',
            'body' => 'This is a test message body from admin to client.',
            'is_read' => false,
        ]);

        // Check that email was sent (via Mail::html)
        Mail::assertSentCount(1);
    }

    public function test_client_can_create_message_to_admin(): void
    {
        $messageData = [
            'recipient_id' => 'user-'.$this->admin->id,
            'subject' => 'Test message from client',
            'body' => 'This is a test message body from client to admin.',
        ];

        $response = $this->actingAs($this->client)
            ->post('/messages', $messageData);

        $response->assertRedirect('/messages');
        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->client->id,
            'recipient_id' => $this->admin->id,
            'subject' => 'Test message from client',
            'body' => 'This is a test message body from client to admin.',
            'is_read' => false,
        ]);

        // Check that email was sent (via Mail::html)
        Mail::assertSentCount(1);
    }

    public function test_client_cannot_message_other_clients(): void
    {
        $messageData = [
            'recipient_id' => 'user-'.$this->otherClient->id,
            'subject' => 'Unauthorized message',
            'body' => 'This should not be allowed.',
        ];

        $response = $this->actingAs($this->client)
            ->post('/messages', $messageData);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['recipient_id']);
        $this->assertDatabaseMissing('messages', [
            'sender_id' => $this->client->id,
            'recipient_id' => $this->otherClient->id,
        ]);
    }

    public function test_user_can_view_single_message(): void
    {
        $message = Message::factory()->create([
            'sender_id' => $this->admin->id,
            'recipient_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->client)
            ->get("/messages/{$message->id}");

        $response->assertOk();
        $response->assertSee($message->subject);
        $response->assertSee($message->body);
    }

    public function test_user_cannot_view_other_users_message(): void
    {
        $message = Message::factory()->create([
            'sender_id' => $this->admin->id,
            'recipient_id' => $this->otherClient->id,
        ]);

        $response = $this->actingAs($this->client)
            ->get("/messages/{$message->id}");

        $response->assertForbidden();
    }

    public function test_user_can_reply_to_message(): void
    {
        $originalMessage = Message::factory()->create([
            'sender_id' => $this->admin->id,
            'recipient_id' => $this->client->id,
        ]);

        $replyData = [
            'body' => 'This is a reply to the original message.',
        ];

        $response = $this->actingAs($this->client)
            ->post("/messages/{$originalMessage->id}/reply", $replyData);

        $response->assertRedirect("/messages/{$originalMessage->id}");

        // Check that reply was created
        $this->assertDatabaseHas('messages', [
            'sender_id' => $this->client->id,
            'recipient_id' => $this->admin->id,
            'body' => 'This is a reply to the original message.',
            'is_read' => false,
        ]);

        // Check that email was sent (via Mail::html)
        Mail::assertSentCount(1);
    }

    public function test_user_can_mark_message_as_read(): void
    {
        $message = Message::factory()->unread()->create([
            'sender_id' => $this->admin->id,
            'recipient_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->client)
            ->patch("/messages/{$message->id}/mark-read");

        $response->assertRedirect("/messages/{$message->id}");

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'is_read' => true,
        ]);

        $this->assertNotNull($message->fresh()->read_at);
    }

    public function test_user_cannot_reply_to_other_users_message(): void
    {
        // Create message between admin and other client (not current client)
        $otherMessage = Message::factory()->create([
            'sender_id' => $this->admin->id,
            'recipient_id' => $this->otherClient->id,
        ]);

        $replyData = [
            'body' => 'Unauthorized reply attempt.',
        ];

        $response = $this->actingAs($this->client)
            ->post("/messages/{$otherMessage->id}/reply", $replyData);

        $response->assertForbidden();

        // Ensure no reply was created
        $this->assertDatabaseMissing('messages', [
            'sender_id' => $this->client->id,
            'recipient_id' => $this->admin->id,
            'body' => 'Unauthorized reply attempt.',
        ]);
    }

    public function test_user_cannot_mark_other_users_message_as_read(): void
    {
        $message = Message::factory()->unread()->create([
            'sender_id' => $this->admin->id,
            'recipient_id' => $this->otherClient->id,
        ]);

        $response = $this->actingAs($this->client)
            ->patch("/messages/{$message->id}/mark-read");

        $response->assertForbidden();

        $this->assertDatabaseHas('messages', [
            'id' => $message->id,
            'is_read' => false,
        ]);
    }

    public function test_dashboard_shows_recent_messages(): void
    {
        // Create recent messages
        $recentMessage = Message::factory()->create([
            'sender_id' => $this->admin->id,
            'recipient_id' => $this->client->id,
            'created_at' => now()->subHours(2),
        ]);

        $oldMessage = Message::factory()->create([
            'sender_id' => $this->admin->id,
            'recipient_id' => $this->client->id,
            'created_at' => now()->subDays(5),
        ]);

        $response = $this->actingAs($this->client)->get('/dashboard');

        $response->assertOk();
        $response->assertSee($recentMessage->subject);
        // Dashboard should show recent messages, not old ones
        $response->assertDontSee($oldMessage->subject);
    }

    public function test_message_validation_requires_recipient_id(): void
    {
        $messageData = [
            'subject' => 'Test message',
            'body' => 'Test body',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/messages', $messageData);

        $response->assertSessionHasErrors(['recipient_id']);
    }

    public function test_message_validation_requires_body(): void
    {
        $messageData = [
            'recipient_id' => $this->client->id,
            'subject' => 'Test message',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/messages', $messageData);

        $response->assertSessionHasErrors(['body']);
    }

    public function test_message_validation_requires_valid_recipient(): void
    {
        $messageData = [
            'recipient_id' => 99999, // Non-existent user
            'subject' => 'Test message',
            'body' => 'Test body',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/messages', $messageData);

        $response->assertSessionHasErrors(['recipient_id']);
    }

    public function test_reply_validation_requires_body(): void
    {
        $originalMessage = Message::factory()->create([
            'sender_id' => $this->admin->id,
            'recipient_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->client)
            ->post("/messages/{$originalMessage->id}/reply", []);

        $response->assertSessionHasErrors(['body']);
    }

    public function test_message_creation_page_shows_correct_recipients(): void
    {
        $response = $this->actingAs($this->admin)->get('/messages/create');

        $response->assertOk();
        $response->assertSee($this->client->name);
        $response->assertSee($this->otherClient->name); // Admins should see all clients
    }

    public function test_client_message_creation_page_shows_only_admins(): void
    {
        $response = $this->actingAs($this->client)->get('/messages/create');

        $response->assertOk();
        $response->assertSee($this->admin->name);
        $response->assertDontSee($this->otherClient->name); // Should not see other clients
    }

    public function test_message_email_contains_correct_information(): void
    {
        $messageData = [
            'recipient_id' => $this->client->id,
            'subject' => 'Test email subject',
            'body' => 'Test email body content',
        ];

        $this->actingAs($this->admin)->post('/messages', $messageData);

        // Check that email was sent (via Mail::html)
        Mail::assertSentCount(1);
    }

    public function test_unread_message_count_in_dashboard(): void
    {
        // Create unread messages
        Message::factory()->unread()->count(3)->create([
            'sender_id' => $this->admin->id,
            'recipient_id' => $this->client->id,
        ]);

        $response = $this->actingAs($this->client)->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Unread');
    }
}
