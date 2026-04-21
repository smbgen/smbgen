@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Add Social Account</h1>
            <p class="admin-page-subtitle">Connect a LinkedIn, Facebook, or Instagram account</p>
        </div>
        <a href="{{ route('admin.social.accounts.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-error mb-6">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="max-w-2xl">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6">
            <form action="{{ route('admin.social.accounts.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="form-label">Platform <span class="text-red-500">*</span></label>
                    <select name="platform" class="form-input" required>
                        <option value="">Select a platform…</option>
                        <option value="facebook" {{ old('platform') === 'facebook' ? 'selected' : '' }}>
                            Facebook Page
                        </option>
                        <option value="instagram" {{ old('platform') === 'instagram' ? 'selected' : '' }}>
                            Instagram Business
                        </option>
                        <option value="linkedin" {{ old('platform') === 'linkedin' ? 'selected' : '' }}>
                            LinkedIn Page / Profile
                        </option>
                    </select>
                </div>

                <div>
                    <label class="form-label">Account / Page Name <span class="text-red-500">*</span></label>
                    <input type="text" name="account_name" value="{{ old('account_name') }}"
                           class="form-input" placeholder="e.g. My Company Page" required>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                        The public display name of the page or profile.
                    </p>
                </div>

                <div>
                    <label class="form-label">Profile URL</label>
                    <input type="url" name="account_url" value="{{ old('account_url') }}"
                           class="form-input" placeholder="https://www.facebook.com/yourpage">
                </div>

                <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg p-4 text-sm text-amber-900 dark:text-amber-200">
                    <i class="fas fa-info-circle mr-2"></i>
                    After adding the account, configure the OAuth tokens in
                    <strong>Environment Settings</strong> or connect via the platform's OAuth flow.
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('admin.social.accounts.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-plug mr-2"></i>Add Account
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
