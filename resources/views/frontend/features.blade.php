@extends('layouts.frontend')

@section('title', 'Features — smbgen')
@section('description', 'Every feature in the smbgen platform. Contact, booking, payments, client portal, CRM, CMS, blog, messaging, file management, AI content generation, Google Workspace, Stripe, and multi-tenancy — all in one place.')

@push('head')
<style>
    .features-bg {
        background:
            radial-gradient(ellipse at 15% 0%, rgba(59, 130, 246, 0.09) 0%, transparent 50%),
            radial-gradient(ellipse at 85% 100%, rgba(139, 92, 246, 0.07) 0%, transparent 50%),
            #03040d;
    }

    .feature-card {
        transition: transform 0.18s ease, border-color 0.18s ease;
    }

    .feature-card:hover {
        transform: translateY(-2px);
    }

    .section-fade {
        background: linear-gradient(to bottom, transparent, rgba(255,255,255,0.025) 40%, rgba(255,255,255,0.025) 60%, transparent);
    }
</style>
@endpush

@section('content')

{{-- ── Hero ─────────────────────────────────────────────────────────── --}}
<section class="features-bg pt-28 pb-16 px-6">
    <div class="max-w-5xl mx-auto text-center">

        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full border border-white/10 bg-white/5 text-gray-400 text-xs font-bold uppercase tracking-widest mb-10">
            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse inline-block"></span>
            Platform Features
        </div>

        <h1 class="text-5xl md:text-6xl font-black text-white leading-[1.08] tracking-tight mb-6">
            Everything in smbgen,<br>
            <span class="text-blue-400">in one place.</span>
        </h1>

        <p class="text-gray-400 text-xl max-w-2xl mx-auto font-light leading-relaxed mb-12">
            A complete operating platform for small &amp; mid-market businesses. Every module, integration, and capability — no bolt-ons.
        </p>

        {{-- Quick-jump category pills --}}
        <div class="flex flex-wrap justify-center gap-2.5">
            <a href="#core" class="px-3.5 py-1.5 rounded-full border border-blue-600/30 bg-blue-600/10 text-blue-400 text-xs font-bold uppercase tracking-widest hover:bg-blue-600/20 transition-colors">Core</a>
            <a href="#content" class="px-3.5 py-1.5 rounded-full border border-violet-600/30 bg-violet-600/10 text-violet-400 text-xs font-bold uppercase tracking-widest hover:bg-violet-600/20 transition-colors">Content</a>
            <a href="#operations" class="px-3.5 py-1.5 rounded-full border border-emerald-600/30 bg-emerald-600/10 text-emerald-400 text-xs font-bold uppercase tracking-widest hover:bg-emerald-600/20 transition-colors">Operations</a>
            <a href="#ai" class="px-3.5 py-1.5 rounded-full border border-amber-600/30 bg-amber-600/10 text-amber-400 text-xs font-bold uppercase tracking-widest hover:bg-amber-600/20 transition-colors">AI</a>
            <a href="#integrations" class="px-3.5 py-1.5 rounded-full border border-indigo-600/30 bg-indigo-600/10 text-indigo-400 text-xs font-bold uppercase tracking-widest hover:bg-indigo-600/20 transition-colors">Integrations</a>
            <a href="#platform" class="px-3.5 py-1.5 rounded-full border border-cyan-600/30 bg-cyan-600/10 text-cyan-400 text-xs font-bold uppercase tracking-widest hover:bg-cyan-600/20 transition-colors">Platform</a>
        </div>
    </div>
</section>

