<?php

namespace Tests\Feature;

use App\Mail\NewLeadSubmitted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LeadFormMailTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_sends_email_when_lead_form_is_submitted()
    {
        Mail::fake();

        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'message' => 'Test message from unit test.',
        ];

        $response = $this->post('/leadform', $data);

        $response->assertRedirect(); // or assertSessionHas('success')
        Mail::assertSent(NewLeadSubmitted::class, function ($mail) use ($data) {
            return $mail->formData['email'] === $data['email'];
        });
    }
}
