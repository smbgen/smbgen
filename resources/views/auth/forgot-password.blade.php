@extends('layouts.guest')

@section('content')
<div class="px-4 w-full max-w-md">
    <div class="bg-gray-800/90 border border-gray-700 rounded-xl shadow-xl p-6">
        <div class="mb-4 text-sm text-gray-300">
            {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </div>

        <!-- Session Status -->
        <x-auth-session-status class="mb-4" :status="session('status')" />

        <form method="POST" action="{{ route('password.email') }}" class="space-y-3">
            @csrf

            <!-- Email Address -->
            <div x-data="{ isGmail: {{ old('email') && preg_match('/@(gmail\.|googlemail\.)/i', old('email')) ? 'true' : 'false' }} }"
                 @input.capture="isGmail = /\@(gmail\.|googlemail\.)/i.test($event.target.value)">
                <x-input-label for="email" :value="__('Email')" class="text-gray-300" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />

                {{-- Gmail hint --}}
                <div x-show="isGmail"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-1"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="mt-2 flex items-start gap-2 rounded-lg bg-blue-900/40 border border-blue-700/60 px-3 py-2 text-xs text-blue-300">
                    <svg class="mt-0.5 h-3.5 w-3.5 shrink-0 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 2a10 10 0 100 20A10 10 0 0012 2z"/>
                    </svg>
                    <span>Gmail accounts sign in with Google — no password needed. Use <a href="{{ route('auth.google.redirect') }}" class="font-semibold text-blue-200 underline hover:text-white">Continue with Google</a> below instead.</span>
                </div>
            </div>

            <div class="flex items-center justify-end mt-4">
                <x-primary-button>
                    {{ __('Email Password Reset Link') }}
                </x-primary-button>
            </div>
        </form>

        <div class="flex items-center gap-3 mt-5 mb-3">
            <span class="h-px bg-gray-700 flex-1"></span>
            <span class="text-xs uppercase tracking-wider text-gray-400">OR</span>
            <span class="h-px bg-gray-700 flex-1"></span>
        </div>

        <a href="{{ route('auth.google.redirect') }}" class="w-full inline-flex items-center justify-center px-4 py-3 border border-gray-600 rounded-lg text-gray-300 hover:text-white hover:bg-gray-700 transition-colors">
            <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="mr-2">
                <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
            </svg>
            Continue with Google
        </a>
    </div>
</div>
@endsection