{{-- ── Core (smbgen-core) ───────────────────────────────────────────── --}}
<section id="core" class="px-6 py-20 bg-[#03040d]">
    <div class="max-w-6xl mx-auto">

        <div class="mb-10">
            <div class="flex items-center gap-3 mb-2">
                <span class="h-px flex-1 max-w-8 bg-blue-600/40"></span>
                <span class="text-blue-400 text-xs font-black uppercase tracking-widest">smbgen-core</span>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">The core product</h2>
            <p class="text-gray-500 mt-2 text-base max-w-xl">Six connected parts of the same system. Contact through close — without stitching together separate tools.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">

            {{-- Contact --}}
            <a href="{{ route('product.contact') }}" class="feature-card block rounded-2xl p-6 border border-blue-600/20 bg-gradient-to-br from-blue-950/40 to-transparent hover:border-blue-600/40">
                <div class="w-10 h-10 rounded-xl bg-blue-600/20 border border-blue-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-1.5">Contact</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Structured lead intake that qualifies, routes, and hands off cleanly — not just a name and email form.</p>
                <div class="mt-4 flex flex-wrap gap-1.5">
                    <span class="text-xs text-blue-400/80 bg-blue-600/10 border border-blue-600/20 px-2 py-0.5 rounded-md">Lead capture</span>
                    <span class="text-xs text-blue-400/80 bg-blue-600/10 border border-blue-600/20 px-2 py-0.5 rounded-md">Qualification</span>
                    <span class="text-xs text-blue-400/80 bg-blue-600/10 border border-blue-600/20 px-2 py-0.5 rounded-md">Smart routing</span>
                </div>
            </a>

            {{-- Booking --}}
            <a href="{{ route('product.book') }}" class="feature-card block rounded-2xl p-6 border border-violet-600/20 bg-gradient-to-br from-violet-950/40 to-transparent hover:border-violet-600/40">
                <div class="w-10 h-10 rounded-xl bg-violet-600/20 border border-violet-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-1.5">Booking</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Availability, scheduling, confirmations, and reminders in a single flow customers actually complete.</p>
                <div class="mt-4 flex flex-wrap gap-1.5">
                    <span class="text-xs text-violet-400/80 bg-violet-600/10 border border-violet-600/20 px-2 py-0.5 rounded-md">Availability</span>
                    <span class="text-xs text-violet-400/80 bg-violet-600/10 border border-violet-600/20 px-2 py-0.5 rounded-md">Wizard flow</span>
                    <span class="text-xs text-violet-400/80 bg-violet-600/10 border border-violet-600/20 px-2 py-0.5 rounded-md">Reminders</span>
                    <span class="text-xs text-violet-400/80 bg-violet-600/10 border border-violet-600/20 px-2 py-0.5 rounded-md">Custom fields</span>
                </div>
            </a>

            {{-- Payments --}}
            <a href="{{ route('product.pay') }}" class="feature-card block rounded-2xl p-6 border border-emerald-600/20 bg-gradient-to-br from-emerald-950/40 to-transparent hover:border-emerald-600/40">
                <div class="w-10 h-10 rounded-xl bg-emerald-600/20 border border-emerald-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-1.5">Payments</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Stripe-powered payment collection tied to the customer workflow — from invoice or request to confirmed receipt.</p>
                <div class="mt-4 flex flex-wrap gap-1.5">
                    <span class="text-xs text-emerald-400/80 bg-emerald-600/10 border border-emerald-600/20 px-2 py-0.5 rounded-md">Stripe</span>
                    <span class="text-xs text-emerald-400/80 bg-emerald-600/10 border border-emerald-600/20 px-2 py-0.5 rounded-md">Invoicing</span>
                    <span class="text-xs text-emerald-400/80 bg-emerald-600/10 border border-emerald-600/20 px-2 py-0.5 rounded-md">Webhooks</span>
                </div>
            </a>

            {{-- Client Portal --}}
            <a href="{{ route('product.portal') }}" class="feature-card block rounded-2xl p-6 border border-orange-600/20 bg-gradient-to-br from-orange-950/40 to-transparent hover:border-orange-600/40">
                <div class="w-10 h-10 rounded-xl bg-orange-600/20 border border-orange-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-1.5">Client Portal</h3>
                <p class="text-gray-500 text-sm leading-relaxed">One login for clients to review files, check progress, manage billing, and stay aligned — less manual status email.</p>
                <div class="mt-4 flex flex-wrap gap-1.5">
                    <span class="text-xs text-orange-400/80 bg-orange-600/10 border border-orange-600/20 px-2 py-0.5 rounded-md">Secure login</span>
                    <span class="text-xs text-orange-400/80 bg-orange-600/10 border border-orange-600/20 px-2 py-0.5 rounded-md">File access</span>
                    <span class="text-xs text-orange-400/80 bg-orange-600/10 border border-orange-600/20 px-2 py-0.5 rounded-md">Billing</span>
                </div>
            </a>

            {{-- CRM --}}
            <a href="{{ route('product.crm') }}" class="feature-card block rounded-2xl p-6 border border-indigo-600/20 bg-gradient-to-br from-indigo-950/40 to-transparent hover:border-indigo-600/40">
                <div class="w-10 h-10 rounded-xl bg-indigo-600/20 border border-indigo-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-1.5">CRM</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Contact records, notes, follow-ups, and deal visibility connected to every contact, booking, portal, and payment event.</p>
                <div class="mt-4 flex flex-wrap gap-1.5">
                    <span class="text-xs text-indigo-400/80 bg-indigo-600/10 border border-indigo-600/20 px-2 py-0.5 rounded-md">Contact history</span>
                    <span class="text-xs text-indigo-400/80 bg-indigo-600/10 border border-indigo-600/20 px-2 py-0.5 rounded-md">Follow-ups</span>
                    <span class="text-xs text-indigo-400/80 bg-indigo-600/10 border border-indigo-600/20 px-2 py-0.5 rounded-md">Deal pipeline</span>
                </div>
            </a>

            {{-- CMS --}}
            <a href="{{ route('product.cms') }}" class="feature-card block rounded-2xl p-6 border border-cyan-600/20 bg-gradient-to-br from-cyan-950/40 to-transparent hover:border-cyan-600/40">
                <div class="w-10 h-10 rounded-xl bg-cyan-600/20 border border-cyan-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-1.5">CMS</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Page creation, content blocks, media library, and form builder — publish and update without waiting on developers.</p>
                <div class="mt-4 flex flex-wrap gap-1.5">
                    <span class="text-xs text-cyan-400/80 bg-cyan-600/10 border border-cyan-600/20 px-2 py-0.5 rounded-md">Page builder</span>
                    <span class="text-xs text-cyan-400/80 bg-cyan-600/10 border border-cyan-600/20 px-2 py-0.5 rounded-md">Form builder</span>
                    <span class="text-xs text-cyan-400/80 bg-cyan-600/10 border border-cyan-600/20 px-2 py-0.5 rounded-md">Media library</span>
                    <span class="text-xs text-cyan-400/80 bg-cyan-600/10 border border-cyan-600/20 px-2 py-0.5 rounded-md">Themes</span>
                </div>
            </a>

        </div>
    </div>
