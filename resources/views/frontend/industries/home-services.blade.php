@extends('layouts.frontend')

@php
    $bookHref    = Route::has('booking.wizard') ? route('booking.wizard') : route('contact');
    $contactHref = route('contact');
    $loginHref   = route('login');
@endphp

@section('title', 'smbgen for Home Service Pros — Job Booking, Dispatch & Client Management')
@section('description', 'Built for plumbers, HVAC techs, electricians, and contractors. Online job booking with service area forms, Google Calendar dispatch, automated confirmations, and a client portal — all in one place.')

@push('head')
<style>
    .hs-hero-bg {
        background:
            radial-gradient(ellipse at 65% -5%, rgba(249,115,22,0.15) 0%, transparent 55%),
            radial-gradient(ellipse at 5%  85%, rgba(99,102,241,0.10) 0%, transparent 50%),
            radial-gradient(ellipse at 95% 75%, rgba(234,179,8,0.07) 0%, transparent 45%),
            #06101d;
    }
    .hs-card-hover {
        transition: box-shadow 0.18s ease, transform 0.18s ease;
    }
    .hs-card-hover:hover {
        box-shadow: 0 0 0 1px rgba(249,115,22,0.25), 0 8px 32px rgba(249,115,22,0.08);
        transform: translateY(-2px);
    }
    .hs-gradient-text {
        background: linear-gradient(135deg, #fb923c, #facc15, #f97316);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .hs-badge {
        background: linear-gradient(135deg, rgba(249,115,22,0.15), rgba(234,179,8,0.12));
        border: 1px solid rgba(249,115,22,0.25);
    }
</style>
@endpush

@section('content')

{{-- ============================================================ --}}
{{-- HERO                                                          --}}
{{-- ============================================================ --}}
<section class="hs-hero-bg min-h-[90vh] flex items-center">
    <div class="max-w-6xl mx-auto px-6 py-28">
        <div class="grid md:grid-cols-2 gap-16 items-center">

            {{-- Left: Copy --}}
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full hs-badge text-orange-300 text-xs font-semibold mb-7">
                    <span class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></span>
                    Built for Home Service Pros
                </div>

                <h1 class="text-4xl md:text-5xl lg:text-6xl font-black text-white leading-[1.08] tracking-tight mb-6">
                    End the back-and-forth.<br>
                    Book more jobs<br>
                    <span class="hs-gradient-text">automatically.</span>
                </h1>

                <p class="text-gray-300 text-lg leading-relaxed mb-8 max-w-lg">
                    Homeowners book service calls online — they pick a window, describe the issue, and include their address.
                    It hits your Google Calendar instantly. You show up. No phone tag. No missed calls.
                </p>

                <div class="flex flex-col sm:flex-row gap-3 mb-10">
                    <a href="{{ $bookHref }}?intent=home-services"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-orange-500 hover:bg-orange-400 text-white font-bold transition-colors shadow-xl shadow-orange-900/30 text-sm">
                        Book a 20-min demo &rarr;
                    </a>
                    <a href="{{ $loginHref }}"
                       class="inline-flex items-center justify-center gap-2 px-7 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">
                        Sign in to your account
                    </a>
                </div>

                <div class="flex items-center gap-3 text-gray-500 text-xs flex-wrap">
                    @foreach(['No credit card to start', 'Live in under a day', 'Works with Google Calendar'] as $p)
                        <span class="flex items-center gap-1 text-orange-400 font-medium">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $p }}
                        </span>
                        @if(!$loop->last)<span class="text-gray-700">·</span>@endif
                    @endforeach
                </div>
            </div>

            {{-- Right: Job booking preview card --}}
            <div class="relative">
                <div class="bg-white/5 border border-white/10 rounded-2xl p-6 shadow-2xl backdrop-blur-sm">

                    <div class="flex items-center justify-between mb-5 pb-4 border-b border-white/10">
                        <div>
                            <p class="text-white font-bold text-sm">Request a Service Call</p>
                            <p class="text-gray-400 text-xs mt-0.5">Plumbing · HVAC · Electrical · General</p>
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-orange-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Service type select --}}
                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-2">Service type</p>
                    <div class="grid grid-cols-2 gap-2 mb-4">
                        @foreach(['🔧 Plumbing', '❄️ HVAC', '⚡ Electrical', '🏠 General Repair'] as $i => $svc)
                            <button class="px-3 py-2 rounded-lg text-xs font-medium border transition-colors
                                {{ $i === 0
                                    ? 'bg-orange-500 text-white border-orange-400'
                                    : 'bg-white/5 text-gray-300 border-white/10 hover:border-orange-500/40' }}">
                                {{ $svc }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Time windows --}}
                    <p class="text-gray-400 text-xs font-semibold uppercase tracking-wider mb-2">Preferred window</p>
                    <div class="grid grid-cols-3 gap-2 mb-4">
                        @foreach(['Today AM', 'Today PM', 'Tomorrow AM', 'Tomorrow PM', 'This Week', 'Flexible'] as $i => $w)
                            <button class="px-2 py-2 rounded-lg text-xs font-medium border transition-colors
                                {{ $i === 1
                                    ? 'bg-orange-500 text-white border-orange-400 shadow-lg shadow-orange-900/30'
                                    : 'bg-white/5 text-gray-300 border-white/10 hover:border-orange-500/40' }}">
                                {{ $w }}
                            </button>
                        @endforeach
                    </div>

                    {{-- Address --}}
                    <div class="space-y-2 mb-4">
                        <div class="bg-white/5 border border-orange-500/30 rounded-lg px-3 py-2.5 flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 text-orange-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span class="text-orange-300 text-xs">456 Oak Ave, Hagerstown MD 21740</span>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-lg px-3 py-2.5">
                            <span class="text-gray-500 text-xs">Describe the issue briefly...</span>
                        </div>
                    </div>

                    <button class="w-full py-3 rounded-xl bg-orange-500 hover:bg-orange-400 text-white font-bold text-sm transition-colors shadow-lg shadow-orange-900/20">
                        Request Service Call &rarr;
                    </button>
                </div>

                {{-- Floating badge --}}
                <div class="absolute -bottom-4 -left-4 bg-gray-900 border border-orange-500/30 rounded-xl px-4 py-3 shadow-xl flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full bg-orange-500/20 flex items-center justify-center shrink-0">
                        <svg class="w-4 h-4 text-orange-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                    </div>
                    <div>
                        <p class="text-white text-xs font-bold">Job dispatched</p>
                        <p class="text-gray-400 text-xs">Calendar blocked · Client notified</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- HOW IT WORKS                                                  --}}
{{-- ============================================================ --}}
<section class="bg-gray-950 py-24 px-6 border-t border-white/5">
    <div class="max-w-4xl mx-auto text-center mb-16">
        <span class="text-orange-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">How it works</span>
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">
            From phone chaos to clean dispatch.
        </h2>
    </div>

    <div class="max-w-5xl mx-auto grid md:grid-cols-4 gap-6">
        @php
            $steps = [
                ['num' => '01', 'icon' => 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 'title' => 'You set up your page', 'body' => 'Connect Google Calendar, list your service types, and set your available windows in under 30 minutes.'],
                ['num' => '02', 'icon' => 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z', 'title' => 'Homeowner books online', 'body' => 'They pick a service type, choose a time window, enter their address and describe the issue.'],
                ['num' => '03', 'icon' => 'M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9', 'title' => 'You get notified', 'body' => 'Email notification with job details, client address, and issue description. No more missed calls.'],
                ['num' => '04', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4', 'title' => 'Job tracked in dashboard', 'body' => 'View all upcoming jobs, client history, and job notes in your admin dashboard.'],
            ];
        @endphp

        @foreach($steps as $step)
            <div class="hs-card-hover bg-white/3 border border-white/8 rounded-2xl p-6 text-center">
                <div class="w-10 h-10 rounded-xl bg-orange-500/15 border border-orange-500/20 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"/>
                    </svg>
                </div>
                <span class="text-orange-500/60 text-xs font-black uppercase tracking-widest mb-2 block">{{ $step['num'] }}</span>
                <h3 class="text-white font-bold text-sm mb-2">{{ $step['title'] }}</h3>
                <p class="text-gray-400 text-xs leading-relaxed">{{ $step['body'] }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ============================================================ --}}
{{-- FEATURES                                                      --}}
{{-- ============================================================ --}}
<section class="bg-[#06101d] py-24 px-6 border-t border-white/5">
    <div class="max-w-6xl mx-auto">
        <div class="text-center mb-16">
            <span class="text-orange-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">What you get</span>
            <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">
                Everything a growing home service business needs.
            </h2>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
            @php
                $features = [
                    ['color' => 'orange', 'title' => 'Service Dispatch Booking', 'body' => 'Clients book a service window, select job type, and provide their address. It syncs to your Google Calendar with all details.', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['color' => 'blue', 'title' => 'Service Area Forms', 'body' => 'Capture service address, job type, description, and urgency level — exactly the intake data you need before showing up.', 'icon' => 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z M15 11a3 3 0 11-6 0 3 3 0 016 0z'],
                    ['color' => 'emerald', 'title' => 'Instant Confirmations', 'body' => 'Automatic email confirmation goes to the homeowner the moment they book. Includes job details and your contact info.', 'icon' => 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'],
                    ['color' => 'yellow', 'title' => 'Job & Lead Tracking', 'body' => "Every booking and inquiry is logged in your CRM. See who's waiting on a callback and who's ready to be scheduled.", 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
                    ['color' => 'sky', 'title' => 'Google Calendar Blocks', 'body' => 'Each job automatically creates a calendar event with the address, job type, and notes — no manual data entry.', 'icon' => 'M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z'],
                    ['color' => 'violet', 'title' => 'Client History', 'body' => "When a return customer books, you see their job history in the portal. Know what you've done before you arrive.", 'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
                ];
                $colorMap = [
                    'orange'  => ['bg' => 'bg-orange-500/10',  'border' => 'border-orange-500/20',  'text' => 'text-orange-400',  'hover' => 'hover:border-orange-500/30'],
                    'blue'    => ['bg' => 'bg-blue-500/10',    'border' => 'border-blue-500/20',    'text' => 'text-blue-400',    'hover' => 'hover:border-blue-500/30'],
                    'emerald' => ['bg' => 'bg-emerald-500/10', 'border' => 'border-emerald-500/20', 'text' => 'text-emerald-400', 'hover' => 'hover:border-emerald-500/30'],
                    'yellow'  => ['bg' => 'bg-yellow-500/10',  'border' => 'border-yellow-500/20',  'text' => 'text-yellow-400',  'hover' => 'hover:border-yellow-500/30'],
                    'sky'     => ['bg' => 'bg-sky-500/10',     'border' => 'border-sky-500/20',     'text' => 'text-sky-400',     'hover' => 'hover:border-sky-500/30'],
                    'violet'  => ['bg' => 'bg-violet-500/10',  'border' => 'border-violet-500/20',  'text' => 'text-violet-400',  'hover' => 'hover:border-violet-500/30'],
                ];
            @endphp

            @foreach($features as $feature)
                @php $c = $colorMap[$feature['color']]; @endphp
                <div class="hs-card-hover bg-white/3 border border-white/8 {{ $c['hover'] }} rounded-2xl p-6">
                    <div class="w-10 h-10 rounded-xl {{ $c['bg'] }} border {{ $c['border'] }} flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 {{ $c['text'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $feature['icon'] }}"/>
                        </svg>
                    </div>
                    <h3 class="text-white font-bold text-sm mb-2">{{ $feature['title'] }}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">{{ $feature['body'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- WHO IT'S FOR                                                  --}}
{{-- ============================================================ --}}
<section class="bg-gray-950 py-24 px-6 border-t border-white/5">
    <div class="max-w-4xl mx-auto text-center mb-14">
        <span class="text-orange-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">Who it's for</span>
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">
            Any trade. Any specialty.
        </h2>
    </div>
    <div class="max-w-3xl mx-auto grid grid-cols-2 sm:grid-cols-3 gap-3">
        @foreach(['Plumbers', 'HVAC Technicians', 'Electricians', 'General Contractors', 'Landscapers', 'Pest Control', 'Appliance Repair', 'Roofers', 'Painters', 'Cleaners', 'Pool Service', 'Handymen'] as $trade)
            <div class="bg-white/3 border border-white/8 rounded-xl px-4 py-3 text-center">
                <p class="text-gray-300 text-sm font-medium">{{ $trade }}</p>
            </div>
        @endforeach
    </div>
</section>

{{-- ============================================================ --}}
{{-- PRICING                                                       --}}
{{-- ============================================================ --}}
<section class="bg-[#06101d] py-24 px-6 border-t border-white/5">
    <div class="max-w-4xl mx-auto text-center mb-16">
        <span class="text-orange-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">Simple pricing</span>
        <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">
            One plan. No surprises.
        </h2>
        <p class="text-gray-400 mt-4 text-lg">Less than one missed emergency call would cover months of this.</p>
    </div>

    <div class="max-w-md mx-auto">
        <div class="bg-white/5 border border-orange-500/30 rounded-2xl p-8 shadow-2xl shadow-orange-900/10 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-orange-500/5 to-transparent pointer-events-none"></div>

            <div class="relative">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <p class="text-orange-400 text-xs font-black uppercase tracking-widest mb-1">Pro Plan</p>
                        <div class="flex items-end gap-1">
                            <span class="text-5xl font-black text-white">$79</span>
                            <span class="text-gray-400 text-sm mb-2">/month</span>
                        </div>
                    </div>
                    <div class="px-3 py-1.5 rounded-full bg-orange-500/15 border border-orange-500/30 text-orange-300 text-xs font-bold">
                        Most Popular
                    </div>
                </div>

                <ul class="space-y-3 mb-8">
                    @foreach([
                        'Google Calendar service dispatch',
                        'Service type & job description forms',
                        'Automated confirmation emails',
                        'Lead & job tracking CRM',
                        'Contact form with notifications',
                        'Admin dashboard',
                        'Unlimited bookings per month',
                        'Client history & notes',
                        'Setup support included',
                    ] as $item)
                        <li class="flex items-start gap-3 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-orange-400 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>

                <a href="{{ $bookHref }}?intent=home-services-signup"
                   class="block w-full text-center py-4 rounded-xl bg-orange-500 hover:bg-orange-400 text-white font-bold transition-colors shadow-lg shadow-orange-900/30 text-sm">
                    Get started today &rarr;
                </a>
                <p class="text-center text-gray-500 text-xs mt-4">30-day money-back guarantee. Cancel anytime.</p>
            </div>
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- FAQ                                                           --}}
{{-- ============================================================ --}}
<section class="bg-gray-950 py-24 px-6 border-t border-white/5" x-data="{ open: null }">
    <div class="max-w-3xl mx-auto">
        <div class="text-center mb-14">
            <span class="text-orange-400 text-xs font-black uppercase tracking-[0.2em] mb-4 block">FAQ</span>
            <h2 class="text-3xl md:text-4xl font-black text-white tracking-tight">Common questions</h2>
        </div>

        @php
            $faqs = [
                ['q' => 'Do I need a website already?', 'a' => 'No. smbgen gives you a booking page and contact form you can link from your Google Business Profile, social media, or anywhere online. A full website is optional.'],
                ['q' => 'What info does the booking form collect?', 'a' => 'Service type, preferred time window, service address, contact details, and a description of the issue. You can customize which fields appear.'],
                ['q' => 'How does calendar blocking work?', 'a' => 'When a homeowner books, a Google Calendar event is created with the job address, type, and details automatically. You see it immediately in your calendar.'],
                ['q' => 'Can multiple techs use the same account?', 'a' => 'Yes. You can manage multiple users and connect multiple Google Calendars — useful if you have a team of technicians.'],
                ['q' => 'What about emergency / same-day calls?', 'a' => "You can configure 'Today AM' and 'Today PM' windows so urgent calls still book properly, or keep a contact number visible for true emergencies."],
                ['q' => 'Is setup hard?', 'a' => 'No. Most home service pros are live in under an hour. We walk you through connecting Google Calendar, setting your service types, and sharing your booking link. Setup support is included.'],
            ];
        @endphp

        <div class="space-y-3">
            @foreach($faqs as $i => $faq)
                <div class="bg-white/3 border border-white/8 rounded-xl overflow-hidden">
                    <button
                        @click="open = open === {{ $i }} ? null : {{ $i }}"
                        class="w-full flex items-center justify-between px-6 py-4 text-left text-white font-semibold text-sm hover:text-orange-300 transition-colors">
                        <span>{{ $faq['q'] }}</span>
                        <svg class="w-4 h-4 shrink-0 transition-transform duration-200 text-gray-400"
                             :class="open === {{ $i }} ? 'rotate-45 text-orange-400' : ''"
                             fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                    </button>
                    <div x-show="open === {{ $i }}" x-transition x-cloak class="px-6 pb-5">
                        <p class="text-gray-400 text-sm leading-relaxed">{{ $faq['a'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============================================================ --}}
{{-- FINAL CTA                                                     --}}
{{-- ============================================================ --}}
<section class="bg-[#06101d] py-28 px-6 border-t border-white/5">
    <div class="max-w-3xl mx-auto text-center">

        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full hs-badge text-orange-300 text-xs font-semibold mb-8">
            <span class="w-1.5 h-1.5 rounded-full bg-orange-400 animate-pulse"></span>
            Ready to get started?
        </div>

        <h2 class="text-4xl md:text-5xl font-black text-white mb-5 tracking-tight">
            Your first booked job<br>could be today.
        </h2>

        <p class="text-gray-400 text-lg mb-10 leading-relaxed">
            Book a 20-minute call and we'll get your Google Calendar connected,
            your service types configured, and your booking link ready to share — same day.
        </p>

        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ $bookHref }}?intent=home-services"
               class="px-8 py-4 rounded-xl bg-orange-500 hover:bg-orange-400 text-white font-bold transition-colors shadow-xl shadow-orange-900/30 text-sm">
                Book a 20-min demo &rarr;
            </a>
            <a href="{{ $contactHref }}?topic=home-services"
               class="px-8 py-4 rounded-xl bg-white/5 hover:bg-white/10 text-gray-200 font-semibold border border-white/10 transition-colors text-sm">
                Send a question first
            </a>
        </div>

        <p class="text-gray-600 text-xs mt-8">$79/month · Cancel anytime · Setup included</p>
    </div>
</section>

@endsection
