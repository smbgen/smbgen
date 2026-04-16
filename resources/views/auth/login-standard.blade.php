@extends('layouts.guest')

@section('content')
<div class="px-4 w-full max-w-md">
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

        <div class="mt-4 flex items-center justify-between text-sm">
            <a href="{{ route('register') }}" class="text-blue-600 hover:underline dark:text-blue-300">Create a new account</a>
            <a href="{{ route('password.request') }}" class="text-blue-600 hover:underline dark:text-blue-300">Forgot your password?</a>
        </div>
    </div>
</div>
@endsection
