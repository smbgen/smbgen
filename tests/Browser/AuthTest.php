<?php

namespace Tests\Browser;

use App\Models\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class AuthTest extends DuskTestCase
{
    public function test_user_can_view_login_page(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->assertSee('Log in')
                ->assertPresent('input[name="email"]')
                ->assertPresent('input[name="password"]');
        });
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'dusk-login@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/login')
                ->type('email', $user->email)
                ->type('password', 'password')
                ->press('Log in')
                ->assertPathIs('/dashboard');
        });

        $user->delete();
    }

    public function test_user_sees_error_on_invalid_credentials(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/login')
                ->type('email', 'nobody@example.com')
                ->type('password', 'wrong-password')
                ->press('Log in')
                ->assertSee('credentials do not match');
        });
    }

    public function test_user_can_logout(): void
    {
        $user = User::factory()->create();

        $this->browse(function (Browser $browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/dashboard')
                ->click('[data-dusk="user-menu"]')
                ->clickLink('Log Out')
                ->assertPathIs('/');
        });

        $user->delete();
    }

    public function test_registration_page_is_accessible(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->assertSee('Register')
                ->assertPresent('input[name="name"]')
                ->assertPresent('input[name="email"]')
                ->assertPresent('input[name="password"]');
        });
    }

    public function test_unauthenticated_user_is_redirected_from_admin(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/admin/dashboard')
                ->assertPathIs('/login');
        });
    }
}
