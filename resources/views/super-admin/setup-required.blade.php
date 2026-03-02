@extends('layouts.super-admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-yellow-600/20 border border-yellow-600/30 rounded-lg p-6 mb-8">
        <div class="flex items-start gap-4">
            <i class="fas fa-exclamation-triangle text-yellow-400 text-3xl mt-1"></i>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-yellow-400 mb-2">Tenancy System Setup Required</h1>
                <p class="text-gray-300">The multi-tenancy system needs to be initialized before you can manage tenants.</p>
            </div>
        </div>
    </div>

    @if(isset($error))
        <div class="bg-red-600/20 border border-red-600/30 rounded-lg p-4 mb-8">
            <div class="flex items-center gap-2 mb-2">
                <i class="fas fa-times-circle text-red-400"></i>
                <span class="text-red-400 font-semibold">Error Details:</span>
            </div>
            <pre class="text-gray-300 text-sm font-mono overflow-x-auto">{{ $error }}</pre>
        </div>
    @endif

    <div class="bg-gray-800 rounded-lg border border-gray-700 p-8">
        <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
            <i class="fas fa-rocket text-blue-400"></i>
            Setup Instructions
        </h2>

        <div class="space-y-6">
            <!-- Step 1 -->
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">1</div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-white mb-2">Run Tenancy Migrations</h3>
                    <p class="text-gray-300 mb-3">Execute the migrations to create the required tenancy tables:</p>
                    <div class="bg-gray-900 rounded-lg p-4 font-mono text-sm text-green-400 border border-gray-700">
                        php artisan migrate --path=database/migrations/tenant
                    </div>
                    <p class="text-gray-400 text-sm mt-2">This creates the <code class="bg-gray-900 px-2 py-1 rounded">tenants</code> and <code class="bg-gray-900 px-2 py-1 rounded">domains</code> tables.</p>
                </div>
            </div>

            <!-- Step 2 -->
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">2</div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-white mb-2">Verify Tables Created</h3>
                    <p class="text-gray-300 mb-3">Check that the tables were created successfully:</p>
                    <div class="bg-gray-900 rounded-lg p-4 font-mono text-sm text-green-400 border border-gray-700">
                        php artisan tinker<br>
                        <span class="text-gray-500">>>> </span>Schema::hasTable('tenants')<br>
                        <span class="text-gray-500">>>> </span>Schema::hasTable('domains')
                    </div>
                    <p class="text-gray-400 text-sm mt-2">Both should return <code class="bg-gray-900 px-2 py-1 rounded text-green-400">true</code></p>
                </div>
            </div>

            <!-- Step 3 -->
            <div class="flex gap-4">
                <div class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center font-bold">3</div>
                <div class="flex-1">
                    <h3 class="text-lg font-semibold text-white mb-2">Refresh This Page</h3>
                    <p class="text-gray-300 mb-3">Once migrations are complete, refresh this page:</p>
                    <a href="{{ route('super-admin.dashboard') }}" 
                       class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold transition-colors">
                        <i class="fas fa-sync-alt"></i>
                        Refresh Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Resources -->
    <div class="mt-8 bg-gray-800 rounded-lg border border-gray-700 p-6">
        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-book text-purple-400"></i>
            Additional Resources
        </h2>
        
        <div class="space-y-4">
            <div>
                <h4 class="text-white font-semibold mb-2">Create a Test Tenant</h4>
                <p class="text-gray-300 text-sm mb-2">After setup, you can create a test tenant from the command line:</p>
                <div class="bg-gray-900 rounded-lg p-3 font-mono text-sm text-green-400 border border-gray-700">
                    php artisan tenants:create test-company
                </div>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-2">View All Tenants</h4>
                <p class="text-gray-300 text-sm mb-2">List all tenants in the system:</p>
                <div class="bg-gray-900 rounded-lg p-3 font-mono text-sm text-green-400 border border-gray-700">
                    php artisan tenants:list
                </div>
            </div>

            <div>
                <h4 class="text-white font-semibold mb-2">Documentation</h4>
                <a href="https://tenancyforlaravel.com/docs/v3/quickstart" target="_blank" 
                   class="text-blue-400 hover:text-blue-300 text-sm flex items-center gap-1">
                    Laravel Tenancy Documentation
                    <i class="fas fa-external-link-alt text-xs"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Troubleshooting -->
    <div class="mt-8 bg-gray-800 rounded-lg border border-gray-700 p-6">
        <h2 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
            <i class="fas fa-wrench text-orange-400"></i>
            Troubleshooting
        </h2>
        
        <div class="space-y-3 text-sm">
            <div class="flex gap-3">
                <i class="fas fa-question-circle text-gray-400 mt-0.5"></i>
                <div>
                    <p class="text-white font-medium">Migration files not found?</p>
                    <p class="text-gray-300">Check if <code class="bg-gray-900 px-2 py-1 rounded">database/migrations/tenant</code> directory exists with migration files.</p>
                </div>
            </div>
            
            <div class="flex gap-3">
                <i class="fas fa-question-circle text-gray-400 mt-0.5"></i>
                <div>
                    <p class="text-white font-medium">Permission errors?</p>
                    <p class="text-gray-300">Ensure your database user has CREATE TABLE permissions.</p>
                </div>
            </div>
            
            <div class="flex gap-3">
                <i class="fas fa-question-circle text-gray-400 mt-0.5"></i>
                <div>
                    <p class="text-white font-medium">Still seeing errors?</p>
                    <p class="text-gray-300">Check <code class="bg-gray-900 px-2 py-1 rounded">storage/logs/laravel.log</code> for detailed error messages.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
