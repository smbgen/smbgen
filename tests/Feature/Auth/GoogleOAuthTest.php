<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as SocialiteUser;
use Tests\TestCase;

class GoogleOAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_redirect_route_initiates_redirect(): void
    {
        Socialite::shouldReceive('driver->redirect')
            ->once()
            ->andReturn(redirect()->away('https://accounts.google.com/o/oauth2/auth?fake'));

        $response = $this->get(route('auth.google.redirect'));

        $response->assertStatus(302);
    }

    public function test_google_callback_creates_new_user_and_logs_in(): void
    {
        $socialiteUser = $this->mockSocialiteUser(
            id: '12345',
            name: 'Alex Ramsey',
            email: 'alex@example.com',
        );

        Socialite::shouldReceive('driver->user')
            ->once()
            ->andReturn($socialiteUser);

        $response = $this->get(route('auth.google.callback'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'alex@example.com',
            'google_id' => '12345',
        ]);
        $this->assertDatabaseHas('clients', [
            'email' => 'alex@example.com',
            'name' => 'Alex Ramsey',
            'is_active' => true,
        ]);
        $user = User::where('email', 'alex@example.com')->firstOrFail();
        $this->assertTrue($user->hasVerifiedEmail());
        $response->assertRedirect('/dashboard');
    }

    public function test_google_callback_logs_in_existing_user(): void
    {
        $user = User::factory()->unverified()->create([
            'email' => 'existing@example.com',
            'google_id' => '99999',
        ]);

        $socialiteUser = $this->mockSocialiteUser(
            id: '99999',
            name: $user->name,
            email: $user->email,
        );

        Socialite::shouldReceive('driver->user')
            ->once()
            ->andReturn($socialiteUser);

        $response = $this->get(route('auth.google.callback'));

        $this->assertAuthenticated();
        $this->assertDatabaseHas('clients', [
            'email' => 'existing@example.com',
            'name' => $user->name,
            'is_active' => true,
        ]);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect('/dashboard');
    }

    public function test_google_callback_redirects_admin_to_admin_dashboard(): void
    {
        $user = User::factory()->admin()->create([
            'email' => 'admin@example.com',
            'google_id' => '77777',
        ]);

        $socialiteUser = $this->mockSocialiteUser(
            id: '77777',
            name: $user->name,
            email: $user->email,
        );

        Socialite::shouldReceive('driver->user')
            ->once()
            ->andReturn($socialiteUser);

        $response = $this->get(route('auth.google.callback'));

        $response->assertRedirect('/admin/dashboard');
    }

    public function test_google_callback_redirects_to_login_on_failure(): void
    {
        Socialite::shouldReceive('driver->user')
            ->once()
            ->andThrow(new \Exception('OAuth token mismatch'));

        $response = $this->get(route('auth.google.callback'));

        $this->assertGuest();
        $response->assertRedirect(route('login', absolute: false));
        $response->assertSessionHasErrors('email');
    }

    public function test_register_page_contains_google_oauth_button(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('Continue with Google');
        $response->assertSee(route('auth.google.redirect'));
    }

    private function mockSocialiteUser(string $id, string $name, string $email): SocialiteUser
    {
        $user = \Mockery::mock(SocialiteUser::class);
        $user->shouldReceive('getId')->andReturn($id);
        $user->shouldReceive('getName')->andReturn($name);
        $user->shouldReceive('getEmail')->andReturn($email);

        return $user;
    }
}
