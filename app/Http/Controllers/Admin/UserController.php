<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of users
     */
    public function index()
    {
        $admin = auth()->user();

        if (! $admin || ! $admin->isAdministrator()) {
            abort(403, 'Forbidden');
        }

        $users = $this->tenantScopedUsersQuery($admin)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        if (! auth()->user() || ! auth()->user()->isAdministrator()) {
            abort(403, 'Forbidden');
        }

        return view('admin.users.create');
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $admin = auth()->user();

        if (! $admin || ! $admin->isAdministrator()) {
            abort(403, 'Forbidden');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['required', 'in:user,tenant_admin,company_administrator'],
        ]);

        $data['password'] = Hash::make($data['password']);
        $data['tenant_id'] = $admin->tenant_id;

        User::create($data);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing a user
     */
    public function edit(User $user)
    {
        $admin = auth()->user();

        if (! $admin || ! $admin->isAdministrator()) {
            abort(403, 'Forbidden');
        }

        $this->authorizeTenantUserAccess($admin, $user);

        return view('admin.users.edit', compact('user'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        \Log::info('UserController update method called', [
            'user_id' => $user->id,
            'request_method' => $request->method(),
            'request_data' => $request->except(['password', 'password_confirmation']),
        ]);

        $admin = auth()->user();

        if (! $admin || ! $admin->isAdministrator()) {
            abort(403, 'Forbidden');
        }

        $this->authorizeTenantUserAccess($admin, $user);

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:user,client,tenant_admin,company_administrator'],
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
            'role' => $data['role'],
        ]);

        // Update password if provided
        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user)
    {
        \Log::warning('UserController destroy method called', [
            'user_id' => $user->id,
            'user_email' => $user->email,
            'deleted_by' => auth()->id(),
            'request_method' => request()->method(),
        ]);

        $admin = auth()->user();

        if (! $admin || ! $admin->isAdministrator()) {
            abort(403, 'Forbidden');
        }

        $this->authorizeTenantUserAccess($admin, $user);

        // Prevent deleting yourself
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully.');
    }

    public function updatePassword(Request $request, User $user)
    {
        $admin = auth()->user();

        if (! $admin || ! $admin->isAdministrator()) {
            abort(403, 'Forbidden');
        }

        $this->authorizeTenantUserAccess($admin, $user);

        logger('updatePassword hit for user ID: '.$user->id);
        $request->validate([
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'Password updated for '.$user->email);
    }

    /**
     * Elevate a user to company administrator
     */
    public function elevate(Request $request, User $user)
    {
        // Only allow existing administrators to perform elevation
        $admin = auth()->user();

        if (! $admin || ! $admin->isAdministrator()) {
            abort(403, 'Forbidden');
        }

        $this->authorizeTenantUserAccess($admin, $user);

        // Prevent elevating the current user (no-op) or demoting if already admin
        if ($user->isAdministrator()) {
            return back()->with('status', $user->email.' is already an administrator.');
        }

        $user->update(['role' => User::ROLE_ADMINISTRATOR]);

        return back()->with('status', 'Elevated '.$user->email.' to company administrator.');
    }

    /**
     * Manually verify a user's email
     */
    public function verify(User $user)
    {
        $admin = auth()->user();

        if (! $admin || ! $admin->isAdministrator()) {
            abort(403, 'Forbidden');
        }

        $this->authorizeTenantUserAccess($admin, $user);

        if ($user->hasVerifiedEmail()) {
            return back()->with('status', $user->email.' is already verified.');
        }

        $user->markEmailAsVerified();

        return back()->with('success', 'Email verified for '.$user->email);
    }

    /**
     * Manually unverify a user's email
     */
    public function unverify(User $user)
    {
        $admin = auth()->user();

        if (! $admin || ! $admin->isAdministrator()) {
            abort(403, 'Forbidden');
        }

        $this->authorizeTenantUserAccess($admin, $user);

        if (! $user->hasVerifiedEmail()) {
            return back()->with('status', $user->email.' is not verified.');
        }

        $user->update(['email_verified_at' => null]);

        return back()->with('success', 'Email verification removed for '.$user->email);
    }

    private function tenantScopedUsersQuery(User $admin)
    {
        $query = User::query();

        if ($admin->tenant_id) {
            return $query->where('tenant_id', $admin->tenant_id);
        }

        return $query->whereNull('tenant_id');
    }

    private function authorizeTenantUserAccess(User $admin, User $targetUser): void
    {
        if ((string) $admin->tenant_id !== (string) $targetUser->tenant_id) {
            abort(403, 'Forbidden');
        }
    }
}
