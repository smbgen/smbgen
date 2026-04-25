<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DemoController extends Controller
{
    public const DEMO_ADMIN_EMAIL = 'demo-admin@demo.local';

    public const DEMO_CLIENT_EMAIL = 'demo-client@demo.local';

    /**
     * Show the public demo landing page.
     */
    public function landing(): View
    {
        abort_unless(config('app.demo_mode'), 404);

        return view('demo.landing');
    }

    /**
     * Auto-login as the demo user for the given role and redirect to the appropriate area.
     */
    public function login(Request $request, string $role): RedirectResponse
    {
        abort_unless(config('app.demo_mode'), 404);
        $email = match ($role) {
            'admin' => self::DEMO_ADMIN_EMAIL,
            'client' => self::DEMO_CLIENT_EMAIL,
            default => null,
        };

        if (! $email) {
            return redirect()->route('demo.landing');
        }

        $user = User::where('email', $email)->first();

        if (! $user) {
            return redirect()->route('demo.landing')->withErrors([
                'demo' => 'Demo accounts are not set up yet. Please run the demo seeder.',
            ]);
        }

        Auth::login($user);

        $request->session()->regenerate();

        return match ($role) {
            'admin' => redirect()->route('admin.dashboard'),
            default => redirect()->route('dashboard'),
        };
    }
}