</section>

{{-- ── Content & Publishing ─────────────────────────────────────────── --}}
<section id="content" class="px-6 py-20 section-fade">
    <div class="max-w-6xl mx-auto">

        <div class="mb-10">
            <div class="flex items-center gap-3 mb-2">
                <span class="h-px flex-1 max-w-8 bg-violet-600/40"></span>
                <span class="text-violet-400 text-xs font-black uppercase tracking-widest">Content & Publishing</span>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">Publish anything, fast.</h2>
            <p class="text-gray-500 mt-2 text-base max-w-xl">Blog, pages, forms, and media — all managed from the same admin without touching code.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">

            <div class="feature-card rounded-2xl p-6 border border-violet-600/20 bg-gradient-to-br from-violet-950/30 to-transparent hover:border-violet-600/40">
                <div class="w-10 h-10 rounded-xl bg-violet-600/20 border border-violet-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-1.5">Blog</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Flexible content blocks, category management, featured images, and one-click WordPress import for existing content.</p>
                <div class="mt-4 flex flex-wrap gap-1.5">
                    <span class="text-xs text-violet-400/80 bg-violet-600/10 border border-violet-600/20 px-2 py-0.5 rounded-md">Rich blocks</span>
                    <span class="text-xs text-violet-400/80 bg-violet-600/10 border border-violet-600/20 px-2 py-0.5 rounded-md">Categories</span>
                    <span class="text-xs text-violet-400/80 bg-violet-600/10 border border-violet-600/20 px-2 py-0.5 rounded-md">WP import</span>
                </div>
            </div>

            <div class="feature-card rounded-2xl p-6 border border-violet-600/20 bg-gradient-to-br from-violet-950/30 to-transparent hover:border-violet-600/40">
                <div class="w-10 h-10 rounded-xl bg-violet-600/20 border border-violet-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-1.5">Page Builder</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Visual content block system for building and updating pages. Custom slugs, SEO meta, and live preview without dev involvement.</p>
                <div class="mt-4 flex flex-wrap gap-1.5">
                    <span class="text-xs text-violet-400/80 bg-violet-600/10 border border-violet-600/20 px-2 py-0.5 rounded-md">Custom slugs</span>
                    <span class="text-xs text-violet-400/80 bg-violet-600/10 border border-violet-600/20 px-2 py-0.5 rounded-md">SEO meta</span>
                    <span class="text-xs text-violet-400/80 bg-violet-600/10 border border-violet-600/20 px-2 py-0.5 rounded-md">Live preview</span>
                </div>
            </div>

            <div class="feature-card rounded-2xl p-6 border border-violet-600/20 bg-gradient-to-br from-violet-950/30 to-transparent hover:border-violet-600/40">
                <div class="w-10 h-10 rounded-xl bg-violet-600/20 border border-violet-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-violet-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-1.5">Form Builder</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Drag-and-drop forms embeddable on any CMS page. Responses captured, routed, and tracked — no third-party form tool needed.</p>
                <div class="mt-4 flex flex-wrap gap-1.5">
                    <span class="text-xs text-violet-400/80 bg-violet-600/10 border border-violet-600/20 px-2 py-0.5 rounded-md">Drag & drop</span>
                    <span class="text-xs text-violet-400/80 bg-violet-600/10 border border-violet-600/20 px-2 py-0.5 rounded-md">Lead capture</span>
                    <span class="text-xs text-violet-400/80 bg-violet-600/10 border border-violet-600/20 px-2 py-0.5 rounded-md">Throttled</span>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ── Operations ───────────────────────────────────────────────────── --}}
