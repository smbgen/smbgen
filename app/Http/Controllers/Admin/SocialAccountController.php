<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSocialAccountRequest;
use App\Models\SocialAccount;
use App\Services\ActivityLogger;

class SocialAccountController extends Controller
{
    /**
     * List all connected social accounts.
     */
    public function index()
    {
        $accounts = SocialAccount::with('user')
            ->orderBy('platform')
            ->orderBy('account_name')
            ->get();

        return view('admin.social.accounts.index', compact('accounts'));
    }

    /**
     * Show form to manually add / document a social account.
     */
    public function create()
    {
        return view('admin.social.accounts.create');
    }

    /**
     * Store a new social account record (manual entry – OAuth flow is separate).
     */
    public function store(StoreSocialAccountRequest $request)
    {
        $account = SocialAccount::create([
            'user_id' => auth()->id(),
            'platform' => $request->platform,
            'account_name' => $request->account_name,
            'account_url' => $request->account_url,
            'active' => true,
            'connection_status' => SocialAccount::STATUS_CONNECTED,
        ]);

        ActivityLogger::log('social_account_created', "Connected {$account->platformLabel()} account: {$account->account_name}", $account);

        return redirect()->route('admin.social.accounts.index')
            ->with('success', "{$account->platformLabel()} account connected successfully.");
    }

    /**
     * Toggle the active flag for an account.
     */
    public function toggle(SocialAccount $account)
    {
        $account->update(['active' => ! $account->active]);

        $state = $account->active ? 'enabled' : 'disabled';
        ActivityLogger::log('social_account_toggled', "Account {$state}: {$account->account_name}", $account);

        return back()->with('success', "Account {$state}.");
    }

    /**
     * Remove a connected account.
     */
    public function destroy(SocialAccount $account)
    {
        $label = $account->platformLabel();
        $name = $account->account_name;
        $account->delete();

        ActivityLogger::log('social_account_deleted', "Deleted {$label} account: {$name}");

        return redirect()->route('admin.social.accounts.index')
            ->with('success', "{$label} account removed.");
    }
}
