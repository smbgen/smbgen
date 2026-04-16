@extends('layouts.guest')

@section('content')
<div class="relative w-full min-h-screen bg-gradient-to-br from-gray-100 via-slate-50 to-blue-100/40 dark:from-gray-900 dark:via-gray-900 dark:to-blue-950/30 flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="mb-5 flex justify-center">
            <div class="inline-flex items-center rounded-full border border-blue-200 bg-white/85 px-4 py-2 text-sm font-semibold tracking-wide text-gray-800 shadow-sm dark:border-blue-500/30 dark:bg-gray-800/85 dark:text-gray-100">
                {{ config('app.company_name', config('app.name')) }}
            </div>
        </div>

        <div class="bg-white/90 dark:bg-gray-800/90 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl p-6">
        <h2 class="text-gray-900 dark:text-white text-xl font-semibold mb-1">Sign in to your organization</h2>
        <p class="text-gray-500 dark:text-gray-400 text-sm mb-4">Use your account credentials to continue.</p>

        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Email')" class="text-gray-700 dark:text-gray-300" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', request('email'))" required autofocus autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="password" :value="__('Password')" class="text-gray-700 dark:text-gray-300" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="current-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <x-primary-button class="w-full justify-center">
                {{ __('Sign in') }}
            </x-primary-button>
        </form>

        <div class="flex items-center gap-3 mt-5 mb-3">
            <span class="h-px bg-gray-300 dark:bg-gray-700 flex-1"></span>
            <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">OR</span>
            <span class="h-px bg-gray-300 dark:bg-gray-700 flex-1"></span>
        </div>

        <a href="{{ route('auth.google.redirect') }}" class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="mr-2">
                <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
            </svg>
            Continue with Google
        </a>

        <div class="mt-4 flex items-center justify-between text-sm">
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline dark:text-blue-300">Create a new account</a>
            <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline dark:text-blue-300">Forgot your password?</a>
        </div>
    </div>
</div>
</div>
@endsection
