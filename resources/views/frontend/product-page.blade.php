@extends('layouts.frontend')

@php
    $portalHref = auth()->check()
        ? (auth()->user()->isAdministrator() ? route('admin.dashboard') : route('dashboard'))
        : route('login');

    $bookHref    = Route::has('booking.wizard') ? route('booking.wizard') : route('contact');
    $contactHref = route('contact');

    $products = [
        'contact-core' => [
            'number'      => '01',
            'title'       => 'Contact',
            'slug'        => 'contact',
            'headline'    => 'A contact form that qualifies before it routes.',
            'subhead'     => 'Structured intake instead of a generic box',
            'stat'        => ['value' => '3×', 'label' => 'higher qualification rate vs generic forms'],
            'body'        => 'The contact page should do more than collect a name and email. smbgen-core gives you a structured intake experience that qualifies leads, routes the right requests, and makes the next step obvious — without your team sorting through noise.',
            'points'      => [
                'Structured intake instead of a generic form',
                'Smarter lead qualification before the sales conversation',
                'Cleaner handoff into booking, CRM, and follow-up',
                'Spam filtering and routing rules built in',
            ],
            'steps' => [
                ['num' => '01', 'title' => 'Customer fills intake', 'body' => 'A multi-step form collects exactly what you need — name, email, project type, budget, timeline — with validation and smart branching.'],
                ['num' => '02', 'title' => 'Request is qualified and routed', 'body' => 'Rules you define automatically tag, score, and route the submission — to the right team member, to the CRM, or to a booking confirmation.'],
                ['num' => '03', 'title' => 'Team gets a clean lead', 'body' => 'Your team sees a fully-formed lead record with context, not raw form data. Ready to act, not ready to interpret.'],
            ],
            'features' => [
                ['title' => 'Multi-step intake', 'body' => 'Break long forms into logical steps so completion rates stay high.'],
                ['title' => 'Smart routing rules', 'body' => 'Route to team members, tags, or CRM pipelines based on answers.'],
                ['title' => 'Spam filtering', 'body' => 'Honeypot + rate limiting keep your inbox clean.'],
                ['title' => 'CRM handoff', 'body' => 'Every submission creates or updates a contact record automatically.'],
                ['title' => 'Custom confirmation', 'body' => 'Send a branded confirmation email and set the next-step expectation.'],
                ['title' => 'Booking trigger', 'body' => 'Qualify the lead, then route directly into the booking flow.'],
            ],
            'use_cases' => [
                ['who' => 'Service businesses', 'what' => 'Replace the contact@yourdomain.com catch-all with a structured intake that tells you what a lead actually needs.'],
                ['who' => 'Agencies', 'what' => 'Qualify project type, budget, and timeline before the first call — stop wasting discovery time.'],
                ['who' => 'Consultants', 'what' => 'Gate your calendar behind a qualifying form so every booking is worth your time.'],
            ],
            'primaryLabel'   => 'Open contact flow',
            'primaryHref'    => null,
            'secondaryLabel' => 'Book a discovery call',
            'surface'    => 'background: radial-gradient(ellipse at 70% 0%, rgba(37,99,235,0.18) 0%, transparent 60%), #06101d; border: 1px solid rgba(37,99,235,0.22);',
            'badgeClass' => 'bg-blue-600/20 border-blue-500/30 text-blue-300',
            'accentClass'=> 'text-blue-400',
            'accentBg'   => 'bg-blue-600',
            'accentFaint'=> 'bg-blue-600/10 border-blue-500/20',
            'buttonClass'=> 'bg-blue-700 hover:bg-blue-600 border-blue-600/40',
            'checkClass' => 'bg-blue-600/20 border-blue-600/40 text-blue-400',
            'visual'     => 'contact',
        ],
        'book-core' => [
            'number'      => '02',
            'title'       => 'Book',
            'slug'        => 'book',
            'headline'    => 'A booking flow people actually complete.',
            'subhead'     => 'Scheduling that converts interest into appointments',
            'stat'        => ['value' => '68%', 'label' => 'of bookings happen outside business hours'],
            'body'        => 'Booking should feel fast, clear, and trustworthy. smbgen-core handles availability, appointment selection, confirmations, and follow-through so customers move from interest to action without friction — and without you managing it manually.',
            'points'      => [
                'Availability and scheduling in one clear path',
                'Confirmations and reminders built in',
                'Booking tied directly to the client record',
                'Google Calendar sync for real-time availability',
            ],
            'steps' => [
                ['num' => '01', 'title' => 'Customer picks a day', 'body' => 'A clean calendar view shows real availability from your Google Calendar — no double-booking, no manual updates.'],
                ['num' => '02', 'title' => 'Selects a time and fills details', 'body' => 'They choose a slot, confirm their timezone, and complete the intake fields you configured — all in one flow.'],
                ['num' => '03', 'title' => 'Confirmation and reminders sent', 'body' => 'Both parties get a confirmation immediately. Reminders fire automatically before the appointment.'],
            ],
            'features' => [
                ['title' => 'Real-time availability', 'body' => 'Pulled live from Google Calendar — no gap between what you\'re showing and what\'s actually open.'],
                ['title' => 'Timezone detection', 'body' => 'Auto-detects the customer\'s timezone so times display correctly without extra steps.'],
                ['title' => 'Automated reminders', 'body' => 'Reduce no-shows with email reminders sent 24 hours and 1 hour before the appointment.'],
                ['title' => 'Custom intake fields', 'body' => 'Collect project type, company size, or any context you need before the call.'],
                ['title' => 'CRM + portal link', 'body' => 'Every booking auto-creates a client record and appears in the admin dashboard.'],
                ['title' => 'Blackout dates', 'body' => 'Block holidays, travel, or off-days — they disappear from the availability view automatically.'],
            ],
            'use_cases' => [
                ['who' => 'Consultants & coaches', 'what' => 'Replace Calendly with a booking experience that lives on your own domain and feeds your own CRM.'],
                ['who' => 'Service businesses', 'what' => 'Accept appointments 24/7 without playing phone tag — availability is always accurate and up to date.'],
                ['who' => 'Sales teams', 'what' => 'Shorten the time from "interested" to "scheduled" — put a booking link on every outbound sequence.'],
            ],
            'primaryLabel'   => 'Try the booking flow',
            'primaryHref'    => null,
            'secondaryLabel' => 'Book a discovery call',
            'surface'    => 'background: radial-gradient(ellipse at 30% 0%, rgba(139,92,246,0.18) 0%, transparent 60%), #0a0616; border: 1px solid rgba(139,92,246,0.2);',
            'badgeClass' => 'bg-violet-600/20 border-violet-500/30 text-violet-300',
            'accentClass'=> 'text-violet-400',
            'accentBg'   => 'bg-violet-600',
            'accentFaint'=> 'bg-violet-600/10 border-violet-500/20',
            'buttonClass'=> 'bg-violet-700 hover:bg-violet-600 border-violet-600/40',
            'checkClass' => 'bg-violet-600/20 border-violet-600/40 text-violet-400',
            'visual'     => 'book',
        ],
        'pay-core' => [
            'number'      => '03',
            'title'       => 'Pay',
            'slug'        => 'pay',
            'headline'    => 'A payment experience that gets money in faster.',
            'subhead'     => 'Simple, trustworthy, and tied to your workflow',
            'stat'        => ['value' => '2.4×', 'label' => 'faster collection vs emailed PDF invoices'],
            'body'        => 'The payment step should remove hesitation, not create it. smbgen-core gives you a cleaner payment path tied to the actual customer workflow — from invoice or request to confirmation — without sending customers to a third-party portal they don\'t trust.',
            'points'      => [
                'Simple payment collection customers understand instantly',
                'Cleaner handoff from booking or approval into payment',
                'Invoice history accessible from the client portal',
                'A better trust signal than scattered third-party links',
            ],
            'steps' => [
                ['num' => '01', 'title' => 'Invoice created or trigger fires', 'body' => 'An admin creates an invoice or a booking/approval automatically triggers a payment request — with the right amount, description, and due date.'],
                ['num' => '02', 'title' => 'Customer receives a payment link', 'body' => 'A branded, mobile-optimised payment page lands in their inbox or portal. No confusion about where to go or who they\'re paying.'],
                ['num' => '03', 'title' => 'Payment processed, records updated', 'body' => 'Funds collected, invoice marked paid, client record updated, confirmation sent. Nothing falls through the cracks.'],
            ],
            'features' => [
                ['title' => 'Branded payment page', 'body' => 'Your logo, your colors — customers know exactly who they\'re paying.'],
                ['title' => 'Invoice generation', 'body' => 'Create itemised invoices from the admin dashboard or trigger them automatically.'],
                ['title' => 'Payment history', 'body' => 'Full record accessible from the client portal and admin CRM.'],
                ['title' => 'Automatic receipts', 'body' => 'Customers get a branded receipt immediately after paying.'],
                ['title' => 'Overdue reminders', 'body' => 'Scheduled follow-up emails fire automatically for unpaid invoices.'],
                ['title' => 'Portal integration', 'body' => 'Billing shows up inside the client portal — no separate login required.'],
            ],
            'use_cases' => [
                ['who' => 'Professional services', 'what' => 'Stop chasing payments over email — send a link that actually gets paid.'],
                ['who' => 'Agencies on retainer', 'what' => 'Automate monthly invoicing and let clients pay from the same portal they use for everything else.'],
                ['who' => 'Project-based businesses', 'what' => 'Tie deposit and final payment triggers to booking confirmation and project completion.'],
            ],
            'primaryLabel'   => Route::has('payment.collect') ? 'Open payment flow' : 'Talk to us about Pay',
            'primaryHref'    => Route::has('payment.collect') ? route('payment.collect') : null,
            'secondaryLabel' => 'Book a discovery call',
            'surface'    => 'background: radial-gradient(ellipse at 70% 0%, rgba(16,185,129,0.15) 0%, transparent 60%), #04150f; border: 1px solid rgba(16,185,129,0.22);',
            'badgeClass' => 'bg-emerald-600/20 border-emerald-500/30 text-emerald-300',
            'accentClass'=> 'text-emerald-400',
            'accentBg'   => 'bg-emerald-600',
            'accentFaint'=> 'bg-emerald-600/10 border-emerald-500/20',
            'buttonClass'=> 'bg-emerald-700 hover:bg-emerald-600 border-emerald-600/40',
            'checkClass' => 'bg-emerald-600/20 border-emerald-600/40 text-emerald-400',
            'visual'     => 'pay',
        ],
        'portal-core' => [
            'number'      => '04',
            'title'       => 'Client Portal',
            'slug'        => 'portal',
            'headline'    => 'One place for your clients after the sale.',
            'subhead'     => 'A professional post-sale experience that builds trust',
            'stat'        => ['value' => '60%', 'label' => 'reduction in "what\'s the status?" emails'],
            'body'        => 'Clients need one clear place to go after they sign. smbgen-core gives them a real portal — login, files, messages, billing, and project status in one view — so they feel taken care of and your team spends less time on status updates.',
            'points'      => [
                'One login for updates, files, and next steps',
                'A cleaner client experience from day one',
                'Less manual status communication for your team',
                'Live messaging and document approvals in one place',
            ],
            'steps' => [
                ['num' => '01', 'title' => 'Client is invited and registers', 'body' => 'After booking or contract sign, the client receives an invitation. One click, they set their password and they\'re in.'],
                ['num' => '02', 'title' => 'They see their dedicated space', 'body' => 'Their portal shows exactly what\'s relevant to them — project status, files uploaded for them, messages, and billing history.'],
                ['num' => '03', 'title' => 'Ongoing collaboration in one place', 'body' => 'Messages, approvals, invoice payments, and file updates all happen here — not scattered across email threads.'],
            ],
            'features' => [
                ['title' => 'Secure client login', 'body' => 'Each client has their own space — no shared passwords, no seeing other clients\' work.'],
                ['title' => 'File sharing + approvals', 'body' => 'Upload deliverables, get sign-off, and track version history without emailing attachments.'],
                ['title' => 'Direct messaging', 'body' => 'A thread per client, visible to your whole team — no context lost in individual inboxes.'],
                ['title' => 'Billing view', 'body' => 'Clients see invoices and can pay directly from from the portal.'],
                ['title' => 'Project status', 'body' => 'Show progress stages so clients always know where things stand without asking.'],
                ['title' => 'Email verification', 'body' => 'Portal access is gated behind email verification — no surprises.'],
            ],
            'use_cases' => [
                ['who' => 'Creative agencies', 'what' => 'Replace the Dropbox-link-in-email workflow with a real client experience that reflects your brand.'],
                ['who' => 'Professional services', 'what' => 'Give clients a single place for documents, billing, and communications — cut the status update calls.'],
                ['who' => 'Development shops', 'what' => 'Keep clients in the loop on milestones and deliverables without switching to a project management tool they won\'t use.'],
            ],
            'primaryLabel'   => auth()->check() ? 'Open portal' : 'View portal access',
            'primaryHref'    => $portalHref,
            'secondaryLabel' => 'Book a discovery call',
            'surface'    => 'background: radial-gradient(ellipse at 30% 100%, rgba(249,115,22,0.18) 0%, transparent 55%), #140900; border: 1px solid rgba(249,115,22,0.22);',
            'badgeClass' => 'bg-orange-600/20 border-orange-500/30 text-orange-300',
            'accentClass'=> 'text-orange-400',
            'accentBg'   => 'bg-orange-600',
            'accentFaint'=> 'bg-orange-600/10 border-orange-500/20',
            'buttonClass'=> 'bg-orange-700 hover:bg-orange-600 border-orange-600/40',
            'checkClass' => 'bg-orange-600/20 border-orange-600/40 text-orange-400',
            'visual'     => 'portal',
        ],
        'crm-core' => [
            'number'      => '05',
            'title'       => 'CRM',
            'slug'        => 'crm',
            'headline'    => 'A CRM your team will actually use.',
            'subhead'     => 'Leads, contacts, and deals in one operating layer',
            'stat'        => ['value' => '41%', 'label' => 'more revenue from teams with CRM visibility'],
            'body'        => 'Contact records, opportunities, notes, follow-ups, and deal visibility all live in one system. smbgen-core keeps the commercial side of the business visible and actionable — without duplicate data entry or switching between tools.',
            'points'      => [
                'Lead and contact history in one place',
                'Follow-up visibility across the whole team',
                'Connected to contact, booking, portal, and payment activity',
                'AI-powered lead scoring to prioritise outreach',
            ],
            'steps' => [
                ['num' => '01', 'title' => 'Lead comes in via contact or booking', 'body' => 'Every form submission or booking automatically creates or updates a contact record — you never start from a blank slate.'],
                ['num' => '02', 'title' => 'Team works the pipeline', 'body' => 'Assign leads, log notes, set follow-up tasks, and move deals through custom stages — all visible to the whole commercial team.'],
                ['num' => '03', 'title' => 'Deal closes, portal and billing activate', 'body' => 'When a deal moves to won, the client portal invite and first invoice can trigger automatically — no handoff friction.'],
            ],
            'features' => [
                ['title' => 'Contact timeline', 'body' => 'See every touchpoint — form submissions, bookings, messages, payments — in chronological order.'],
                ['title' => 'Pipeline stages', 'body' => 'Custom stages that match your actual sales process, not a generic template.'],
                ['title' => 'Follow-up tasks', 'body' => 'Assign reminders to team members so nothing falls through the cracks.'],
                ['title' => 'AI lead scoring', 'body' => 'ML-powered scoring ranks leads by conversion probability so you focus on the right ones first.'],
                ['title' => 'Deal tracking', 'body' => 'Value, probability, and expected close date — all in one view.'],
                ['title' => 'Activity log', 'body' => 'Full history of who did what and when, across every client record.'],
            ],
            'use_cases' => [
                ['who' => 'Small sales teams', 'what' => 'Get visibility across the pipeline without a Salesforce-sized budget or a Salesforce-sized implementation.'],
                ['who' => 'Owner-operators', 'what' => 'Stop running your business out of your inbox — know exactly which leads need attention today.'],
                ['who' => 'Growing agencies', 'what' => 'Track new business alongside active client work without two separate tools and duplicated contacts.'],
            ],
            'primaryLabel'   => 'Talk to us about CRM',
            'primaryHref'    => null,
            'secondaryLabel' => 'Book a discovery call',
            'surface'    => 'background: radial-gradient(ellipse at 65% 0%, rgba(99,102,241,0.16) 0%, transparent 55%), #06081a; border: 1px solid rgba(99,102,241,0.2);',
            'badgeClass' => 'bg-indigo-600/20 border-indigo-500/30 text-indigo-300',
            'accentClass'=> 'text-indigo-400',
            'accentBg'   => 'bg-indigo-600',
            'accentFaint'=> 'bg-indigo-600/10 border-indigo-500/20',
            'buttonClass'=> 'bg-indigo-700 hover:bg-indigo-600 border-indigo-600/40',
            'checkClass' => 'bg-indigo-600/20 border-indigo-600/40 text-indigo-400',
            'visual'     => 'crm',
        ],
        'cms-core' => [
            'number'      => '06',
            'title'       => 'CMS',
            'slug'        => 'cms',
            'headline'    => 'Publish without waiting on a developer.',
            'subhead'     => 'Update pages without waiting on dev time',
            'stat'        => ['value' => '5×', 'label' => 'faster content updates vs dev-dependent workflows'],
            'body'        => 'Your team should be able to update pages, publish offers, manage media, and keep the site current — without waiting on a developer for every change. smbgen-core makes content operations manageable and keeps your site moving at business speed.',
            'points'      => [
                'Page editing without bottlenecking on developers',
                'Faster turnaround on updates, offers, and landing pages',
                'Media library and asset management built in',
                'Content tied to the same core system as the rest of the business',
            ],
            'steps' => [
                ['num' => '01', 'title' => 'Open the page editor', 'body' => 'Any admin can log in and open the page they want to update — no code, no FTP, no deployment needed.'],
                ['num' => '02', 'title' => 'Edit content and upload assets', 'body' => 'Update copy, swap images, add sections or offers — the editor handles HTML output and SEO fields automatically.'],
                ['num' => '03', 'title' => 'Publish or schedule', 'body' => 'Go live immediately or schedule the publish for the right moment — changes are versioned so you can roll back if needed.'],
            ],
            'features' => [
                ['title' => 'Visual page editor', 'body' => 'Edit what you see — no context-switching between a backend and a frontend.'],
                ['title' => 'Media library', 'body' => 'Centralised asset management — upload once, use everywhere, no broken image links.'],
                ['title' => 'SEO fields', 'body' => 'Title, description, and canonical URL editable per page without touching a config file.'],
                ['title' => 'Blog system', 'body' => 'Full publish–schedule–archive workflow for blog posts and content marketing.'],
                ['title' => 'Landing pages', 'body' => 'Create campaign-specific landing pages without spinning up new projects.'],
                ['title' => 'Revision history', 'body' => 'Every save is versioned — roll back to any previous state without drama.'],
            ],
            'use_cases' => [
                ['who' => 'Marketing teams', 'what' => 'Own your own content updates — don\'t raise a dev ticket every time a price or offer changes.'],
                ['who' => 'Service businesses', 'what' => 'Keep the site current with promotions, team bios, and service descriptions without engineering involvement.'],
                ['who' => 'Content-driven businesses', 'what' => 'Publish, schedule, and manage a blog alongside the rest of your operations — no separate WordPress install.'],
            ],
            'primaryLabel'   => 'Talk to us about CMS',
            'primaryHref'    => null,
            'secondaryLabel' => 'Book a discovery call',
            'surface'    => 'background: radial-gradient(ellipse at 60% 0%, rgba(6,182,212,0.14) 0%, transparent 55%), #021118; border: 1px solid rgba(6,182,212,0.2);',
            'badgeClass' => 'bg-cyan-600/20 border-cyan-500/30 text-cyan-300',
            'accentClass'=> 'text-cyan-400',
            'accentBg'   => 'bg-cyan-600',
            'accentFaint'=> 'bg-cyan-600/10 border-cyan-500/20',
            'buttonClass'=> 'bg-cyan-700 hover:bg-cyan-600 border-cyan-600/40',
            'checkClass' => 'bg-cyan-600/20 border-cyan-600/40 text-cyan-400',
            'visual'     => 'cms',
        ],
    ];

    $page       = $products[$productId] ?? $products['contact-core'];
    $primaryUrl = $page['primaryHref'] ?? ($productId === 'book-core' ? $bookHref : $contactHref);
