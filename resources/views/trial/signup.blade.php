@extends('layouts.guest')

@push('styles')
<style>
    .trial-shell {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        border-radius: 1.5rem;
        background:
            radial-gradient(ellipse 960px 540px at 88% -10%, rgba(79, 70, 229, 0.2) 0%, rgba(79, 70, 229, 0) 62%),
            radial-gradient(ellipse 860px 420px at -8% 94%, rgba(16, 185, 129, 0.14) 0%, rgba(16, 185, 129, 0) 70%),
            linear-gradient(160deg, #f8fbff 0%, #eef4ff 44%, #eaf7ff 100%);
    }

    .trial-shell::before {
        content: '';
        position: absolute;
        inset: -35% -20% auto;
        height: 120%;
        background: conic-gradient(from 180deg at 50% 50%, rgba(99, 102, 241, 0.16), rgba(6, 182, 212, 0.08), rgba(16, 185, 129, 0.12), rgba(99, 102, 241, 0.16));
        filter: blur(80px);
        opacity: 0.6;
        z-index: 0;
        pointer-events: none;
    }

    .trial-content {
        position: relative;
        z-index: 1;
    }

    .trial-pill {
        font-size: 11px;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        font-weight: 700;
    }

    @media (prefers-reduced-motion: reduce) {
        .trial-shell::before {
            animation: none;
        }
    }
</style>
@endpush

@section('content')
@php
    $baseHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';
@endphp

<div class="w-full max-w-7xl px-4 sm:px-6 lg:px-8 trial-shell" x-data="{ companyName: '{{ old('company_name') }}', subdomain: '{{ old('subdomain') }}' }">
    <div class="trial-content grid gap-6 p-4 sm:p-6 lg:grid-cols-[minmax(0,1.05fr)_minmax(330px,0.95fr)] lg:items-start">
        <div class="rounded-2xl border border-white/50 bg-white/90 p-8 shadow-2xl backdrop-blur-sm dark:border-gray-700 dark:bg-gray-800/90 sm:p-10">
            <div class="mb-8 text-center">
                <div class="mb-4 inline-flex h-16 w-16 items-center justify-center rounded-2xl bg-indigo-100 dark:bg-indigo-900/30">
                    <svg class="h-8 w-8 text-indigo-600 dark:text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <div class="trial-pill mb-3 inline-flex items-center gap-2 rounded-full border border-indigo-200 bg-indigo-50 px-3 py-1 text-indigo-700 dark:border-indigo-500/30 dark:bg-indigo-500/10 dark:text-indigo-200">
                    <span class="inline-block h-1.5 w-1.5 rounded-full bg-indigo-500"></span>
                    smbgen free trial
                </div>
                <p class="mb-2 text-sm font-semibold text-indigo-600 dark:text-indigo-300">Start Your Free Trial</p>
                <h1 class="mb-2 text-3xl font-black tracking-tight text-gray-900 dark:text-white">Launch Your Workspace in Minutes</h1>
                <p class="mx-auto max-w-xl text-sm leading-6 text-gray-600 dark:text-gray-300">Start your branded tenant, invite your team, and begin using payments, messaging, and client workflows with zero setup friction.</p>
                <div class="mt-4 flex flex-wrap items-center justify-center gap-2 text-xs">
                    <span class="rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 font-semibold text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">14 days free</span>
                    <span class="rounded-full border border-blue-200 bg-blue-50 px-3 py-1 font-semibold text-blue-700 dark:border-blue-500/30 dark:bg-blue-500/10 dark:text-blue-200">No credit card</span>
                    <span class="rounded-full border border-violet-200 bg-violet-50 px-3 py-1 font-semibold text-violet-700 dark:border-violet-500/30 dark:bg-violet-500/10 dark:text-violet-200">Instant tenant provisioning</span>
                </div>
            </div>

            @if($errors->any())
                <div class="mb-6 rounded-lg border border-red-200 bg-red-50 p-4 dark:border-red-500/30 dark:bg-red-900/20">
                    <div class="flex items-start">
                        <svg class="mr-2 mt-0.5 h-5 w-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <ul class="space-y-1 text-sm text-red-600 dark:text-red-400">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('trial.register') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="company_name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Company Name <span class="text-red-500">*</span></label>
                    <input type="text" name="company_name" id="company_name" required class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" value="{{ old('company_name') }}" x-model="companyName" @input="if (!subdomain) { subdomain = companyName.toLowerCase().replace(/[^a-z0-9\s-]/g, '').trim().replace(/\s+/g, '-').replace(/-+/g, '-') }" placeholder="Acme Inc">
                    @error('company_name')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="subdomain" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Workspace Subdomain <span class="text-red-500">*</span></label>
                    <div class="flex items-center overflow-hidden rounded-lg border border-gray-300 focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500 dark:border-gray-600">
                        <input type="text" name="subdomain" id="subdomain" required class="w-full border-0 px-4 py-3 focus:ring-0 dark:bg-gray-700 dark:text-white" value="{{ old('subdomain') }}" x-model="subdomain" placeholder="acme">
                        <span class="border-l border-gray-300 bg-gray-50 px-3 py-3 text-sm text-gray-500 dark:border-gray-600 dark:bg-gray-700/60 dark:text-gray-400">.{{ $baseHost }}</span>
                    </div>
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Use lowercase letters, numbers, and hyphens only.</p>
                    <p class="mt-1 text-xs text-indigo-600 dark:text-indigo-300">Preview: <span class="font-medium" x-text="(subdomain || 'your-company') + '.{{ $baseHost }}'"></span></p>
                    @error('subdomain')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="custom_domain" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Custom Domain (Optional)</label>
                    <input type="text" name="custom_domain" id="custom_domain" class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" value="{{ old('custom_domain') }}" placeholder="app.yourcompany.com">
                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">You can update this later once DNS is pointed.</p>
                    @error('custom_domain')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Your Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" required class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" value="{{ old('name') }}" placeholder="John Doe">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" required class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" value="{{ old('email') }}" placeholder="john@acme.com">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label for="password" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password" id="password" required class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="At least 8 characters">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">Confirm Password <span class="text-red-500">*</span></label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required class="w-full rounded-lg border border-gray-300 px-4 py-3 transition-colors focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white" placeholder="Confirm your password">
                    </div>
                </div>

                <button type="submit" class="w-full rounded-xl bg-gradient-to-r from-indigo-600 to-sky-500 px-6 py-3 font-semibold text-white shadow-lg shadow-indigo-900/30 transition-colors hover:from-indigo-500 hover:to-sky-400">Create my workspace</button>
            </form>

            <div class="my-6 flex items-center gap-3">
                <span class="h-px flex-1 bg-gray-300 dark:bg-gray-600"></span>
                <span class="text-xs uppercase tracking-wider text-gray-500 dark:text-gray-400">or</span>
                <span class="h-px flex-1 bg-gray-300 dark:bg-gray-600"></span>
            </div>

            <div>
                <a href="{{ route('trial.google.redirect') }}" class="inline-flex w-full items-center justify-center rounded-xl border border-gray-300 px-4 py-3 text-gray-700 transition-colors hover:bg-gray-50 hover:text-gray-900 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 dark:hover:text-white">
                    <svg width="18" height="18" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48" class="mr-2">
                        <path fill="#FFC107" d="M43.611,20.083H42V20H24v8h11.303c-1.649,4.657-6.08,8-11.303,8c-6.627,0-12-5.373-12-12c0-6.627,5.373-12,12-12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C12.955,4,4,12.955,4,24c0,11.045,8.955,20,20,20c11.045,0,20-8.955,20-20C44,22.659,43.862,21.35,43.611,20.083z"/>
                        <path fill="#FF3D00" d="M6.306,14.691l6.571,4.819C14.655,15.108,18.961,12,24,12c3.059,0,5.842,1.154,7.961,3.039l5.657-5.657C34.046,6.053,29.268,4,24,4C16.318,4,9.656,8.337,6.306,14.691z"/>
                        <path fill="#4CAF50" d="M24,44c5.166,0,9.86-1.977,13.409-5.192l-6.19-5.238C29.211,35.091,26.715,36,24,36c-5.202,0-9.619-3.317-11.283-7.946l-6.522,5.025C9.505,39.556,16.227,44,24,44z"/>
                        <path fill="#1976D2" d="M43.611,20.083H42V20H24v8h11.303c-0.792,2.237-2.231,4.166-4.087,5.571c0.001-0.001,0.002-0.001,0.003-0.002l6.19,5.238C36.971,39.205,44,34,44,24C44,22.659,43.862,21.35,43.611,20.083z"/>
                    </svg>
                    One-click with Google
                </a>
                <p class="mt-2 text-center text-xs text-gray-500 dark:text-gray-400">Already using Google Workspace? This is the fastest way to start.</p>
            </div>

            <div class="mt-6 text-center text-sm text-gray-600 dark:text-gray-400">
                Already have an account?
                <a href="{{ route('login') }}" class="font-medium text-indigo-600 transition-colors hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300">Sign in</a>
            </div>
        </div>

        <div class="space-y-5">
            <div class="rounded-2xl border border-slate-800 bg-slate-900 p-6 text-white shadow-xl shadow-slate-900/20">
                <p class="trial-pill text-cyan-300">Why teams choose smbgen</p>
                <h2 class="mt-3 text-2xl font-semibold">Turn visitors into booked, paying clients</h2>
                <p class="mt-3 text-sm leading-6 text-slate-300">Your trial includes the exact workflow most service businesses need to close more revenue with less manual follow-up.</p>
                <div class="mt-5 space-y-3">
                    <div class="rounded-xl border border-slate-700 bg-slate-950/60 p-3 text-sm text-slate-200">Lead capture and client messaging in one workspace</div>
                    <div class="rounded-xl border border-slate-700 bg-slate-950/60 p-3 text-sm text-slate-200">Domain onboarding with tenant-ready setup flow</div>
                    <div class="rounded-xl border border-slate-700 bg-slate-950/60 p-3 text-sm text-slate-200">Payments and integrations setup from your admin dashboard</div>
                </div>
            </div>

            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-6 text-emerald-900 dark:border-emerald-700 dark:bg-emerald-950 dark:text-emerald-50">
                <p class="trial-pill text-emerald-700 dark:text-emerald-200">Quick start path</p>
                <p class="mt-2 text-sm font-semibold">What happens next</p>
                <div class="mt-4 space-y-3 text-sm">
                    <p><span class="font-semibold">1.</span> Create your workspace and subdomain.</p>
                    <p><span class="font-semibold">2.</span> Land directly in your tenant login.</p>
                    <p><span class="font-semibold">3.</span> Finish onboarding and launch in the same session.</p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white/90 px-5 py-4 text-xs leading-5 text-gray-600 dark:border-gray-700 dark:bg-gray-800/90 dark:text-gray-300">
                By signing up, you agree to our Terms of Service and Privacy Policy.
            </div>
        </div>
    </div>
</div>
@endsection
