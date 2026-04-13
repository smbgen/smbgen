@extends('layouts.frontend')

@section('title', 'SMBGen Overview Deck')
@section('description', 'Local-only deep overview deck of the SMBGen platform with links to every major app area and feature set.')

@php
    $sections = [
        [
            'id' => 'platform-core',
            'label' => 'Core Product',
            'title' => 'SMBGen Core Operating Loop',
            'summary' => 'Lead capture to revenue to retention in one system layer: Contact, Book, Pay, Portal, CRM, and CMS.',
            'theme' => 'blue',
            'items' => [
                ['name' => 'Contact Intake', 'description' => 'Structured lead intake with qualification and routing.', 'route' => 'product.contact'],
                ['name' => 'Booking Engine', 'description' => 'Wizard flow, availability, confirmations, and reminders.', 'route' => 'product.book'],
                ['name' => 'Payment Flow', 'description' => 'Payment collection and Stripe-linked checkout flow.', 'route' => 'product.pay'],
                ['name' => 'Client Portal', 'description' => 'Authenticated workspace for files, messages, and status.', 'route' => 'product.portal'],
                ['name' => 'CRM Layer', 'description' => 'Client records, follow-up context, and conversion visibility.', 'route' => 'product.crm'],
                ['name' => 'CMS Layer', 'description' => 'Page and content operations for non-technical teams.', 'route' => 'product.cms'],
            ],
        ],
        [
            'id' => 'admin-ops',
            'label' => 'Admin Ops',
            'title' => 'Admin Control Surface',
            'summary' => 'End-to-end business operation controls: dashboards, clients, bookings, billing, content, users, and diagnostics.',
            'theme' => 'violet',
            'items' => [
                ['name' => 'Admin Dashboard', 'description' => 'Operational command center for the tenant.', 'route' => 'admin.dashboard'],
                ['name' => 'Client Management', 'description' => 'CRUD, imports, exports, Google link, and account provisioning.', 'route' => 'clients.index'],
                ['name' => 'Booking Management', 'description' => 'Booking dashboard, reminder workflows, and conversion to client.', 'route' => 'admin.bookings.index'],
                ['name' => 'Billing Back Office', 'description' => 'Invoice actions, Stripe links, refunds, and sync actions.', 'route' => 'admin.billing.index'],
                ['name' => 'Business Settings', 'description' => 'Tenant-facing settings and setup wizard controls.', 'route' => 'admin.business_settings.index'],
                ['name' => 'Search + Activity Logs', 'description' => 'Cross-entity search with operational activity traceability.', 'route' => 'admin.search'],
            ],
        ],
        [
            'id' => 'content-publishing',
            'label' => 'Content Engine',
            'title' => 'CMS + Blog Publishing Stack',
            'summary' => 'Production-ready content stack with pages, media, forms, blog taxonomy, and import tooling.',
            'theme' => 'cyan',
            'items' => [
                ['name' => 'CMS Page Manager', 'description' => 'Page lifecycle, duplication, metadata, and publishing.', 'route' => 'admin.cms.index'],
                ['name' => 'CMS Image Library', 'description' => 'Media uploads, management API, and cleanup operations.', 'route' => 'admin.cms.images.index'],
                ['name' => 'CMS Public Render', 'description' => 'Public-facing CMS page rendering pipeline.', 'route' => 'home'],
                ['name' => 'Blog Publishing', 'description' => 'Post workflow with categories and tags.', 'route' => 'admin.blog.posts.index'],
                ['name' => 'WordPress Import', 'description' => 'Import bridge for migration from legacy blog systems.', 'route' => 'admin.blog.import.index'],
                ['name' => 'Public Blog', 'description' => 'Feed, search, taxonomy pages, and post pages.', 'route' => 'blog.index'],
            ],
        ],
        [
            'id' => 'client-experience',
            'label' => 'Client Experience',
            'title' => 'Customer-Facing Experience Layer',
            'summary' => 'Everything your clients touch: portal, secure files, messaging, booking, and service communications.',
            'theme' => 'emerald',
            'items' => [
                ['name' => 'Portal Dashboard', 'description' => 'Client home for account navigation and delivery visibility.', 'route' => 'dashboard'],
                ['name' => 'Messages', 'description' => 'Threaded client/team communication and reply flows.', 'route' => 'messages.index'],
                ['name' => 'Billing View', 'description' => 'Client-facing billing and invoice payment entry points.', 'route' => 'billing.index'],
                ['name' => 'Booking Wizard', 'description' => 'Public booking funnel with availability logic.', 'route' => 'booking.wizard'],
                ['name' => 'Contact Funnel', 'description' => 'Public contact intake and lead capture route.', 'route' => 'contact'],
                ['name' => 'Legal Surface', 'description' => 'EULA and privacy compliance entry points.', 'route' => 'legal.privacy'],
            ],
        ],
        [
            'id' => 'ai-automation',
            'label' => 'AI + Automation',
            'title' => 'AI-Assisted Content and Ops',
            'summary' => 'Built-in AI tools for content generation, SEO assist, and intelligent workflows across client and admin surfaces.',
            'theme' => 'amber',
            'items' => [
                ['name' => 'AI Settings', 'description' => 'Admin model and provider controls.', 'route' => 'admin.ai.settings.index'],
                ['name' => 'AI Generate Endpoint', 'description' => 'Content generation flow used by admin tooling.', 'route' => 'admin.ai.stats'],
                ['name' => 'SEO Assistant', 'description' => 'Client-facing SEO query assistant workflow.', 'route' => 'client.seo-assistant'],
                ['name' => 'Email Tracking', 'description' => 'Open and click telemetry for outbound communication.', 'route' => 'home'],
                ['name' => 'Lead Qualification Inputs', 'description' => 'Form and booking data shape for smarter triage.', 'route' => 'admin.leads.index'],
                ['name' => 'Package Review Workflow', 'description' => 'AI-friendly package review and status controls.', 'route' => 'admin.packages.index'],
            ],
        ],
        [
            'id' => 'integrations',
            'label' => 'Integrations',
            'title' => 'Integrations and Connected Systems',
            'summary' => 'Google and Stripe integrations with production routes for auth, sync, billing, and webhook operations.',
            'theme' => 'indigo',
            'items' => [
                ['name' => 'Google OAuth', 'description' => 'User authentication via Google sign-in.', 'route' => 'auth.google.redirect'],
                ['name' => 'Google Calendar Admin', 'description' => 'Calendar connect, callback, and sync settings.', 'route' => 'admin.calendar.index'],
                ['name' => 'Google Workspace Page', 'description' => 'Public integration overview and positioning.', 'route' => 'google.workspace'],
                ['name' => 'Stripe Payment Surface', 'description' => 'Hosted payment collection route.', 'route' => 'payment.collect'],
                ['name' => 'Stripe Webhook Endpoint', 'description' => 'Server-side billing event ingestion route.', 'route' => 'payment.collect'],
                ['name' => 'Asset Delivery', 'description' => 'Protected asset serving with cloud/local fallback.', 'route' => 'assets.public', 'params' => ['path' => 'cms/images']],
            ],
        ],
        [
            'id' => 'multitenancy-governance',
            'label' => 'Multi-Tenancy + Governance',
            'title' => 'Tenant Isolation and Platform Governance',
            'summary' => 'Super-admin control layer for tenant lifecycle, impersonation, setup workflows, and environment controls.',
            'theme' => 'rose',
            'items' => [
                ['name' => 'Super Admin Dashboard', 'description' => 'Global visibility and tenant-level governance.', 'route' => 'super-admin.dashboard'],
                ['name' => 'Tenant Management', 'description' => 'Create, configure, and operate tenant workspaces.', 'route' => 'super-admin.tenants.index'],
                ['name' => 'Deployment Console', 'description' => 'Operational deployment utility surface.', 'route' => 'super-admin.deployment-console'],
                ['name' => 'Diagnostics', 'description' => 'System diagnostics and setup health checks.', 'route' => 'super-admin.diagnostics'],
                ['name' => 'Guided Setup', 'description' => 'Tenant onboarding flow and setup requirements.', 'route' => 'super-admin.guided-setup'],
                ['name' => 'Impersonation Control', 'description' => 'Super-admin to tenant-admin impersonation lifecycle.', 'route' => 'super-admin.tenants.index'],
            ],
        ],
    ];

    $themeMap = [
        'blue' => ['line' => 'bg-blue-500/40', 'label' => 'text-blue-300', 'card' => 'border-blue-500/20 bg-blue-950/20', 'badge' => 'text-blue-300 border-blue-400/30 bg-blue-500/10'],
        'violet' => ['line' => 'bg-violet-500/40', 'label' => 'text-violet-300', 'card' => 'border-violet-500/20 bg-violet-950/20', 'badge' => 'text-violet-300 border-violet-400/30 bg-violet-500/10'],
        'cyan' => ['line' => 'bg-cyan-500/40', 'label' => 'text-cyan-300', 'card' => 'border-cyan-500/20 bg-cyan-950/20', 'badge' => 'text-cyan-300 border-cyan-400/30 bg-cyan-500/10'],
        'emerald' => ['line' => 'bg-emerald-500/40', 'label' => 'text-emerald-300', 'card' => 'border-emerald-500/20 bg-emerald-950/20', 'badge' => 'text-emerald-300 border-emerald-400/30 bg-emerald-500/10'],
        'amber' => ['line' => 'bg-amber-500/40', 'label' => 'text-amber-300', 'card' => 'border-amber-500/20 bg-amber-950/20', 'badge' => 'text-amber-300 border-amber-400/30 bg-amber-500/10'],
        'indigo' => ['line' => 'bg-indigo-500/40', 'label' => 'text-indigo-300', 'card' => 'border-indigo-500/20 bg-indigo-950/20', 'badge' => 'text-indigo-300 border-indigo-400/30 bg-indigo-500/10'],
        'rose' => ['line' => 'bg-rose-500/40', 'label' => 'text-rose-300', 'card' => 'border-rose-500/20 bg-rose-950/20', 'badge' => 'text-rose-300 border-rose-400/30 bg-rose-500/10'],
    ];
