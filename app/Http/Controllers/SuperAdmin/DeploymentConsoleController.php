<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BusinessSetting;
use App\Models\User;
use App\Support\ModuleRegistry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class DeploymentConsoleController extends Controller
{
    public function dashboard(): View
    {
        return view('super-admin.dashboard', [
            'modules' => ModuleRegistry::all(),
            'superAdminCount' => User::query()->where('is_super_admin', true)->count(),
            'administratorCount' => User::query()->whereIn('role', [User::ROLE_ADMINISTRATOR, User::ROLE_ADMINISTRATOR_LEGACY])->count(),
            'deploymentName' => BusinessSetting::get('deployment_name', config('app.name')),
            'deploymentEnvironment' => BusinessSetting::get('deployment_environment', config('app.env')),
            'selectedFrontend' => ModuleRegistry::selectedFrontend(),
            'guidedSetupCompleted' => BusinessSetting::get('super_admin_guided_setup_completed', false),
        ]);
    }

    public function edit(): View
    {
        return view('super-admin.deployment-console', $this->viewData());
    }

    public function guidedSetup(): View
    {
        return view('super-admin.guided-setup', $this->viewData());
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);

        ModuleRegistry::persist($validated);

        return redirect()->route('super-admin.deployment-console')
            ->with('success', 'Deployment console updated successfully.');
    }

    public function storeGuidedSetup(Request $request): RedirectResponse
    {
        $validated = $this->validatePayload($request);

        ModuleRegistry::persist($validated);
        BusinessSetting::set('super_admin_guided_setup_in_progress', true, 'boolean');

        return redirect()->route('super-admin.guided-setup')
            ->with('success', 'Super admin guided setup saved.');
    }

    public function complete(): RedirectResponse
    {
        BusinessSetting::set('super_admin_guided_setup_completed', true, 'boolean');
        BusinessSetting::set('super_admin_guided_setup_completed_at', now()->toDateTimeString(), 'string');

        return redirect()->route('super-admin.dashboard')
            ->with('success', 'Guided setup completed.');
    }

    public function updateSuperAdmin(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'is_super_admin' => ['required', 'boolean'],
        ]);

        if (! $validated['is_super_admin'] && $user->isSuperAdmin() && User::query()->where('is_super_admin', true)->count() <= 1) {
            return back()->with('error', 'At least one super admin must remain assigned.');
        }

        $user->forceFill([
            'is_super_admin' => $validated['is_super_admin'],
        ])->save();

        return back()->with('success', 'Super admin access updated for '.$user->email.'.');
    }

    private function validatePayload(Request $request): array
    {
        $frontendKeys = collect(ModuleRegistry::frontendOptions())->pluck('key')->all();
        $moduleKeys = array_keys(ModuleRegistry::definitions());

        return $request->validate([
            'deployment_name' => ['required', 'string', 'max:255'],
            'deployment_domain' => ['nullable', 'string', 'max:255'],
            'deployment_environment' => ['required', 'string', 'in:local,staging,production'],
            'frontend_module' => ['required', 'string', 'in:'.implode(',', $frontendKeys)],
            'enabled_modules' => ['nullable', 'array'],
            'enabled_modules.*' => ['string', 'in:'.implode(',', $moduleKeys)],
        ]);
    }

    private function viewData(): array
    {
        $tenantsTableExists = Schema::hasTable('tenants');
        $activityLogTableExists = Schema::hasTable('activity_logs');

        $usersQuery = User::query()->orderByDesc('is_super_admin')->orderBy('name');

        if ($tenantsTableExists) {
            $usersQuery->with('tenant');
        }

        $recentlyLoggedInUsers = collect();

        if ($activityLogTableExists) {
            $latestLoginSubquery = ActivityLog::query()
                ->selectRaw('user_id, MAX(created_at) as last_logged_in_at')
                ->whereIn('action', ['login', 'login_google'])
                ->groupBy('user_id');

            $recentLoginsQuery = User::query()
                ->select('users.*', 'latest_logins.last_logged_in_at')
                ->joinSub($latestLoginSubquery, 'latest_logins', function ($join): void {
                    $join->on('users.id', '=', 'latest_logins.user_id');
                })
                ->orderByDesc('latest_logins.last_logged_in_at')
                ->limit(100);

            if ($tenantsTableExists) {
                $recentLoginsQuery->with('tenant');
            }

            $recentlyLoggedInUsers = $recentLoginsQuery->get();
        }

        return [
            'modules' => ModuleRegistry::all(),
            'frontendOptions' => ModuleRegistry::frontendOptions(),
            'deploymentName' => BusinessSetting::get('deployment_name', config('app.name')),
            'deploymentDomain' => BusinessSetting::get('deployment_domain', config('app.url')),
            'deploymentEnvironment' => BusinessSetting::get('deployment_environment', config('app.env')),
            'selectedFrontend' => ModuleRegistry::selectedFrontend(),
            'guidedSetupCompleted' => BusinessSetting::get('super_admin_guided_setup_completed', false),
            'users' => $usersQuery->get(),
            'recentlyLoggedInUsers' => $recentlyLoggedInUsers,
        ];
    }
}
