@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    @if(session('success'))
        <div class="mb-4 rounded border border-green-300 bg-green-50 px-4 py-2 text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 rounded border border-red-300 bg-red-50 px-4 py-2 text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300">
            {{ session('error') }}
        </div>
    @endif

    <div class="mb-6">
        <h2 class="mb-2 text-2xl font-bold text-gray-900 dark:text-white">Business Settings</h2>
        <p class="text-gray-600 dark:text-gray-400">Configure {{ config('app.name', 'smbgen') }} for your business needs</p>
    </div>

    <form method="POST" action="{{ route('admin.business_settings.update') }}" class="space-y-8">
        @csrf
        @method('PATCH')

        <!-- Branding & Identity -->
        <section class="rounded-lg border border-gray-300 dark:border-gray-800 bg-white dark:bg-gray-900/50 p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-200">Branding & Identity</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Configure your business name and company branding.</p>

            <div class="space-y-4">
                <div>
                    <label for="app_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Application Name</label>
                    <input id="app_name" type="text" name="app_name" value="{{ old('app_name', $settings['app_name'] ?? config('app.name', 'smbgen')) }}" class="block w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                        Appears in page titles and navigation. 
                        <span class="text-blue-700 dark:text-blue-400">✓ Syncs to <code class="rounded bg-gray-200 px-1 dark:bg-gray-700">APP_NAME</code> in .env file.</span>
                    </p>
                    @error('app_name')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="company_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Company Name</label>
                    <input id="company_name" type="text" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? config('business.company_name', 'smbgen')) }}" class="block w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500" required>
                    <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">
                        Your business name used in emails, bookings, and public pages. 
                        <span class="text-blue-700 dark:text-blue-400">✓ Syncs to <code class="rounded bg-gray-200 px-1 dark:bg-gray-700">BUSINESS_COMPANY_NAME</code> in .env file.</span>
                    </p>
                    @error('company_name')
                        <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </section>

        <!-- Admin Email Notifications -->
        <section class="rounded-lg border border-gray-300 dark:border-gray-800 bg-white dark:bg-gray-900/50 p-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-900 dark:text-gray-200">📧 Admin Email Notifications</h3>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">Configure which administrators receive email notifications for bookings and lead submissions.</p>

            @if($adminUsers->isEmpty())
                <div class="bg-yellow-100 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-800 rounded-lg p-4">
                    <p class="text-sm text-yellow-800 dark:text-yellow-200">No administrators found.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($adminUsers as $admin)
                        <div class="bg-gray-100 dark:bg-gray-800/50 rounded-lg p-4 border border-gray-300 dark:border-gray-700">
                            <div class="flex items-start justify-between mb-3">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-gray-200">{{ $admin->name }}</h4>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $admin->email }}</p>
                                </div>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        name="admin_notifications[{{ $admin->id }}][notify_on_new_leads]" 
                                        value="1"
                                        {{ old("admin_notifications.{$admin->id}.notify_on_new_leads", $admin->notify_on_new_leads) ? 'checked' : '' }}
                                        class="rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-blue-600 focus:ring-blue-500 focus:ring-offset-white dark:focus:ring-offset-gray-900"
                                    >
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Notify on new lead submissions</span>
                                </label>
                                
                                <label class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        name="admin_notifications[{{ $admin->id }}][notify_on_new_bookings]" 
                                        value="1"
                                        {{ old("admin_notifications.{$admin->id}.notify_on_new_bookings", $admin->notify_on_new_bookings) ? 'checked' : '' }}
                                        class="rounded border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-blue-600 focus:ring-blue-500 focus:ring-offset-white dark:focus:ring-offset-gray-900"
                                    >
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Notify on new bookings</span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4 rounded-lg border border-blue-300 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
                    <div class="flex items-start">
                        <svg class="mr-3 mt-0.5 h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div>
                            <h4 class="mb-1 text-sm font-medium text-blue-900 dark:text-blue-300">Email Notification Details</h4>
                            <ul class="space-y-1 text-sm text-blue-800 dark:text-blue-200/80">
                                <li>• <strong>New Bookings:</strong> Includes all form responses, appointment details, and Google Meet links</li>
                                <li>• <strong>New Leads:</strong> Includes contact form submissions from CMS pages</li>
                                <li>• Custom form fields are automatically included in both notification types</li>
                            </ul>
                        </div>
                    </div>
                </div>
            @endif
        </section>

        <!-- Theme Colors - NOT IMPLEMENTED -->
        <section class="rounded-lg border border-red-300 bg-white p-6 dark:border-red-900/50 dark:bg-gray-900/50">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-200">Theme Colors</h3>
            <div class="rounded-lg border border-red-300 bg-red-50 p-4 dark:border-red-800 dark:bg-red-900/20">
                <div class="flex items-start">
                    <svg class="mr-3 mt-0.5 h-5 w-5 flex-shrink-0 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <h4 class="mb-1 text-sm font-medium text-red-900 dark:text-red-300">Feature Not Implemented</h4>
                        <p class="mb-2 text-sm text-red-800 dark:text-red-200/80">
                            Theme colors are currently stored in the database but not applied anywhere in the application. 
                            This feature requires implementation of CSS variable injection or Tailwind config updates.
                        </p>
                        <p class="text-xs text-red-700 dark:text-red-300/60">
                            <strong>Workaround:</strong> Edit <code class="rounded bg-red-100 px-1 dark:bg-gray-700">config/business.php</code> 
                            and set .env variables like <code class="rounded bg-red-100 px-1 dark:bg-gray-700">BUSINESS_PRIMARY_COLOR</code>.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Google Workspace - NOT IMPLEMENTED -->
        <section class="rounded-lg border border-yellow-300 bg-white p-6 dark:border-yellow-900/50 dark:bg-gray-900/50">
            <h3 class="mb-4 text-lg font-semibold text-gray-900 dark:text-gray-200">Google Workspace Domain Restriction</h3>
            <div class="rounded-lg border border-yellow-300 bg-yellow-50 p-4 dark:border-yellow-800 dark:bg-yellow-900/20">
                <div class="flex items-start">
                    <svg class="mr-3 mt-0.5 h-5 w-5 flex-shrink-0 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    <div>
                        <h4 class="mb-1 text-sm font-medium text-yellow-900 dark:text-yellow-300">Feature Not Implemented</h4>
                        <p class="text-sm text-yellow-800 dark:text-yellow-200/80">
                            Google Workspace domain restriction is stored but not enforced during authentication. 
                            This requires implementation in the Google OAuth callback to validate user email domains.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Information Notice -->
        <div class="rounded-lg border border-blue-300 bg-blue-50 p-4 dark:border-blue-800 dark:bg-blue-900/20">
            <div class="flex items-start">
                <svg class="mr-3 mt-0.5 h-5 w-5 flex-shrink-0 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="mb-1 text-sm font-medium text-blue-900 dark:text-blue-300">Business Logic Settings Moved</h4>
                    <p class="text-sm text-blue-800 dark:text-blue-200/80">
                        Invoice and booking behavior settings (automatic invoicing, deposits, report pricing, etc.) have been removed from this page. 
                        They will be available in the <strong>Invoices Settings</strong> section when it's implemented.
                    </p>
                </div>
            </div>
        </div>

        <div class="pt-2">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-6 rounded-md transition-colors">
                <i class="fas fa-save mr-2"></i>Save Settings
            </button>
        </div>
    </form>

</div>
@endsection
