<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        // Explicitly send email verification notification
        try {
            $user->sendEmailVerificationNotification();
            \Log::info('Verification email sent', ['user_id' => $user->id, 'email' => $user->email]);
        } catch (\Exception $e) {
            \Log::error('Failed to send verification email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        // Send welcome email (listeners automatically handle tracking)
        try {
            $companyName = config('business.company_name', config('app.company_name', 'smbgen'));
            \Mail::html(
                view('emails.welcome', ['user' => $user, 'companyName' => $companyName])->render(),
                function ($message) use ($user, $companyName) {
                    $message->to($user->email, $user->name)
                        ->subject("Welcome to {$companyName}!");
                }
            );
        } catch (\Exception $e) {
            \Log::error('Failed to send welcome email', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        Auth::login($user);

        // Redirect to verification notice if email not verified, otherwise to dashboard
        return redirect(route('verification.notice', absolute: false));
    }
}