@endphp

@push('head')
<style>
    .deck-root {
        background:
            radial-gradient(1200px 700px at 0% 0%, rgba(59, 130, 246, 0.14) 0%, transparent 55%),
            radial-gradient(1200px 700px at 100% 100%, rgba(236, 72, 153, 0.1) 0%, transparent 55%),
            #02030a;
    }

    .deck-card {
        transition: transform .22s ease, border-color .22s ease, box-shadow .22s ease;
    }

    .deck-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 24px 60px rgba(0, 0, 0, .24);
    }

    .grid-dense {
        grid-auto-rows: 1fr;
    }

    .glass-strip {
        backdrop-filter: blur(10px);
        background: rgba(2, 6, 23, 0.58);
    }
</style>
@endpush

@section('content')
<section class="deck-root text-white min-h-screen">
    <div class="max-w-7xl mx-auto px-6 pt-24 pb-12">
        <div class="glass-strip rounded-2xl border border-white/10 px-6 py-5 mb-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-[11px] uppercase tracking-[0.22em] text-blue-300 font-bold">Local-only walkthrough deck</p>
                    <h1 class="text-4xl md:text-5xl font-black tracking-tight mt-2">SMBGen Platform Overview</h1>
                    <p class="text-slate-300 mt-3 max-w-3xl">A recordable, deep-link-first overview to walk through the complete product surface from top-level narrative to feature-level screens.</p>
                </div>
                <div class="flex gap-2 flex-wrap md:justify-end">
                    <a href="#jump" class="px-4 py-2 rounded-lg border border-white/20 text-sm font-semibold text-slate-200 hover:bg-white/10 transition-colors">Jump Index</a>
                    <a href="{{ route('home') }}" class="px-4 py-2 rounded-lg bg-blue-600 hover:bg-blue-500 text-sm font-semibold transition-colors">Back to Frontend</a>
                </div>
            </div>
        </div>

        <div id="jump" class="mb-14">
            <p class="text-[11px] uppercase tracking-[0.2em] text-slate-400 font-bold mb-4">Deck Index</p>
            <div class="flex flex-wrap gap-2.5">
                @foreach($sections as $section)
                    @php $colors = $themeMap[$section['theme']]; @endphp
                    <a href="#{{ $section['id'] }}" class="px-3.5 py-1.5 rounded-full border text-xs font-bold tracking-wide {{ $colors['badge'] }} hover:bg-white/10 transition-colors">{{ $section['label'] }}</a>
                @endforeach
            </div>
        </div>

        @foreach($sections as $section)
            @php $colors = $themeMap[$section['theme']]; @endphp
            <section id="{{ $section['id'] }}" class="scroll-mt-24 mb-16 md:mb-20">
                <div class="flex items-center gap-3 mb-3">
                    <span class="h-px w-12 {{ $colors['line'] }}"></span>
                    <p class="text-[11px] uppercase tracking-[0.2em] font-bold {{ $colors['label'] }}">{{ $section['label'] }}</p>
                </div>
                <h2 class="text-3xl md:text-4xl font-black tracking-tight">{{ $section['title'] }}</h2>
                <p class="text-slate-300 mt-3 max-w-4xl leading-relaxed">{{ $section['summary'] }}</p>

                <div class="mt-7 grid sm:grid-cols-2 lg:grid-cols-3 gap-4 grid-dense">
                    @foreach($section['items'] as $item)
                        @php
                            $href = null;
                            if (isset($item['route']) && Route::has($item['route'])) {
                                $href = route($item['route'], $item['params'] ?? []);
                            }
                        @endphp
                        <article class="deck-card rounded-2xl border p-5 {{ $colors['card'] }}">
                            <div class="flex items-start justify-between gap-3">
                                <h3 class="text-lg font-bold leading-tight">{{ $item['name'] }}</h3>
                                <span class="text-[10px] uppercase tracking-wider px-2 py-1 rounded-md border {{ $colors['badge'] }}">Feature</span>
                            </div>
                            <p class="mt-3 text-sm text-slate-300 leading-relaxed">{{ $item['description'] }}</p>
                            @if($href)
                                <a href="{{ $href }}" class="inline-flex items-center gap-2 mt-4 text-sm font-semibold {{ $colors['label'] }} hover:text-white transition-colors">
                                    Open this area
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                    </svg>
                                </a>
                            @else
                                <p class="mt-4 text-xs text-slate-500">Route not available in this environment.</p>
                            @endif
                        </article>
                    @endforeach
                </div>
            </section>
        @endforeach

        <section class="pb-16">
            <div class="rounded-2xl border border-white/10 bg-slate-900/60 p-6 md:p-8">
                <p class="text-[11px] uppercase tracking-[0.2em] text-slate-400 font-bold">Suggested Screenshare Flow</p>
                <ol class="mt-4 grid md:grid-cols-2 gap-3 text-sm text-slate-300">
                    <li class="rounded-xl border border-white/10 bg-slate-950/40 p-4"><strong class="text-white">1. Positioning:</strong> Start at Core Product, explain the contact-to-cash loop.</li>
                    <li class="rounded-xl border border-white/10 bg-slate-950/40 p-4"><strong class="text-white">2. Admin Depth:</strong> Walk dashboard, clients, bookings, billing, and setup wizard.</li>
                    <li class="rounded-xl border border-white/10 bg-slate-950/40 p-4"><strong class="text-white">3. Publishing Stack:</strong> Show CMS pages, image library, blog workflow, and public output.</li>
                    <li class="rounded-xl border border-white/10 bg-slate-950/40 p-4"><strong class="text-white">4. Client Experience:</strong> Show portal, messages, files, billing, and booking handoff.</li>
                    <li class="rounded-xl border border-white/10 bg-slate-950/40 p-4"><strong class="text-white">5. AI + Integrations:</strong> Cover AI settings, SEO assistant, Google, and Stripe.</li>
                    <li class="rounded-xl border border-white/10 bg-slate-950/40 p-4"><strong class="text-white">6. Governance:</strong> Finish with super-admin, tenants, diagnostics, and deployment console.</li>
                </ol>
            </div>
        </section>
    </div>
</section>
@endsection
