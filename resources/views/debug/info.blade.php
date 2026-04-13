@extends('layouts.guest')

@section('title', 'Debug Information')

@section('content')
<div class="container py-8">
    <div class="bg-blue-50 dark:bg-blue-900 rounded-lg p-8 text-blue-900 dark:text-white border border-blue-200 dark:border-blue-700 mb-6">
        <h1 class="text-4xl font-bold mb-2">🛠️ Debug Mode Active</h1>
        <p class="text-blue-700 dark:text-blue-200">Application is running in debug mode. These routes are only available when APP_DEBUG=true</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Error Page Previews -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-exclamation-triangle mr-2"></i>Error Page Previews
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">View error pages directly without triggering actual errors</p>
            <div class="space-y-2">
                <a href="{{ route('debug.error.403') }}" class="block bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded transition-colors">
                    <i class="fas fa-ban mr-2"></i>403 Forbidden Page
                </a>
                <a href="{{ route('debug.error.404') }}" class="block bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded transition-colors">
                    <i class="fas fa-question-circle mr-2"></i>404 Not Found Page
                </a>
                <a href="{{ route('debug.error.405') }}" class="block bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded transition-colors">
                    <i class="fas fa-times-circle mr-2"></i>405 Method Not Allowed
                </a>
                <a href="{{ route('debug.error.500') }}" class="block bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded transition-colors">
                    <i class="fas fa-server mr-2"></i>500 Server Error Page
                </a>
                <a href="{{ route('debug.error.503') }}" class="block bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded transition-colors">
                    <i class="fas fa-wrench mr-2"></i>503 Service Unavailable
                </a>
            </div>
        </div>

        <!-- Actual Error Testing -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-bug mr-2"></i>Actual Error Testing
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-4">Test actual error handling (will trigger real errors)</p>
            <div class="space-y-2">
                <a href="{{ route('debug.test.403') }}" class="block bg-red-700 hover:bg-red-800 text-white px-4 py-2 rounded transition-colors">
                    <i class="fas fa-shield-alt mr-2"></i>Trigger 403 Error
                </a>
                <a href="{{ route('debug.test.404') }}" class="block bg-yellow-700 hover:bg-yellow-800 text-white px-4 py-2 rounded transition-colors">
                    <i class="fas fa-search mr-2"></i>Trigger 404 Error
                </a>
                <a href="{{ route('debug.test.405') }}" class="block bg-orange-700 hover:bg-orange-800 text-white px-4 py-2 rounded transition-colors">
                    <i class="fas fa-times-circle mr-2"></i>Trigger 405 Error
                </a>
                <a href="{{ route('debug.test.500') }}" class="block bg-purple-700 hover:bg-purple-800 text-white px-4 py-2 rounded transition-colors">
                    <i class="fas fa-bomb mr-2"></i>Trigger 500 Error
                </a>
                <a href="{{ route('debug.test.503') }}" class="block bg-indigo-700 hover:bg-indigo-800 text-white px-4 py-2 rounded transition-colors">
                    <i class="fas fa-wrench mr-2"></i>Trigger 503 Error
                </a>
            </div>
        </div>

        <!-- Application Info -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-info-circle mr-2"></i>Application Info
            </h2>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Environment:</span>
                    <span class="text-gray-900 dark:text-gray-200 font-mono">{{ app()->environment() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Debug Mode:</span>
                    <span class="text-green-700 dark:text-green-400 font-mono">{{ config('app.debug') ? 'true' : 'false' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">Laravel Version:</span>
                    <span class="text-gray-900 dark:text-gray-200 font-mono">{{ app()->version() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600 dark:text-gray-400">PHP Version:</span>
                    <span class="text-gray-900 dark:text-gray-200 font-mono">{{ PHP_VERSION }}</span>
                </div>
            </div>
        </div>

        <!-- Quick Navigation -->
        <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700 shadow-sm">
            <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4">
                <i class="fas fa-compass mr-2"></i>Quick Navigation
            </h2>
            <div class="space-y-2">
                <a href="/" class="block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded transition-colors">
                    <i class="fas fa-home mr-2"></i>Home Page
                </a>
                @auth
                    @if(auth()->user()->isAdministrator())
                        <a href="{{ route('admin.dashboard') }}" class="block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition-colors">
                            <i class="fas fa-tachometer-alt mr-2"></i>Admin Dashboard
                        </a>
                        <a href="{{ route('admin.google-oauth') }}" class="block bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded transition-colors">
                            <i class="fas fa-users-cog mr-2"></i>Social Accounts
                        </a>
                    @else
                        <a href="{{ route('dashboard') }}" class="block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition-colors">
                            <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <div class="mt-8 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-300 dark:border-yellow-500 text-yellow-900 dark:text-yellow-300 px-6 py-4 rounded-lg">
        <div class="flex items-center">
            <i class="fas fa-exclamation-triangle mr-3 text-lg"></i>
            <div>
                <strong>Warning:</strong> These debug routes are only available when APP_DEBUG=true in your .env file.
                Make sure to set APP_DEBUG=false in production environments.
            </div>
        </div>
    </div>
</div>
@endsection