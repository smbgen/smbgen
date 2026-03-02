@extends('layouts.cms')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-blue-900 to-gray-900 flex items-center justify-center p-4">
    <div class="max-w-4xl mx-auto text-center">
        <h1 class="text-5xl md:text-7xl font-bold text-white mb-6">
            Welcome to {{ config('app.name', 'smbgen') }}
        </h1>
        
        <p class="text-xl md:text-2xl text-gray-300 mb-8">
            Your landing page is ready to be customized
        </p>

        <div class="bg-white/10 backdrop-blur-sm rounded-lg p-8 border border-white/20 mb-8">
            <h2 class="text-2xl font-semibold text-white mb-4">
                <i class="fas fa-rocket mr-2"></i>Get Started
            </h2>
            <p class="text-gray-300 mb-6">
                Create a CMS page with the slug "home" to customize this landing page, or disable the Home Landing feature flag.
            </p>
            
            @auth
                @if(auth()->user()->role === 'company_administrator')
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="{{ route('admin.cms.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                            <i class="fas fa-edit mr-2"></i>Create Landing Page
                        </a>
                        <a href="{{ route('admin.environment_settings.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                            <i class="fas fa-cog mr-2"></i>Environment Settings
                        </a>
                        <a href="{{ route('admin.dashboard') }}" class="bg-gray-700 hover:bg-gray-800 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                            <i class="fas fa-dashboard mr-2"></i>Dashboard
                        </a>
                    </div>
                @endif
            @else
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="{{ route('login') }}" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors">
                        <i class="fas fa-sign-in-alt mr-2"></i>Login
                    </a>
                </div>
            @endauth
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-left">
            <div class="bg-white/5 backdrop-blur-sm rounded-lg p-6 border border-white/10">
                <div class="text-blue-400 text-3xl mb-4">
                    <i class="fas fa-paintbrush"></i>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Customizable</h3>
                <p class="text-gray-400">Create custom pages with your own branding, colors, and content using the CMS editor.</p>
            </div>

            <div class="bg-white/5 backdrop-blur-sm rounded-lg p-6 border border-white/10">
                <div class="text-blue-400 text-3xl mb-4">
                    <i class="fas fa-users"></i>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Client Management</h3>
                <p class="text-gray-400">Powerful tools to manage your clients, appointments, and communications all in one place.</p>
            </div>

            <div class="bg-white/5 backdrop-blur-sm rounded-lg p-6 border border-white/10">
                <div class="text-blue-400 text-3xl mb-4">
                    <i class="fas fa-rocket"></i>
                </div>
                <h3 class="text-xl font-semibold text-white mb-2">Easy Setup</h3>
                <p class="text-gray-400">Get started quickly with intuitive features and straightforward configuration options.</p>
            </div>
        </div>
    </div>
</div>
@endsection
