@extends('layouts.frontend')

@php
    $bookHref    = Route::has('booking.wizard') ? route('booking.wizard') : route('contact');
    $contactHref = route('contact');
@endphp

@push('head')
<style>
    .gw-hero-bg {
        background:
            radial-gradient(ellipse at 70% -10%, rgba(66,133,244,0.18) 0%, transparent 55%),
            radial-gradient(ellipse at 10% 90%, rgba(52,168,83,0.10) 0%, transparent 50%),
            radial-gradient(ellipse at 90% 80%, rgba(251,188,5,0.08) 0%, transparent 45%),
            #06101d;
    }
    .gw-card-hover:hover {
        box-shadow: 0 0 0 1px rgba(66,133,244,0.30), 0 8px 32px rgba(66,133,244,0.08);
        transform: translateY(-1px);
        transition: all 0.18s ease;
    }
    .gw-gradient-text {
        background: linear-gradient(135deg, #60a5fa, #34d399, #fbbf24);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .gw-connect-line {
        background: linear-gradient(90deg, rgba(66,133,244,0.0) 0%, rgba(66,133,244,0.35) 50%, rgba(52,168,83,0.0) 100%);
    }
</style>
@endpush

@section('content')

{{-- ============================================================ --}}
{{-- HERO --}}
{{-- ============================================================ --}}
<section class="gw-hero-bg min-h-[88vh] flex items-center">
    <div class="max-w-6xl mx-auto px-6 py-28 text-center">

        {{-- Badge --}}
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-blue-500/30 bg-blue-600/10 text-blue-300 text-xs font-medium mb-8">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-400 animate-pulse"></span>
            Google Workspace Integrations
        </div>

        {{-- Google app icon strip --}}
        <div class="flex items-center justify-center gap-5 mb-10">
            {{-- Google Calendar --}}
            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/5 border border-white/10 shadow-lg">
                <svg class="w-6 h-6" viewBox="0 0 48 48" fill="none">
                    <rect x="4" y="4" width="40" height="40" rx="4" fill="#fff"/>
                    <rect x="12" y="4" width="4" height="8" rx="2" fill="#1a73e8"/>
                    <rect x="32" y="4" width="4" height="8" rx="2" fill="#1a73e8"/>
                    <rect x="4" y="16" width="40" height="2" fill="#1a73e8"/>
                    <text x="24" y="34" font-size="14" font-weight="700" text-anchor="middle" fill="#1a73e8">{{ now()->format('d') }}</text>
                </svg>
            </div>
            {{-- Google Meet --}}
            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/5 border border-white/10 shadow-lg">
                <svg class="w-6 h-6" viewBox="0 0 48 48" fill="none">
                    <rect width="48" height="48" rx="8" fill="#00897B"/>
                    <rect x="8" y="14" width="22" height="20" rx="3" fill="white"/>
                    <path d="M30 20l10-5v18l-10-5V20z" fill="white"/>
                </svg>
            </div>
            {{-- Arrow --}}
            <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
            {{-- smbgen logo placeholder --}}
            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-gradient-to-br from-blue-600 to-indigo-700 border border-blue-500/40 shadow-lg shadow-blue-900/40">
                <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"/>
                </svg>
            </div>
            {{-- Arrow --}}
            <svg class="w-5 h-5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 8l4 4m0 0l-4 4m4-4H3"/>
            </svg>
            {{-- Google SSO shield --}}
            <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-white/5 border border-white/10 shadow-lg">
                <svg class="w-6 h-6" viewBox="0 0 48 48" fill="none">
                    <path d="M24 4L6 12v14c0 10 8 18 18 18s18-8 18-18V12L24 4z" fill="url(#sso-grad)"/>
                    <path d="M18 24l4 4 8-8" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                    <defs>
                        <linearGradient id="sso-grad" x1="6" y1="4" x2="42" y2="44" gradientUnits="userSpaceOnUse">
                            <stop stop-color="#4285F4"/>
                            <stop offset="1" stop-color="#34A853"/>
                        </linearGradient>
                    </defs>
                </svg>
            </div>
        </div>

        <h1 class="text-5xl md:text-6xl font-black text-white leading-[1.05] mb-6" style="letter-spacing: -0.02em;">
            Eliminate the data silos<br>
            in your <span class="gw-gradient-text">Google Workspace</span> stack.
        </h1>

        <p class="text-gray-400 text-xl max-w-2xl mx-auto mb-10 leading-relaxed">
            smbgen connects Calendar, Meet, and Google OAuth directly to your booking flows, customer records, and access management — so your team's Workspace tools and your business platform actually talk to each other.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ $bookHref }}?intent=google-workspace"
               class="px-7 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm tracking-wide transition-colors shadow-lg shadow-blue-900/30 border border-blue-500/40">
                Book an integration planning call
            </a>
            <a href="{{ $contactHref }}?topic=google-workspace"
               class="px-7 py-3.5 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold text-sm border border-white/10 transition-colors">
                Ask a specific question
            </a>
        </div>

    </div>
</section>

{{-- ============================================================ --}}
{{-- INTEGRATION CARDS (4 pillars) --}}
{{-- ============================================================ --}}
<section class="bg-[#06101d] py-24 border-t border-white/5">
    <div class="max-w-6xl mx-auto px-6">

        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">
                Four integrations. One connected workflow.
            </h2>
            <p class="text-gray-400 text-lg max-w-xl mx-auto">
                Each Google service wires into a specific smbgen-core module — so the data lands where your team already works.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-6">

            {{-- Google Calendar --}}
            <div class="gw-card-hover rounded-2xl p-8 transition-all"
                 style="background: rgba(66,133,244,0.05); border: 1px solid rgba(66,133,244,0.18);">
                <div class="flex items-start gap-5 mb-6">
                    <div class="w-12 h-12 rounded-xl flex-shrink-0 flex items-center justify-center bg-blue-600/15 border border-blue-500/25">
                        <svg class="w-7 h-7 text-blue-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="4" width="18" height="18" rx="2" stroke-linecap="round"/>
                            <path d="M8 2v4M16 2v4M3 10h18" stroke-linecap="round"/>
                            <path d="M8 14h.01M12 14h.01M16 14h.01M8 18h.01M12 18h.01M16 18h.01" stroke-linecap="round" stroke-width="2"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-blue-400 uppercase tracking-wider mb-1">Google Calendar</div>
                        <h3 class="text-xl font-bold text-white">Two-way booking sync</h3>
                    </div>
                </div>
                <p class="text-gray-400 mb-5 leading-relaxed">
                    Booked appointments in smbgen automatically create Google Calendar events for your team. Cancelled bookings remove the event. Real-time availability checks your team's calendars so customers only see slots you actually have open.
                </p>
                <ul class="space-y-2 text-sm text-gray-300">
                    @foreach ([
                        'Real-time availability from Google Calendar',
                        'Auto-create & cancel events on booking changes',
                        'Per-staff calendar routing',
                        'Timezone-aware scheduling',
                        'Custom event titles, descriptions, and attendees',
                    ] as $point)
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-blue-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $point }}
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Google Meet --}}
            <div class="gw-card-hover rounded-2xl p-8 transition-all"
                 style="background: rgba(52,168,83,0.05); border: 1px solid rgba(52,168,83,0.18);">
                <div class="flex items-start gap-5 mb-6">
                    <div class="w-12 h-12 rounded-xl flex-shrink-0 flex items-center justify-center bg-green-600/15 border border-green-500/25">
                        <svg class="w-7 h-7 text-green-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.889L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-green-400 uppercase tracking-wider mb-1">Google Meet</div>
                        <h3 class="text-xl font-bold text-white">Auto-generated meeting links</h3>
                    </div>
                </div>
                <p class="text-gray-400 mb-5 leading-relaxed">
                    When a booking is confirmed, smbgen generates a Google Meet link and includes it in the calendar invite, the confirmation email, and the customer portal — no copy-pasting links, no forgotten meetings.
                </p>
                <ul class="space-y-2 text-sm text-gray-300">
                    @foreach ([
                        'Meet link auto-attached to every confirmed booking',
                        'Link included in customer confirmation email',
                        'Visible in client portal appointment view',
                        'Staff calendar event includes join link',
                        'Works with rescheduled and updated bookings',
                    ] as $point)
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-green-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $point }}
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- Google OAuth --}}
            <div class="gw-card-hover rounded-2xl p-8 transition-all"
                 style="background: rgba(251,188,5,0.04); border: 1px solid rgba(251,188,5,0.18);">
                <div class="flex items-start gap-5 mb-6">
                    <div class="w-12 h-12 rounded-xl flex-shrink-0 flex items-center justify-center bg-yellow-600/12 border border-yellow-500/25">
                        <svg class="w-7 h-7 text-yellow-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <circle cx="12" cy="8" r="4"/>
                            <path d="M20 21a8 8 0 10-16 0" stroke-linecap="round"/>
                            <path d="M15.5 8H20M20 8l-2-2M20 8l-2 2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-yellow-400 uppercase tracking-wider mb-1">Google OAuth</div>
                        <h3 class="text-xl font-bold text-white">Sign in with Google</h3>
                    </div>
                </div>
                <p class="text-gray-400 mb-5 leading-relaxed">
                    Customers and staff authenticate using their existing Google accounts. No passwords to manage. Email is pre-verified, profile data is pre-filled, and you decide which Google domains are allowed to register.
                </p>
                <ul class="space-y-2 text-sm text-gray-300">
                    @foreach ([
                        'One-click Google sign-in for customers and staff',
                        'Email verified automatically on first login',
                        'Domain allowlists for staff accounts',
                        'Profile picture and name imported from Google',
                        'Existing email accounts auto-linked on match',
                    ] as $point)
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-yellow-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $point }}
                    </li>
                    @endforeach
                </ul>
            </div>

            {{-- SSO Management --}}
            <div class="gw-card-hover rounded-2xl p-8 transition-all"
                 style="background: rgba(234,67,53,0.04); border: 1px solid rgba(234,67,53,0.18);">
                <div class="flex items-start gap-5 mb-6">
                    <div class="w-12 h-12 rounded-xl flex-shrink-0 flex items-center justify-center bg-red-600/12 border border-red-500/25">
                        <svg class="w-7 h-7 text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M12 3l8 4v5c0 4.5-3.5 8.7-8 9.9C7.5 20.7 4 16.5 4 12V7l8-4z" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M9 12l2 2 4-4" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-red-400 uppercase tracking-wider mb-1">SSO Management</div>
                        <h3 class="text-xl font-bold text-white">Admin-level access control</h3>
                    </div>
                </div>
                <p class="text-gray-400 mb-5 leading-relaxed">
                    Lock your admin panel to your Google Workspace domain. Provision staff roles based on Google groups, revoke access when an account is removed from your directory, and audit every login from one place.
                </p>
                <ul class="space-y-2 text-sm text-gray-300">
                    @foreach ([
                        'Domain-locked admin access (your-company.com only)',
                        'Google group → smbgen role mapping',
                        'Revoke access by removing from Google Workspace',
                        'Login audit log with device and IP data',
                        'Enforce 2FA via Google account settings',
                    ] as $point)
                    <li class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-red-400 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        {{ $point }}
                    </li>
                    @endforeach
                </ul>
            </div>

        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- HOW IT CONNECTS --}}
{{-- ============================================================ --}}
<section class="bg-[#060d1a] py-24 border-t border-white/5">
    <div class="max-w-6xl mx-auto px-6">

        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">How it connects</h2>
            <p class="text-gray-400 text-lg max-w-xl mx-auto">
                Google tools feed in, smbgen acts, your team sees a single view.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">

            @foreach ([
                [
                    'num'   => '01',
                    'color' => 'blue',
                    'icon'  => '<path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round"/>',
                    'title' => 'Customer or staff authenticates with Google',
                    'body'  => 'OAuth handles identity verification. Accounts are created or matched automatically. No extra passwords, no verification emails.',
                ],
                [
                    'num'   => '02',
                    'color' => 'green',
                    'icon'  => '<path d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" stroke-linecap="round" stroke-linejoin="round"/>',
                    'title' => 'Booking checks live calendar availability',
                    'body'  => 'smbgen queries the assigned staff member\'s Google Calendar in real time. Only genuinely open slots appear in the booking flow.',
                ],
                [
                    'num'   => '03',
                    'color' => 'indigo',
                    'icon'  => '<path d="M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.889L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z" stroke-linecap="round" stroke-linejoin="round"/>',
                    'title' => 'Confirmation fires across every channel',
                    'body'  => 'Calendar event created. Meet link generated. Confirmation email sent with join link. Customer portal updated. All from a single booking action.',
                ],
            ] as $step)
            <div class="relative">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-11 h-11 rounded-xl flex items-center justify-center font-black text-sm
                        @if($step['color'] === 'blue') bg-blue-600/20 border border-blue-500/30 text-blue-300
                        @elseif($step['color'] === 'green') bg-green-600/20 border border-green-500/30 text-green-300
                        @else bg-indigo-600/20 border border-indigo-500/30 text-indigo-300
                        @endif">
                        {{ $step['num'] }}
                    </div>
                    <div>
                        <h3 class="text-white font-bold text-lg mb-2 leading-snug">{{ $step['title'] }}</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">{{ $step['body'] }}</p>
                    </div>
                </div>
            </div>
            @endforeach

        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- FEATURE GRID --}}
{{-- ============================================================ --}}
<section class="bg-[#06101d] py-24 border-t border-white/5">
    <div class="max-w-6xl mx-auto px-6">

        <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">
                Everything that ships with the integration
            </h2>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
            @foreach ([
                ['title' => 'Live availability sync',         'body' => 'Calendar free/busy checked on every booking request — no double bookings.',                          'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z', 'color' => 'blue'],
                ['title' => 'Auto Meet link generation',      'body' => 'Every confirmed booking gets a unique Meet link attached to the calendar event and email.',          'icon' => 'M15 10l4.553-2.069A1 1 0 0121 8.82v6.36a1 1 0 01-1.447.889L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z', 'color' => 'green'],
                ['title' => 'Google Sign-In button',          'body' => 'One-click OAuth on every auth page. Works for customers and admin staff.',                          'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'color' => 'yellow'],
                ['title' => 'Domain allowlists',             'body' => 'Restrict staff login to verified @your-company.com Google accounts only.',                          'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z', 'color' => 'red'],
                ['title' => 'Role-to-group mapping',         'body' => 'Map Google Workspace groups to smbgen roles: admin, staff, read-only.',                             'icon' => 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z', 'color' => 'indigo'],
                ['title' => 'Login audit log',               'body' => 'Track every sign-in: timestamp, IP, device type, OAuth provider, and account status.',              'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2', 'color' => 'purple'],
                ['title' => 'Cancellation propagation',      'body' => 'Cancel in smbgen, the Google Calendar event is removed and a cancellation email goes out.',         'icon' => 'M6 18L18 6M6 6l12 12', 'color' => 'red'],
                ['title' => 'Customer portal Meet link',      'body' => 'Customers see their upcoming meeting link inside their booking portal — always one click away.',   'icon' => 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1', 'color' => 'green'],
                ['title' => 'Access revocation',             'body' => 'Remove from Google Workspace → access to smbgen admin is revoked on next login check.',             'icon' => 'M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636', 'color' => 'orange'],
            ] as $feature)
            <div class="rounded-xl p-6 transition-all hover:bg-white/[0.03]"
                 style="background: rgba(255,255,255,0.025); border: 1px solid rgba(255,255,255,0.07);">
                <div class="w-9 h-9 rounded-lg flex items-center justify-center mb-4
                    @if($feature['color'] === 'blue') bg-blue-600/15 border border-blue-500/20
                    @elseif($feature['color'] === 'green') bg-green-600/15 border border-green-500/20
                    @elseif($feature['color'] === 'yellow') bg-yellow-600/15 border border-yellow-500/20
                    @elseif($feature['color'] === 'red') bg-red-600/15 border border-red-500/20
                    @elseif($feature['color'] === 'indigo') bg-indigo-600/15 border border-indigo-500/20
                    @elseif($feature['color'] === 'purple') bg-purple-600/15 border border-purple-500/20
                    @elseif($feature['color'] === 'orange') bg-orange-600/15 border border-orange-500/20
                    @else bg-gray-600/15 border border-gray-500/20
                    @endif">
                    <svg class="w-4.5 h-4.5
                        @if($feature['color'] === 'blue') text-blue-400
                        @elseif($feature['color'] === 'green') text-green-400
                        @elseif($feature['color'] === 'yellow') text-yellow-400
                        @elseif($feature['color'] === 'red') text-red-400
                        @elseif($feature['color'] === 'indigo') text-indigo-400
                        @elseif($feature['color'] === 'purple') text-purple-400
                        @elseif($feature['color'] === 'orange') text-orange-400
                        @else text-gray-400
                        @endif"
                         viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="{{ $feature['icon'] }}" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <h4 class="text-white font-bold mb-1.5">{{ $feature['title'] }}</h4>
                <p class="text-gray-400 text-sm leading-relaxed">{{ $feature['body'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- WHO IT'S FOR --}}
{{-- ============================================================ --}}
<section class="bg-[#060d1a] py-24 border-t border-white/5">
    <div class="max-w-6xl mx-auto px-6">

        <div class="text-center mb-14">
            <h2 class="text-3xl md:text-4xl font-extrabold text-white mb-4">Who gets the most out of this</h2>
            <p class="text-gray-400 text-lg max-w-xl mx-auto">
                If your team already lives in Google Workspace, this makes smbgen feel native — not bolted on.
            </p>
        </div>

        <div class="grid md:grid-cols-3 gap-6">
            @foreach ([
                [
                    'who'   => 'Agencies & studios',
                    'color' => 'blue',
                    'what'  => 'Client discovery calls book straight into the Google Calendar of the account lead, with a Meet link ready. OAuth means clients check their portal without creating another account.',
                ],
                [
                    'who'   => 'Professional services',
                    'color' => 'green',
                    'what'  => 'Consultants and advisors use Meet for every client session. Calendar sync ensures no double bookings. SSO keeps the admin panel locked to verified firm email addresses.',
                ],
                [
                    'who'   => 'Operations & finance teams',
                    'color' => 'indigo',
                    'what'  => 'Centralise access control through Google Workspace directory. Onboard a new hire: add to the Google group → smbgen role granted. Offboard: remove → access revoked.',
                ],
            ] as $card)
            <div class="rounded-2xl p-7
                @if($card['color'] === 'blue') border border-blue-500/15
                @elseif($card['color'] === 'green') border border-green-500/15
                @else border border-indigo-500/15
                @endif"
                 style="background: rgba(255,255,255,0.025);">
                <div class="inline-block px-3 py-1 rounded-full text-xs font-semibold mb-4
                    @if($card['color'] === 'blue') bg-blue-600/12 text-blue-300
                    @elseif($card['color'] === 'green') bg-green-600/12 text-green-300
                    @else bg-indigo-600/12 text-indigo-300
                    @endif">
                    {{ $card['who'] }}
                </div>
                <p class="text-gray-300 leading-relaxed">{{ $card['what'] }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- smbgen-core CONNECTOR STRIP --}}
{{-- ============================================================ --}}
<section class="py-14 border-t border-white/5" style="background: rgba(66,133,244,0.04);">
    <div class="max-w-6xl mx-auto px-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-8">
            <div>
                <div class="text-xs font-semibold text-blue-400 uppercase tracking-widest mb-2">Part of smbgen-core</div>
                <h3 class="text-2xl font-extrabold text-white mb-1">Google Workspace is one piece.</h3>
                <p class="text-gray-400 max-w-lg">
                    The same booking that creates a Meet event also creates a CRM contact, sends a confirmation email, and updates the customer portal — across smbgen-core modules.
                </p>
            </div>
            <div class="flex flex-wrap gap-3 flex-shrink-0">
                @foreach ([
                    ['label' => 'Book-Core', 'href' => route('product.book'), 'color' => 'blue'],
                    ['label' => 'Contact-Core', 'href' => route('product.contact'), 'color' => 'indigo'],
                    ['label' => 'CRM-Core', 'href' => route('product.crm'), 'color' => 'violet'],
                    ['label' => 'Portal-Core', 'href' => route('product.portal'), 'color' => 'sky'],
                ] as $link)
                <a href="{{ $link['href'] }}"
                   class="px-4 py-2 rounded-lg text-sm font-semibold transition-colors
                    @if($link['color'] === 'blue') bg-blue-600/15 hover:bg-blue-600/25 text-blue-300 border border-blue-500/20
                    @elseif($link['color'] === 'indigo') bg-indigo-600/15 hover:bg-indigo-600/25 text-indigo-300 border border-indigo-500/20
                    @elseif($link['color'] === 'violet') bg-violet-600/15 hover:bg-violet-600/25 text-violet-300 border border-violet-500/20
                    @else bg-sky-600/15 hover:bg-sky-600/25 text-sky-300 border border-sky-500/20
                    @endif">
                    {{ $link['label'] }}
                </a>
                @endforeach
            </div>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- CTA --}}
{{-- ============================================================ --}}
<section class="bg-[#06101d] py-28 border-t border-white/5">
    <div class="max-w-3xl mx-auto px-6 text-center">

        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-blue-500/30 bg-blue-600/10 text-blue-300 text-xs font-medium mb-8">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-400"></span>
            Ready to connect your Workspace
        </div>

        <h2 class="text-4xl md:text-5xl font-black text-white mb-5" style="letter-spacing: -0.02em;">
            Start with a 30-minute<br>integration planning call.
        </h2>
        <p class="text-gray-400 text-lg mb-10 leading-relaxed">
            We'll map your current Google Workspace setup to the right smbgen modules, identify any gaps, and give you a clear plan — no obligation.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ $bookHref }}?intent=google-workspace"
               class="px-8 py-4 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold transition-colors shadow-xl shadow-blue-900/30 border border-blue-500/40">
                Book the planning call
            </a>
            <a href="{{ $contactHref }}?topic=google-workspace"
               class="px-8 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors">
                Send a question first
            </a>
            <a href="{{ route('solutions') }}"
               class="px-8 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-400 font-semibold border border-white/10 transition-colors">
                Explore all solutions
            </a>
        </div>

    </div>
</section>

@endsection