@endphp

@section('title', 'smbgen-core — ' . $page['title'] . ': ' . $page['subhead'])
@section('description', $page['body'])

@push('head')
<style>
.product-page-bg { background: #03040d; }
.step-line::after {
    content: '';
    position: absolute;
    top: 1.25rem;
    left: 50%;
    transform: translateX(-50%);
    width: 2px;
    height: calc(100% + 2rem);
    background: linear-gradient(to bottom, rgba(255,255,255,0.08), transparent);
}
</style>
@endpush

@section('content')
<div class="product-page-bg">

    {{-- ── Hero ──────────────────────────────────────────────────────── --}}
    <section class="px-6 pt-20 pb-16">
        <div class="max-w-6xl mx-auto">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-xs text-gray-500 mb-10">
                <a href="{{ route('home') }}" class="hover:text-gray-300 transition-colors">Home</a>
                <span>/</span>
                <a href="{{ route('solutions') }}" class="hover:text-gray-300 transition-colors">Solutions</a>
                <span>/</span>
                <span class="{{ $page['accentClass'] }} font-semibold">{{ $page['title'] }}</span>
            </nav>

            <div class="grid md:grid-cols-2 gap-12 items-center">
                {{-- Left: copy --}}
                <div>
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-white/10 bg-white/5 text-gray-400 text-xs font-bold uppercase tracking-widest mb-7">
                        <span class="w-1.5 h-1.5 rounded-full {{ str_replace('text-', 'bg-', $page['accentClass']) }} animate-pulse inline-block"></span>
                        smbgen-core &mdash; {{ $page['number'] }}
                    </div>
                    <h1 class="text-5xl md:text-6xl font-black text-white leading-[1.05] tracking-tight mb-5">
                        {{ $page['headline'] }}
                    </h1>
                    <p class="text-gray-400 text-lg leading-relaxed mb-8 max-w-lg">
                        {{ $page['body'] }}
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="{{ $primaryUrl }}" class="px-7 py-3.5 rounded-xl text-white font-black uppercase tracking-wider text-sm transition-colors border shadow-lg {{ $page['buttonClass'] }}">
                            {{ $page['primaryLabel'] }} &rarr;
                        </a>
                        <a href="{{ $bookHref }}" class="{{ $page['accentClass'] }} border border-current/30 font-bold px-7 py-3.5 rounded-xl hover:bg-white/5 transition-colors text-sm">
                            {{ $page['secondaryLabel'] }}
                        </a>
                    </div>
                </div>

                {{-- Right: stat + key points --}}
                <div class="space-y-5">
                    {{-- Stat card --}}
                    <div class="rounded-2xl p-7 border {{ $page['accentFaint'] }}">
                        <div class="text-5xl font-black {{ $page['accentClass'] }} mb-1">{{ $page['stat']['value'] }}</div>
                        <div class="text-gray-400 text-sm leading-snug">{{ $page['stat']['label'] }}</div>
                    </div>
                    {{-- Points --}}
                    <div class="rounded-2xl p-6 border border-white/5 bg-white/[0.03] space-y-3">
                        @foreach($page['points'] as $point)
                            <div class="flex items-center gap-3 text-gray-300 text-sm">
                                <span class="w-4 h-4 rounded border flex items-center justify-center text-[10px] shrink-0 {{ $page['checkClass'] }}">&#10003;</span>
                                {{ $point }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── Interactive preview card ───────────────────────────────────── --}}
    <section class="px-6 pb-20">
        <div class="max-w-6xl mx-auto">
            <div class="rounded-3xl overflow-hidden" style="{{ $page['surface'] }}">
                <div class="grid md:grid-cols-2 gap-0">
                    {{-- Feature copy --}}
                    <div class="p-10 md:p-14 flex flex-col justify-center">
                        <div class="flex items-center gap-3 mb-7">
                            <div class="w-10 h-10 rounded-xl border flex items-center justify-center {{ $page['badgeClass'] }}">
                                <span class="text-xs font-black">{{ $page['number'] }}</span>
                            </div>
                            <div>
                                <span class="{{ $page['accentClass'] }} text-[10px] font-bold uppercase tracking-[0.25em]">smbgen-core</span>
                                <div class="text-white font-black text-xl uppercase tracking-widest">{{ $page['title'] }}</div>
                            </div>
                        </div>
                        <h2 class="text-3xl font-black text-white leading-tight tracking-tight mb-5">
                            {{ $page['subhead'] }}
                        </h2>
                        <div class="space-y-2.5 mb-8">
                            @foreach($page['points'] as $point)
                                <div class="flex items-center gap-2.5 text-gray-300 text-sm">
                                    <span class="w-4 h-4 rounded border flex items-center justify-center text-[10px] shrink-0 {{ $page['checkClass'] }}">&#10003;</span>
                                    {{ $point }}
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center gap-4">
                            <a href="{{ $primaryUrl }}" class="px-6 py-3 rounded-xl text-white font-black uppercase tracking-wider text-sm transition-colors border {{ $page['buttonClass'] }}">
                                {{ $page['primaryLabel'] }} &rarr;
                            </a>
                            <a href="{{ route('solutions') }}" class="{{ $page['accentClass'] }} text-sm font-semibold hover:text-white transition-colors">All solutions</a>
                        </div>
                    </div>

                    {{-- Visual preview --}}
                    <div class="p-10 md:p-14 flex items-center">
                        <div class="w-full rounded-2xl p-6" style="background:rgba(255,255,255,0.025);border:1px solid rgba(255,255,255,0.06);">
                            @if($page['visual'] === 'contact')
                                <div class="{{ $page['accentClass'] }} text-[10px] font-black uppercase tracking-[0.2em] mb-5">Lead intake preview</div>
                                <div class="space-y-3">
                                    <div class="h-10 rounded-xl bg-white/5"></div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="h-10 rounded-xl bg-white/5"></div>
                                        <div class="h-10 rounded-xl bg-white/5"></div>
                                    </div>
                                    <div class="h-10 rounded-xl bg-white/5"></div>
                                    <div class="flex items-center justify-between rounded-xl px-4 py-3 text-xs" style="background:rgba(37,99,235,0.12);border:1px solid rgba(37,99,235,0.2);">
                                        <span class="text-blue-300">Qualified lead routing</span>
                                        <span class="w-2 h-2 rounded-full bg-blue-400 animate-pulse"></span>
                                    </div>
                                    <div class="rounded-xl px-4 py-3 text-center text-xs text-white font-bold" style="background:rgba(37,99,235,0.5);">Submit &rarr;</div>
                                </div>
                            @elseif($page['visual'] === 'book')
                                <div class="{{ $page['accentClass'] }} text-[10px] font-black uppercase tracking-[0.2em] mb-5">Booking flow preview</div>
                                <div class="grid grid-cols-3 gap-2 mb-4">
                                    @foreach(['Mon','Tue','Wed','Thu','Fri','Sat'] as $day)
                                        <div class="rounded-xl border px-2 py-3 text-center text-xs text-violet-200" style="border-color:rgba(139,92,246,0.2);background:rgba(139,92,246,0.1);">{{ $day }}</div>
                                    @endforeach
                                </div>
                                <div class="space-y-2">
                                    @foreach(['9:00 AM', '11:30 AM', '2:00 PM'] as $slot)
                                        <div class="flex items-center justify-between rounded-xl bg-white/5 px-4 py-3 text-xs text-gray-300">
                                            <span>{{ $slot }}</span><span class="text-violet-400 font-semibold">Available</span>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($page['visual'] === 'pay')
                                <div class="{{ $page['accentClass'] }} text-[10px] font-black uppercase tracking-[0.2em] mb-5">Payment flow preview</div>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between text-sm text-gray-200 font-semibold px-1">
                                        <span>Invoice #1048</span><span>$1,250.00</span>
                                    </div>
                                    <div class="h-10 rounded-xl bg-white/5"></div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="h-10 rounded-xl bg-white/5"></div>
                                        <div class="h-10 rounded-xl bg-white/5"></div>
                                    </div>
                                    <div class="rounded-xl px-4 py-3 text-center text-xs text-white font-bold animate-pulse" style="background:rgba(16,185,129,0.6);">Pay $1,250.00 &rarr;</div>
                                </div>
                            @elseif($page['visual'] === 'portal')
                                <div class="{{ $page['accentClass'] }} text-[10px] font-black uppercase tracking-[0.2em] mb-5">Client portal preview</div>
                                <div class="space-y-3">
                                    @foreach([['Project status','On track'],['Files &amp; approvals','3 pending'],['Messages','2 unread'],['Billing','Invoice ready']] as [$label,$value])
                                        <div class="flex items-center justify-between rounded-xl bg-white/5 px-4 py-3 text-sm text-gray-200">
                                            <span>{!! $label !!}</span>
                                            <span class="text-orange-400 font-semibold text-xs">{{ $value }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($page['visual'] === 'crm')
                                <div class="{{ $page['accentClass'] }} text-[10px] font-black uppercase tracking-[0.2em] mb-5">CRM pipeline preview</div>
                                <div class="space-y-3">
                                    @foreach([['Alex R.','High intent',95],['Michelle T.','Evaluating',62],['James K.','New lead',38]] as [$name,$stage,$score])
                                        <div class="rounded-xl bg-white/5 px-4 py-3 space-y-1.5">
                                            <div class="flex items-center justify-between text-xs text-gray-200">
                                                <span class="font-semibold">{{ $name }}</span>
                                                <span class="text-indigo-400">{{ $stage }}</span>
                                            </div>
                                            <div class="h-1.5 rounded-full bg-white/10 overflow-hidden">
                                                <div class="h-1.5 rounded-full bg-indigo-500" style="width:{{ $score }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="{{ $page['accentClass'] }} text-[10px] font-black uppercase tracking-[0.2em] mb-5">CMS editor preview</div>
                                <div class="space-y-3">
                                    <div class="h-10 rounded-xl bg-white/5"></div>
                                    <div class="h-24 rounded-xl bg-white/5"></div>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div class="h-14 rounded-xl bg-white/5"></div>
                                        <div class="h-14 rounded-xl bg-white/5"></div>
                                        <div class="h-14 rounded-xl bg-white/5"></div>
                                    </div>
                                    <div class="flex items-center justify-between rounded-xl px-4 py-3 text-xs" style="background:rgba(6,182,212,0.12);border:1px solid rgba(6,182,212,0.2);">
                                        <span class="text-cyan-300">Publish changes</span>
                                        <span class="w-2 h-2 rounded-full bg-cyan-400 animate-pulse"></span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- ── How it works ───────────────────────────────────────────────── --}}
    <section class="px-6 pb-24 bg-white/[0.015]">
        <div class="max-w-5xl mx-auto">
            <div class="text-center mb-14 pt-20">
                <span class="text-gray-500 text-xs font-black uppercase tracking-[0.25em]">How {{ $page['title'] }} works</span>
                <h2 class="text-4xl font-black text-white mt-3 tracking-tight">Three steps. No manual work.</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach($page['steps'] as $step)
                    <div class="relative rounded-2xl p-8 border border-white/5 bg-white/[0.03]">
                        <div class="w-10 h-10 rounded-xl border flex items-center justify-center mb-6 {{ $page['badgeClass'] }}">
                            <span class="text-xs font-black">{{ $step['num'] }}</span>
                        </div>
                        <h3 class="text-lg font-black text-white mb-3 leading-tight">{{ $step['title'] }}</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">{{ $step['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Feature grid ───────────────────────────────────────────────── --}}
    <section class="px-6 py-24">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-14">
                <span class="text-gray-500 text-xs font-black uppercase tracking-[0.25em]">What's included</span>
                <h2 class="text-4xl font-black text-white mt-3 tracking-tight">Everything you need. Nothing you don't.</h2>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @foreach($page['features'] as $feature)
                    <div class="rounded-2xl p-7 border border-white/5 bg-white/[0.03] hover:bg-white/[0.05] transition-colors">
                        <div class="w-8 h-8 rounded-lg {{ $page['accentFaint'] }} border flex items-center justify-center mb-5">
                            <span class="{{ $page['accentClass'] }} text-sm font-black">&#10003;</span>
                        </div>
                        <h3 class="text-base font-black text-white mb-2">{{ $feature['title'] }}</h3>
                        <p class="text-gray-400 text-sm leading-relaxed">{{ $feature['body'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Use cases ───────────────────────────────────────────────────── --}}
    <section class="px-6 pb-24">
        <div class="max-w-6xl mx-auto">
            <div class="text-center mb-14">
                <span class="text-gray-500 text-xs font-black uppercase tracking-[0.25em]">Who it's for</span>
                <h2 class="text-4xl font-black text-white mt-3 tracking-tight">Built for businesses that move fast.</h2>
            </div>
            <div class="grid md:grid-cols-3 gap-6">
                @foreach($page['use_cases'] as $uc)
                    <div class="rounded-2xl p-8 border {{ $page['accentFaint'] }}">
                        <div class="{{ $page['accentClass'] }} text-sm font-black uppercase tracking-wider mb-4">{{ $uc['who'] }}</div>
                        <p class="text-gray-300 text-sm leading-relaxed">{{ $uc['what'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ── Part of smbgen-core strip ───────────────────────────────────── --}}
    <section class="px-6 pb-16">
        <div class="max-w-6xl mx-auto">
            <div class="rounded-2xl border border-white/5 bg-white/[0.03] p-8 md:p-12">
                <div class="text-center mb-10">
                    <span class="text-gray-500 text-xs font-black uppercase tracking-[0.25em]">Part of smbgen-core</span>
                    <h2 class="text-2xl md:text-3xl font-black text-white mt-3">{{ $page['title'] }} fits into the whole.</h2>
                    <p class="text-gray-400 text-base mt-3 max-w-xl mx-auto">Every module shares the same data layer — no double entry, no data silos.</p>
                </div>
                <div class="flex flex-wrap justify-center gap-3">
                    @foreach([
                        ['Contact','contact-core', route('product.contact'),'bg-blue-600/15 border-blue-500/30 text-blue-300'],
                        ['Book','book-core',       route('product.book'),   'bg-violet-600/15 border-violet-500/30 text-violet-300'],
                        ['Pay','pay-core',         route('product.pay'),    'bg-emerald-600/15 border-emerald-500/30 text-emerald-300'],
                        ['Portal','portal-core',   route('product.portal'), 'bg-orange-600/15 border-orange-500/30 text-orange-300'],
                        ['CRM','crm-core',         route('product.crm'),    'bg-indigo-600/15 border-indigo-500/30 text-indigo-300'],
                        ['CMS','cms-core',         route('product.cms'),    'bg-cyan-600/15 border-cyan-500/30 text-cyan-300'],
                    ] as [$label,$id,$href,$cls])
                        <a href="{{ $href }}"
                            class="px-5 py-2.5 rounded-xl border text-sm font-black uppercase tracking-widest transition-all {{ $cls }} {{ $productId === $id ? 'ring-2 ring-white/20 opacity-100' : 'opacity-60 hover:opacity-100' }}">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ── Bottom CTA ──────────────────────────────────────────────────── --}}
    <section class="px-6 py-28">
        <div class="max-w-3xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border {{ $page['accentFaint'] }} text-xs font-bold uppercase tracking-widest mb-8">
                <span class="w-1.5 h-1.5 rounded-full {{ str_replace('text-', 'bg-', $page['accentClass']) }} animate-pulse inline-block"></span>
                <span class="{{ $page['accentClass'] }}">Ready when you are</span>
            </div>
            <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight mb-5 leading-tight">
                Ready to add {{ $page['title'] }}<br>to your stack?
            </h2>
            <p class="text-gray-400 text-lg mb-10 font-light max-w-lg mx-auto leading-relaxed">
                Book a 30-minute discovery call and we'll walk through exactly how it fits your setup — no fluff, no obligation.
            </p>
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{ $bookHref }}" class="bg-white text-gray-900 font-black px-8 py-4 rounded-xl hover:bg-gray-100 transition-colors text-base shadow-xl">
                    Book a call &rarr;
                </a>
                <a href="{{ $primaryUrl }}" class="{{ $page['accentClass'] }} border border-current/30 font-bold px-8 py-4 rounded-xl hover:bg-white/5 transition-colors text-base">
                    {{ $page['primaryLabel'] }}
                </a>
                <a href="{{ route('solutions') }}" class="border border-white/10 text-gray-400 font-semibold px-8 py-4 rounded-xl hover:border-white/20 hover:text-gray-200 transition-colors text-base">
                    Browse all solutions
                </a>
            </div>
        </div>
    </section>

</div>
@endsection
