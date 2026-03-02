<?php

namespace Tests\Feature;

use App\Models\EmailLog;
use App\Models\Message;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class MessageEmailTrackingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function test_sending_a_message_creates_an_email_log_entry(): void
    {
        $admin = User::factory()->create(['role' => 'company_administrator']);
        $client = User::factory()->create(['role' => 'client', 'email' => 'client@example.com']);

        $this->actingAs($admin)->post(route('messages.store'), [
            'recipient_id' => 'user-'.$client->id,
            'subject' => 'Test Subject',
            'body' => 'Test message body',
        ]);

        $emailLog = EmailLog::where('to_email', 'client@example.com')->first();
        $this->assertNotNull($emailLog);
        $this->assertEquals($admin->id, $emailLog->user_id);
        $this->assertStringContainsString('Test Subject', $emailLog->subject);
        $this->assertEquals('Test message body', $emailLog->body);
        $this->assertEquals('sent', $emailLog->status);
        $this->assertNotNull($emailLog->tracking_id);
        $this->assertNotNull($emailLog->sent_at);
    }

    public function test_replying_to_a_message_creates_an_email_log_entry(): void
    {
        $admin = User::factory()->create(['role' => 'company_administrator', 'email' => 'admin@example.com']);
        $client = User::factory()->create(['role' => 'client']);

        $message = Message::create([
            'sender_id' => $admin->id,
            'recipient_id' => $client->id,
            'subject' => 'Original Message',
            'body' => 'Original body',
        ]);

        $this->actingAs($client)->post(route('messages.reply', $message), [
            'body' => 'Reply body',
        ]);

        $emailLog = EmailLog::where('to_email', 'admin@example.com')
            ->where('subject', 'like', '%Re:%')
            ->first();

        $this->assertNotNull($emailLog);
        $this->assertEquals($client->id, $emailLog->user_id);
        $this->assertEquals('Reply body', $emailLog->body);
        $this->assertEquals('sent', $emailLog->status);
        $this->assertNotNull($emailLog->tracking_id);
        $this->assertNotNull($emailLog->sent_at);
    }

    public function test_sent_messages_appear_in_email_deliverability_view(): void
    {
        $admin = User::factory()->create(['role' => 'company_administrator']);
        $client = User::factory()->create(['role' => 'client', 'email' => 'client@example.com']);

        $this->actingAs($admin)->post(route('messages.store'), [
            'recipient_id' => 'user-'.$client->id,
            'subject' => 'Deliverability Test',
            'body' => 'This should appear in email deliverability',
        ]);

        $emailLog = EmailLog::where('to_email', 'client@example.com')->first();
        $this->assertNotNull($emailLog);

        $response = $this->actingAs($admin)->get(route('admin.email-logs.index'));
        $response->assertSuccessful();
        $response->assertSee('Deliverability Test');
        $response->assertSee('client@example.com');
    }
}
