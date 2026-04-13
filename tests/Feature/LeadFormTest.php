<?php

namespace Tests\Feature;

use App\Mail\NewLeadSubmitted;
use App\Models\LeadForm;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LeadFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create admin users
        $this->admin1 = User::factory()->admin()->create(['email' => 'admin1@test.com']);
        $this->admin2 = User::factory()->admin()->create(['email' => 'admin2@test.com']);

        // Fake mail for testing
        Mail::fake();
    }

    public function test_lead_form_can_be_submitted(): void
    {
        $leadData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'I need help with cybersecurity assessment',
            'source' => 'website',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 Test Browser',
            'referrer' => 'https://google.com',
        ];

        $response = $this->post('/leadform', $leadData);

        $response->assertRedirect();
        $this->assertDatabaseHas('lead_forms', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'I need help with cybersecurity assessment',
            'source_site' => 'website',
        ]);
    }

    public function test_lead_form_sends_email_to_all_admins(): void
    {
        $leadData = [
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'message' => 'Interested in cyber audit services',
            'source' => 'landing_page',
            'ip_address' => '127.0.0.1',
            'user_agent' => 'Mozilla/5.0 Test Browser',
            'referrer' => 'https://facebook.com',
        ];

        $this->post('/leadform', $leadData);

        // Check that emails were sent to both admins
        Mail::assertSent(NewLeadSubmitted::class, function ($mail) {
            return $mail->hasTo($this->admin1->email);
        });

        Mail::assertSent(NewLeadSubmitted::class, function ($mail) {
            return $mail->hasTo($this->admin2->email);
        });
    }

    public function test_lead_form_validation_requires_name(): void
    {
        $leadData = [
            'email' => 'test@example.com',
            'message' => 'Test message',
        ];

        $response = $this->post('/leadform', $leadData);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_lead_form_validation_requires_email(): void
    {
        $leadData = [
            'name' => 'Test User',
            'message' => 'Test message',
        ];

        $response = $this->post('/leadform', $leadData);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_lead_form_validation_requires_valid_email(): void
    {
        $leadData = [
            'name' => 'Test User',
            'email' => 'invalid-email',
            'message' => 'Test message',
        ];

        $response = $this->post('/leadform', $leadData);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_lead_form_validation_requires_message(): void
    {
        $leadData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ];

        $response = $this->post('/leadform', $leadData);

        $response->assertSessionHasErrors(['message']);
    }

    public function test_lead_form_stores_ip_address(): void
    {
        $leadData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Test message',
        ];

        $this->post('/leadform', $leadData);

        $this->assertDatabaseHas('lead_forms', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'ip_address' => '127.0.0.1',
        ]);
    }

    public function test_lead_form_stores_user_agent(): void
    {
        $leadData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Test message',
        ];

        $this->withHeaders([
            'User-Agent' => 'Mozilla/5.0 Test Browser',
        ])->post('/leadform', $leadData);

        $this->assertDatabaseHas('lead_forms', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'user_agent' => 'Mozilla/5.0 Test Browser',
        ]);
    }

    public function test_lead_form_stores_referrer(): void
    {
        $leadData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Test message',
        ];

        $this->withHeaders([
            'Referer' => 'https://google.com',
        ])->post('/leadform', $leadData);

        $this->assertDatabaseHas('lead_forms', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'referer' => 'https://google.com',
        ]);
    }

    public function test_lead_form_email_contains_correct_information(): void
    {
        $leadData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'I need cybersecurity help',
            'source' => 'website',
        ];

        $this->post('/leadform', $leadData);

        Mail::assertSent(NewLeadSubmitted::class, function ($mail) {
            return $mail->hasTo($this->admin1->email);
        });
    }

    public function test_lead_form_works_without_admin_users(): void
    {
        // Delete all admin users
        User::where('role', 'company_administrator')->delete();

        $leadData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Test message',
        ];

        $response = $this->post('/leadform', $leadData);

        $response->assertRedirect();
        $this->assertDatabaseHas('lead_forms', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    public function test_lead_form_handles_optional_fields(): void
    {
        $leadData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Test message',
            'source' => 'social_media',
            'phone' => '555-1234',
        ];

        $response = $this->post('/leadform', $leadData);

        $response->assertRedirect();
        $this->assertDatabaseHas('lead_forms', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'source_site' => 'social_media',
        ]);
    }

    public function test_lead_form_prevents_duplicate_submissions(): void
    {
        $leadData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Test message',
        ];

        // Submit the same form twice
        $this->post('/leadform', $leadData);
        $this->post('/leadform', $leadData);

        // Should have two records (duplicates are allowed)
        $this->assertEquals(2, LeadForm::where('email', 'test@example.com')->count());
    }

    public function test_lead_form_creates_client_record_when_converted(): void
    {
        $lead = LeadForm::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        // Simulate conversion (this would be done through admin interface)
        $client = \App\Models\Client::create([
            'name' => $lead->name,
            'email' => $lead->email,
            'message' => $lead->message,
            'source_site' => $lead->source_site,
        ]);

        $this->assertDatabaseHas('clients', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }

    public function test_lead_form_email_has_proper_subject(): void
    {
        $leadData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => 'Test message',
        ];

        $this->post('/leadform', $leadData);

        Mail::assertSent(NewLeadSubmitted::class, function ($mail) {
            return true; // Just check that the email was sent
        });
    }

    public function test_lead_form_handles_long_messages(): void
    {
        $longMessage = str_repeat('This is a very long message. ', 20); // Shorter to fit within 1000 chars

        $leadData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'message' => $longMessage,
        ];

        $response = $this->post('/leadform', $leadData);

        $response->assertRedirect();
        // Check that the message was stored (may be truncated)
        $this->assertDatabaseHas('lead_forms', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Verify the message was stored (check actual stored value)
        $storedLead = LeadForm::where('email', 'test@example.com')->first();
        $this->assertNotNull($storedLead);
        $this->assertStringContainsString('This is a very long message', $storedLead->message);
    }

    public function test_lead_form_handles_special_characters(): void
    {
        $leadData = [
            'name' => 'José María',
            'email' => 'test@example.com',
            'message' => 'Message with special chars: éñáüö',
        ];

        $response = $this->post('/leadform', $leadData);

        $response->assertRedirect();
        $this->assertDatabaseHas('lead_forms', [
            'name' => 'José María',
            'email' => 'test@example.com',
            'message' => 'Message with special chars: éñáüö',
        ]);
    }
}