<section id="operations" class="px-6 py-20 bg-[#03040d]">
    <div class="max-w-6xl mx-auto">

        <div class="mb-10">
            <div class="flex items-center gap-3 mb-2">
                <span class="h-px flex-1 max-w-8 bg-emerald-600/40"></span>
                <span class="text-emerald-400 text-xs font-black uppercase tracking-widest">Operations</span>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">Run the day-to-day.</h2>
            <p class="text-gray-500 mt-2 text-base max-w-xl">The tools your team and clients use every day — messaging, files, scheduling, billing — all inside the same system.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <div class="feature-card rounded-2xl p-6 border border-emerald-600/20 bg-gradient-to-br from-emerald-950/30 to-transparent hover:border-emerald-600/40">
                <div class="w-10 h-10 rounded-xl bg-emerald-600/20 border border-emerald-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-base mb-1.5">Messaging</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Threaded messages between clients and staff — all in the portal, none in personal inboxes.</p>
            </div>

            <div class="feature-card rounded-2xl p-6 border border-emerald-600/20 bg-gradient-to-br from-emerald-950/30 to-transparent hover:border-emerald-600/40">
                <div class="w-10 h-10 rounded-xl bg-emerald-600/20 border border-emerald-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 19a2 2 0 01-2-2V7a2 2 0 012-2h4l2 2h4a2 2 0 012 2v1M5 19h14a2 2 0 002-2v-5a2 2 0 00-2-2H9a2 2 0 00-2 2v5a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-base mb-1.5">File Management</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Secure document storage for client files. AWS S3 or local disk — with time-limited signed URLs for private access.</p>
            </div>

            <div class="feature-card rounded-2xl p-6 border border-emerald-600/20 bg-gradient-to-br from-emerald-950/30 to-transparent hover:border-emerald-600/40">
                <div class="w-10 h-10 rounded-xl bg-emerald-600/20 border border-emerald-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-base mb-1.5">Transactional Email</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Booking confirmations, password resets, and notification emails with open &amp; click tracking built in.</p>
            </div>

            <div class="feature-card rounded-2xl p-6 border border-emerald-600/20 bg-gradient-to-br from-emerald-950/30 to-transparent hover:border-emerald-600/40">
                <div class="w-10 h-10 rounded-xl bg-emerald-600/20 border border-emerald-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-base mb-1.5">Billing & Subscriptions</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Stripe subscription plans, usage tiers, invoices, and webhook-driven lifecycle management.</p>
            </div>

        </div>
    </div>
