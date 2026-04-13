<?php

namespace Tests\Feature\Auth;

use App\Mail\AccountSecurityNoticeMail;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();
        Mail::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        // Case 2: reset notification sent + security notice mail sent.
        Notification::assertSentTo($user, ResetPassword::class);
        Mail::assertSent(AccountSecurityNoticeMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) && $mail->type === 'password_reset_requested';
        });
    }

    public function test_unregistered_email_receives_no_email(): void
    {
        Notification::fake();
        Mail::fake();

        // Case 1: email not registered — silent success, nothing sent.
        $response = $this->post('/forgot-password', ['email' => 'nobody@example.com']);

        Notification::assertNothingSent();
        Mail::assertNothingSent();

        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status');
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();
        Mail::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_google_only_account_receives_notice_mail_but_no_reset_link(): void
    {
        Notification::fake();
        Mail::fake();

        $user = User::factory()->create(['google_id' => '123456789']);

        $response = $this->post('/forgot-password', ['email' => $user->email]);

        // Case 3: No password reset notification — Google accounts don't use passwords.
        Notification::assertNothingSent();

        // Security notice mail sent explaining they must use Google to log in.
        Mail::assertSent(AccountSecurityNoticeMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email) && $mail->type === 'google_login_required';
        });

        // Response is identical to normal success — prevents user enumeration.
        $response->assertSessionHasNoErrors();
        $response->assertSessionHas('status');
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();
        Mail::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }
}
