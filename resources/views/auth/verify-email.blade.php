@extends('layouts.portal')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="section-block text-center">
        {{-- Header Icon --}}
        <div class="flex justify-center mb-6">
            <div class="w-20 h-20 bg-blue-100 dark:bg-blue-900/30 rounded-full flex items-center justify-center">
                <svg class="w-10 h-10 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
            </div>
        </div>

        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-3">Verify Your Email</h1>

        <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-sm mx-auto">
            Before getting started, please verify your email address by clicking the link we just sent you.
        </p>

        @if (session('status') == 'verification-link-sent')
            <div class="mb-6 alert alert-success">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>A new verification link has been sent to your email address.</span>
            </div>
        @endif

        <div class="space-y-3">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="btn-primary w-full">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Resend Verification Email
                </button>
            </form>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-secondary w-full">
                    Log Out
                </button>
            </form>
        </div>

        <p class="text-gray-500 dark:text-gray-500 text-sm mt-8">
            Didn't receive an email? Check your spam folder or contact support.
        </p>
    </div>
</div>
@endsection