</section>

{{-- ── AI ────────────────────────────────────────────────────────────── --}}
<section id="ai" class="px-6 py-20 section-fade">
    <div class="max-w-6xl mx-auto">

        <div class="mb-10">
            <div class="flex items-center gap-3 mb-2">
                <span class="h-px flex-1 max-w-8 bg-amber-600/40"></span>
                <span class="text-amber-400 text-xs font-black uppercase tracking-widest">AI & Automation</span>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">AI where it saves time.</h2>
            <p class="text-gray-500 mt-2 text-base max-w-xl">Claude-powered generation built into the content and operations workflow — not bolted on as a chatbot widget.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">

            <div class="feature-card rounded-2xl p-6 border border-amber-600/20 bg-gradient-to-br from-amber-950/30 to-transparent hover:border-amber-600/40">
                <div class="w-10 h-10 rounded-xl bg-amber-600/20 border border-amber-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-1.5">AI Content Generation</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Anthropic Claude integration for drafting blog posts, CMS page copy, and business descriptions directly inside the admin.</p>
                <div class="mt-4 flex flex-wrap gap-1.5">
                    <span class="text-xs text-amber-400/80 bg-amber-600/10 border border-amber-600/20 px-2 py-0.5 rounded-md">Claude API</span>
                    <span class="text-xs text-amber-400/80 bg-amber-600/10 border border-amber-600/20 px-2 py-0.5 rounded-md">Blog drafts</span>
                    <span class="text-xs text-amber-400/80 bg-amber-600/10 border border-amber-600/20 px-2 py-0.5 rounded-md">Page copy</span>
                </div>
            </div>

            <div class="feature-card rounded-2xl p-6 border border-amber-600/20 bg-gradient-to-br from-amber-950/30 to-transparent hover:border-amber-600/40">
                <div class="w-10 h-10 rounded-xl bg-amber-600/20 border border-amber-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-1.5">Smart Lead Qualification</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Structured intake forms with routing logic that qualifies leads before they reach your team — filtering noise before a human touches it.</p>
                <div class="mt-4 flex flex-wrap gap-1.5">
                    <span class="text-xs text-amber-400/80 bg-amber-600/10 border border-amber-600/20 px-2 py-0.5 rounded-md">Conditional logic</span>
                    <span class="text-xs text-amber-400/80 bg-amber-600/10 border border-amber-600/20 px-2 py-0.5 rounded-md">Auto-routing</span>
                </div>
            </div>

            <div class="feature-card rounded-2xl p-6 border border-amber-600/20 bg-gradient-to-br from-amber-950/30 to-transparent hover:border-amber-600/40">
                <div class="w-10 h-10 rounded-xl bg-amber-600/20 border border-amber-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-amber-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-1.5">Email Analytics</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Open and click tracking on outbound emails — know which messages land and which ones don't without a marketing tool subscription.</p>
                <div class="mt-4 flex flex-wrap gap-1.5">
                    <span class="text-xs text-amber-400/80 bg-amber-600/10 border border-amber-600/20 px-2 py-0.5 rounded-md">Open tracking</span>
                    <span class="text-xs text-amber-400/80 bg-amber-600/10 border border-amber-600/20 px-2 py-0.5 rounded-md">Click tracking</span>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ── Integrations ─────────────────────────────────────────────────── --}}
