<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of super-admin users
     */
    public function index()
    {
        if (! auth()->user() || ! auth()->user()->isSuperAdmin()) {
            abort(403, 'Forbidden');
        }

        $users = User::where('is_super_admin', true)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('super-admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new super-admin user
     */
    public function create()
    {
        if (! auth()->user() || ! auth()->user()->isSuperAdmin()) {
            abort(403, 'Forbidden');
        }

        return view('super-admin.users.create');
    }

    /**
     * Store a newly created super-admin user
     */
    public function store(Request $request)
    {
        if (! auth()->user() || ! auth()->user()->isSuperAdmin()) {
            abort(403, 'Forbidden');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['is_super_admin'] = true;
        $data['role'] = 'user'; // Set a default role, though is_super_admin takes precedence

        User::create($data);

        return redirect()->route('super-admin.users.index')->with('success', 'Super admin user created successfully.');
    }

    /**
     * Show the form for editing a super-admin user
     */
    public function edit(User $user)
    {
        if (! auth()->user() || ! auth()->user()->isSuperAdmin()) {
            abort(403, 'Forbidden');
        }

        // Ensure we're only editing super-admin users
        if (! $user->isSuperAdmin()) {
            abort(404, 'User not found.');
        }

        return view('super-admin.users.edit', compact('user'));
    }

    /**
     * Update the specified super-admin user
     */
    public function update(Request $request, User $user)
    {
        \Log::info('SuperAdmin UserController update method called', [
            'user_id' => $user->id,
            'request_method' => $request->method(),
            'request_data' => $request->except(['password', 'password_confirmation']),
        ]);

        if (! auth()->user() || ! auth()->user()->isSuperAdmin()) {
            abort(403, 'Forbidden');
        }

        // Ensure we're only editing super-admin users
        if (! $user->isSuperAdmin()) {
            abort(404, 'User not found.');
        }

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
        ];

        // Add password validation only if password is provided
        if ($request->filled('password')) {
            $rules['password'] = ['required', 'string', 'min:8', 'confirmed'];
        }

        $data = $request->validate($rules);

        // Update basic user data
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('super-admin.users.index')->with('success', 'Super admin user updated successfully.');
    }

    /**
     * Remove the specified super-admin user
     */
    public function destroy(User $user)
    {
        \Log::warning('SuperAdmin UserController destroy method called', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'deleted_by' => auth()->id(),
            'request_method' => request()->method(),
        ]);

        if (! auth()->user() || ! auth()->user()->isSuperAdmin()) {
            abort(403, 'Forbidden');
        }

        // Ensure we're only deleting super-admin users
        if (! $user->isSuperAdmin()) {
            abort(404, 'User not found.');
        }

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('super-admin.users.index')->with('success', 'Super admin user deleted successfully.');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request, User $user)
    {
        if (! auth()->user() || ! auth()->user()->isSuperAdmin()) {
            abort(403, 'Forbidden');
        }

        // Ensure we're only updating super-admin users
        if (! $user->isSuperAdmin()) {
            abort(404, 'User not found.');
        }

        logger('updatePassword hit for super-admin user ID: '.$user->id);
        $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Password updated for '.$user->email);
    }

    /**
     * Manually verify a super-admin user's email
     */
    public function verify(User $user)
    {
        if (! auth()->user() || ! auth()->user()->isSuperAdmin()) {
            abort(403, 'Forbidden');
        }

        // Ensure we're only verifying super-admin users
        if (! $user->isSuperAdmin()) {
            abort(404, 'User not found.');
        }

        if ($user->hasVerifiedEmail()) {
            return back()->with('status', $user->email.' is already verified.');
        }

        $user->markEmailAsVerified();

        return back()->with('success', 'Email verified for '.$user->email);
    }

    /**
     * Manually unverify a super-admin user's email
     */
    public function unverify(User $user)
    {
        if (! auth()->user() || ! auth()->user()->isSuperAdmin()) {
            abort(403, 'Forbidden');
        }

        // Ensure we're only unverifying super-admin users
        if (! $user->isSuperAdmin()) {
            abort(404, 'User not found.');
        }

        if (! $user->hasVerifiedEmail()) {
            return back()->with('status', $user->email.' is not verified.');
        }

        $user->update(['email_verified_at' => null]);

        return back()->with('success', 'Email verification removed for '.$user->email);
    }
}