<section id="integrations" class="px-6 py-20 bg-[#03040d]">
    <div class="max-w-6xl mx-auto">

        <div class="mb-10">
            <div class="flex items-center gap-3 mb-2">
                <span class="h-px flex-1 max-w-8 bg-indigo-600/40"></span>
                <span class="text-indigo-400 text-xs font-black uppercase tracking-widest">Integrations</span>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">Connect the tools you already use.</h2>
            <p class="text-gray-500 mt-2 text-base max-w-xl">First-party integrations with the platforms that matter most to small and mid-market businesses.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">

            <a href="{{ route('google.workspace') }}" class="feature-card block rounded-2xl p-6 border border-indigo-600/20 bg-gradient-to-br from-indigo-950/30 to-transparent hover:border-indigo-600/40">
                <div class="w-10 h-10 rounded-xl bg-indigo-600/20 border border-indigo-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-1.5">Google Calendar</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Two-way sync between the booking system and Google Calendar. Availability reflects real calendar conflicts, not just system blocks.</p>
                <div class="mt-4 flex flex-wrap gap-1.5">
                    <span class="text-xs text-indigo-400/80 bg-indigo-600/10 border border-indigo-600/20 px-2 py-0.5 rounded-md">Two-way sync</span>
                    <span class="text-xs text-indigo-400/80 bg-indigo-600/10 border border-indigo-600/20 px-2 py-0.5 rounded-md">Conflict detection</span>
                </div>
            </a>

            <div class="feature-card rounded-2xl p-6 border border-indigo-600/20 bg-gradient-to-br from-indigo-950/30 to-transparent hover:border-indigo-600/40">
                <div class="w-10 h-10 rounded-xl bg-indigo-600/20 border border-indigo-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-1.5">Google OAuth</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Sign in with Google for clients and staff. Verified email, faster onboarding, and no password to manage.</p>
                <div class="mt-4 flex flex-wrap gap-1.5">
                    <span class="text-xs text-indigo-400/80 bg-indigo-600/10 border border-indigo-600/20 px-2 py-0.5 rounded-md">One-click login</span>
                    <span class="text-xs text-indigo-400/80 bg-indigo-600/10 border border-indigo-600/20 px-2 py-0.5 rounded-md">Verified email</span>
                </div>
            </div>

            <div class="feature-card rounded-2xl p-6 border border-indigo-600/20 bg-gradient-to-br from-indigo-950/30 to-transparent hover:border-indigo-600/40">
                <div class="w-10 h-10 rounded-xl bg-indigo-600/20 border border-indigo-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-indigo-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-lg mb-1.5">Stripe</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Payment intents, subscription lifecycle, webhook-driven status updates, and card collection — all Stripe native.</p>
                <div class="mt-4 flex flex-wrap gap-1.5">
                    <span class="text-xs text-indigo-400/80 bg-indigo-600/10 border border-indigo-600/20 px-2 py-0.5 rounded-md">Payment intents</span>
                    <span class="text-xs text-indigo-400/80 bg-indigo-600/10 border border-indigo-600/20 px-2 py-0.5 rounded-md">Subscriptions</span>
                    <span class="text-xs text-indigo-400/80 bg-indigo-600/10 border border-indigo-600/20 px-2 py-0.5 rounded-md">Webhooks</span>
                </div>
            </div>

        </div>
    </div>
</section>

{{-- ── Platform ─────────────────────────────────────────────────────── --}}
<section id="platform" class="px-6 py-20 section-fade">
    <div class="max-w-6xl mx-auto">

        <div class="mb-10">
            <div class="flex items-center gap-3 mb-2">
                <span class="h-px flex-1 max-w-8 bg-cyan-600/40"></span>
                <span class="text-cyan-400 text-xs font-black uppercase tracking-widest">Platform</span>
            </div>
            <h2 class="text-3xl font-black text-white tracking-tight">Built to scale with you.</h2>
            <p class="text-gray-500 mt-2 text-base max-w-xl">The infrastructure layer that makes smbgen deployable to many customers, many domains, and many configurations.</p>
        </div>

        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">

            <div class="feature-card rounded-2xl p-6 border border-cyan-600/20 bg-gradient-to-br from-cyan-950/30 to-transparent hover:border-cyan-600/40">
                <div class="w-10 h-10 rounded-xl bg-cyan-600/20 border border-cyan-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-base mb-1.5">Multi-Tenancy</h3>
                <p class="text-gray-500 text-sm leading-relaxed">One codebase, many clients. Isolated tenant data, custom domains, and per-tenant feature configuration.</p>
            </div>

            <div class="feature-card rounded-2xl p-6 border border-cyan-600/20 bg-gradient-to-br from-cyan-950/30 to-transparent hover:border-cyan-600/40">
                <div class="w-10 h-10 rounded-xl bg-cyan-600/20 border border-cyan-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-base mb-1.5">Theme System</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Per-tenant brand colors, fonts, and visual identity injected at the CSS variable level — no hard-coding per client.</p>
            </div>

            <div class="feature-card rounded-2xl p-6 border border-cyan-600/20 bg-gradient-to-br from-cyan-950/30 to-transparent hover:border-cyan-600/40">
                <div class="w-10 h-10 rounded-xl bg-cyan-600/20 border border-cyan-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-base mb-1.5">Feature Flags</h3>
                <p class="text-gray-500 text-sm leading-relaxed">Per-tenant module toggles for booking, billing, messaging, blog, and more — ship features to the right customers at the right time.</p>
            </div>

            <div class="feature-card rounded-2xl p-6 border border-cyan-600/20 bg-gradient-to-br from-cyan-950/30 to-transparent hover:border-cyan-600/40">
                <div class="w-10 h-10 rounded-xl bg-cyan-600/20 border border-cyan-600/30 flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                    </svg>
                </div>
                <h3 class="text-white font-bold text-base mb-1.5">Dark Mode</h3>
                <p class="text-gray-500 text-sm leading-relaxed">System-preference and manual toggle. Every interface layer — admin, portal, frontend — ships with a proper dark implementation.</p>
            </div>

        </div>
    </div>
</section>

{{-- ── CTA ───────────────────────────────────────────────────────────── --}}
<section class="px-6 py-24 bg-[#03040d]">
    <div class="max-w-3xl mx-auto text-center">
        <h2 class="text-4xl font-black text-white tracking-tight mb-5">Ready to see it in action?</h2>
        <p class="text-gray-400 text-lg font-light leading-relaxed mb-10">
            Talk to the team or start with the platform — no setup fee, no long-term commitment.
        </p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="{{ route('solutions') }}" class="px-7 py-3.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-sm tracking-wide transition-colors">
                Explore smbgen-core
            </a>
            <a href="{{ route('contact') }}" class="px-7 py-3.5 rounded-xl border border-white/15 bg-white/5 hover:bg-white/10 text-gray-300 font-bold text-sm tracking-wide transition-colors">
                Talk to us
            </a>
        </div>
    </div>
</section>

@endsection
