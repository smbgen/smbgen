<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Extreme — Laravel Full-Stack App Generator</title>
    <meta name="description" content="Describe your app in plain English. Get a production-ready Laravel full-stack application — Livewire, Tailwind, auth, database, and all. Built to ship.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .hero-bg {
            background: radial-gradient(ellipse at 60% 0%, rgba(99,102,241,0.15) 0%, transparent 60%),
                        radial-gradient(ellipse at 10% 80%, rgba(236,72,153,0.10) 0%, transparent 50%),
                        #060d1a;
        }
        .card-glow:hover {
            box-shadow: 0 0 0 1px rgba(99,102,241,0.3), 0 8px 32px rgba(99,102,241,0.08);
        }
        .gradient-text {
            background: linear-gradient(135deg, #6366f1, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .faq-border {
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .intake-card {
            background: linear-gradient(135deg, rgba(99,102,241,0.06) 0%, rgba(236,72,153,0.06) 100%);
            border: 1px solid rgba(99,102,241,0.2);
        }
        .code-block {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.07);
            font-family: 'Fira Code', 'Cascadia Code', 'Consolas', monospace;
        }
        .tech-badge {
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
        }
    </style>
</head>
<body class="bg-[#060d1a] text-gray-100 antialiased font-sans">

    {{-- NAV --}}
    <nav class="sticky top-0 z-50 border-b border-red-900/30 backdrop-blur-md bg-[#060d1a]/90"
         style="box-shadow: 0 1px 0 rgba(220,38,38,0.12);">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3 group">
                <div class="relative w-8 h-8 flex-shrink-0">
                    <div class="absolute inset-0 rounded-lg bg-red-600 opacity-20 blur-sm group-hover:opacity-40 transition-opacity"></div>
                    <div class="relative w-8 h-8 rounded-lg bg-gradient-to-br from-red-600 to-red-900 border border-red-500/40 flex items-center justify-center shadow-lg shadow-red-900/50">
                        <svg class="w-4 h-4 text-white drop-shadow" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                        </svg>
                    </div>
                </div>
                <div class="flex items-baseline gap-2">
                    <span class="text-white font-black text-lg uppercase"
                          style="letter-spacing: 0.05em; text-shadow: 0 0 20px rgba(220,38,38,0.4);">EXTREME</span>
                    <span class="hidden sm:block text-red-800 text-xs font-mono">by smbgen</span>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <a href="#how-it-works" class="hidden md:block text-gray-400 hover:text-white text-sm transition-colors">How It Works</a>
                <a href="#stack" class="hidden md:block text-gray-400 hover:text-white text-sm transition-colors">Stack</a>
                <a href="#pricing" class="hidden md:block text-gray-400 hover:text-white text-sm transition-colors">Pricing</a>
                <a href="#start"
                   class="px-4 py-2 rounded-lg bg-red-700 hover:bg-red-600 text-white text-sm font-bold uppercase tracking-wide transition-colors border border-red-600/50 shadow-lg shadow-red-900/30">
                    Build My App
                </a>
            </div>
        </div>
    </nav>

    {{-- HERO --}}
    <section class="hero-bg min-h-[90vh] flex items-center">
        <div class="max-w-6xl mx-auto px-6 py-24 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-indigo-500/30 bg-indigo-500/10 text-indigo-400 text-xs font-medium mb-8">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                Lovable — but for Laravel
            </div>

            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight leading-[1.1] mb-6">
                Describe your app.<br>
                <span class="gradient-text">Ship production Laravel.</span>
            </h1>

            <p class="text-gray-400 text-lg sm:text-xl max-w-2xl mx-auto mb-4 leading-relaxed">
                Extreme turns a plain-English prompt into a fully-wired, production-ready Laravel application — Livewire components, Tailwind UI, authentication, database migrations, and API scaffolding included.
            </p>

            <p class="text-gray-500 text-sm max-w-xl mx-auto mb-10">
                No more boilerplate. No more generic starters. Get a real codebase you own, structured the Laravel way, ready to customise and deploy.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="#start" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-base transition-all shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/30 hover:-translate-y-0.5">
                    Start Building — Free
                </a>
                <a href="{{ route('extreme.demo') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl border border-white/10 hover:border-white/20 text-gray-300 hover:text-white font-medium text-base transition-all">
                    Try Live Demo →
                </a>
            </div>

            <p class="mt-6 text-gray-600 text-xs">Laravel 12 &nbsp;·&nbsp; Livewire 3 &nbsp;·&nbsp; Tailwind CSS 3 &nbsp;·&nbsp; Pest Tests &nbsp;·&nbsp; Ready to deploy</p>

            {{-- Prompt demo --}}
            <div class="mt-16 max-w-3xl mx-auto code-block rounded-2xl p-6 text-left">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-3 h-3 rounded-full bg-red-500/60"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500/60"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500/60"></div>
                    <span class="ml-2 text-gray-600 text-xs">extreme — prompt</span>
                </div>
                <p class="text-gray-500 text-sm mb-3">$ extreme generate</p>
                <p class="text-indigo-300 text-sm mb-1">
                    <span class="text-gray-600">›</span> Build me a multi-tenant SaaS for personal trainers. Clients can book sessions,
                </p>
                <p class="text-indigo-300 text-sm mb-1 pl-4">
                    trainers manage their schedule, and subscriptions are handled through Stripe.
                </p>
                <p class="text-indigo-300 text-sm mb-4 pl-4">
                    Auth via Google. Dark mode. Mobile-first.
                </p>
                <div class="border-t border-white/5 pt-4 space-y-1">
                    <p class="text-green-400 text-xs">✓ Scaffolding models: User, Trainer, Client, Session, Subscription</p>
                    <p class="text-green-400 text-xs">✓ Generating 14 migrations…</p>
                    <p class="text-green-400 text-xs">✓ Creating Livewire components: BookingCalendar, SubscriptionManager, TrainerDashboard…</p>
                    <p class="text-green-400 text-xs">✓ Wiring Stripe Cashier + Google Socialite…</p>
                    <p class="text-green-400 text-xs">✓ Writing 38 Pest tests…</p>
                    <p class="text-emerald-300 text-xs font-semibold mt-2">→ Your app is ready. Download or deploy.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- WHY EXTREME --}}
    <section class="py-20 border-t border-white/5">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-14">
                <p class="text-indigo-400 text-sm font-medium uppercase tracking-widest mb-3">Why Extreme</p>
                <h2 class="text-3xl sm:text-4xl font-bold">Built for developers who ship Laravel</h2>
                <p class="text-gray-500 mt-4 max-w-xl mx-auto text-sm">Other AI builders generate toy apps or lock you into a platform. Extreme generates real, idiomatic Laravel that you own outright.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                @foreach([
                    [
                        'icon' => '🧱',
                        'title' => 'Real Laravel. Not a wrapper.',
                        'desc' => 'Every file follows Laravel conventions — controllers, form requests, policies, factories, seeders. No proprietary abstractions. Open the project in PhpStorm and it just makes sense.',
                    ],
                    [
                        'icon' => '⚡',
                        'title' => 'Livewire-first UI',
                        'desc' => 'Reactive components wired with Livewire 3, Alpine.js, and Tailwind out of the box. No React, no Vue, no context switching — just Blade and Livewire the way the Laravel ecosystem intends.',
                    ],
                    [
                        'icon' => '🔒',
                        'title' => 'You own the code',
                        'desc' => "Download a clean zip or push directly to your GitHub repo. No vendor lock-in. No platform dependency. The generated code is yours — host it wherever, modify it however.",
                    ],
                    [
                        'icon' => '🧪',
                        'title' => 'Tests included',
                        'desc' => 'Every generated app ships with a Pest test suite — feature tests for routes, unit tests for business logic, and factory-seeded test data ready to run.',
                    ],
                    [
                        'icon' => '🏗️',
                        'title' => 'Multi-tenancy ready',
                        'desc' => 'Describe a SaaS and Extreme scaffolds tenant isolation, subdomain routing, and per-tenant configuration — tested and working out of the box.',
                    ],
                    [
                        'icon' => '🔌',
                        'title' => 'Integrations on demand',
                        'desc' => 'Mention Stripe, Google OAuth, Mailgun, S3, Twilio or any major service in your prompt. Extreme wires the package, config, and integration layer automatically.',
                    ],
                ] as $feat)
                <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/[0.07] card-glow transition-all">
                    <span class="text-3xl mb-4 block">{{ $feat['icon'] }}</span>
                    <h3 class="text-white font-semibold mb-2">{{ $feat['title'] }}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">{{ $feat['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- HOW IT WORKS --}}
    <section id="how-it-works" class="py-20 border-t border-white/5">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <p class="text-indigo-400 text-sm font-medium uppercase tracking-widest mb-3">Process</p>
                <h2 class="text-3xl sm:text-4xl font-bold">From prompt to production in minutes</h2>
            </div>

            <div class="grid md:grid-cols-4 gap-6">
                @foreach([
                    [
                        'step' => '01',
                        'title' => 'Describe your app',
                        'desc' => 'Write a plain-English prompt — what the app does, who uses it, what integrations you need, and what the UI should feel like.',
                        'icon' => '✍️',
                    ],
                    [
                        'step' => '02',
                        'title' => 'Extreme plans it',
                        'desc' => 'Extreme maps your prompt to routes, models, relationships, Livewire components, and third-party integrations before writing a single line.',
                        'icon' => '🗺️',
                    ],
                    [
                        'step' => '03',
                        'title' => 'Code is generated',
                        'desc' => 'A complete Laravel project is scaffolded — migrations, controllers, views, tests, config, and seed data — following Laravel conventions throughout.',
                        'icon' => '⚙️',
                    ],
                    [
                        'step' => '04',
                        'title' => 'Download & ship',
                        'desc' => 'Download a zip, clone from your GitHub repo, or deploy to Forge or Vapor. The app runs with one `composer install`.',
                        'icon' => '🚀',
                    ],
                ] as $s)
                <div class="p-6 rounded-2xl bg-white/[0.03] border border-white/[0.07] card-glow transition-all text-center">
                    <span class="text-3xl mb-4 block">{{ $s['icon'] }}</span>
                    <div class="text-4xl font-bold text-white/[0.05] mb-3 font-mono">{{ $s['step'] }}</div>
                    <h3 class="text-base font-semibold text-white mb-2">{{ $s['title'] }}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">{{ $s['desc'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- TECH STACK --}}
    <section id="stack" class="py-20 border-t border-white/5">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-14">
                <p class="text-indigo-400 text-sm font-medium uppercase tracking-widest mb-3">Stack</p>
                <h2 class="text-3xl sm:text-4xl font-bold">Every app ships with the modern Laravel stack</h2>
                <p class="text-gray-500 mt-4 max-w-xl mx-auto text-sm">No choices to make. No configuration. Just the stack the Laravel ecosystem settled on.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach([
                    ['name' => 'Laravel 12', 'detail' => 'Framework'],
                    ['name' => 'PHP 8.4', 'detail' => 'Runtime'],
                    ['name' => 'Livewire 3', 'detail' => 'Reactive UI'],
                    ['name' => 'Tailwind CSS 3', 'detail' => 'Styling'],
                    ['name' => 'Alpine.js 3', 'detail' => 'JS interactivity'],
                    ['name' => 'Pest 3', 'detail' => 'Testing'],
                    ['name' => 'Laravel Pint', 'detail' => 'Code style'],
                    ['name' => 'Vite', 'detail' => 'Asset bundling'],
                    ['name' => 'Eloquent ORM', 'detail' => 'Database'],
                    ['name' => 'Laravel Breeze', 'detail' => 'Auth scaffold'],
                    ['name' => 'Sanctum / Passport', 'detail' => 'API auth'],
                    ['name' => 'Queues & Jobs', 'detail' => 'Background work'],
                    ['name' => 'Stancl/Tenancy', 'detail' => 'Multi-tenancy'],
                    ['name' => 'Stripe Cashier', 'detail' => 'Billing'],
                    ['name' => 'Laravel Socialite', 'detail' => 'OAuth'],
                    ['name' => 'Laravel Nightwatch', 'detail' => 'Monitoring'],
                ] as $tech)
                <div class="tech-badge flex flex-col p-4 rounded-xl">
                    <span class="text-white text-sm font-medium">{{ $tech['name'] }}</span>
                    <span class="text-gray-600 text-xs mt-0.5">{{ $tech['detail'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- EXAMPLE PROMPTS --}}
    <section class="py-20 border-t border-white/5">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-14">
                <p class="text-pink-400 text-sm font-medium uppercase tracking-widest mb-3">Examples</p>
                <h2 class="text-3xl sm:text-4xl font-bold">What can you build?</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach([
                    [
                        'prompt' => 'A client portal for a law firm — secure document sharing, appointment booking via Google Calendar, invoice tracking with Stripe, and a client messaging thread.',
                        'tags' => ['Multi-auth', 'Stripe', 'Google Calendar', 'Document uploads'],
                    ],
                    [
                        'prompt' => 'A multi-tenant SaaS where each company gets their own subdomain, admin dashboard, and blog. Super-admin can impersonate any tenant.',
                        'tags' => ['Multi-tenancy', 'Subdomain routing', 'Super-admin', 'CMS'],
                    ],
                    [
                        'prompt' => 'A job board with employer and candidate roles. Employers post listings, candidates apply, and a queue sends notification emails on application events.',
                        'tags' => ['Multi-role auth', 'Queues', 'Email notifications', 'Search'],
                    ],
                    [
                        'prompt' => 'An internal ops tool for a small team — task management, file attachments, time tracking, and a Slack webhook for status changes.',
                        'tags' => ['Internal admin', 'File uploads', 'Webhooks', 'API'],
                    ],
                    [
                        'prompt' => 'A subscription newsletter platform where writers publish posts and readers subscribe via Stripe. Paid posts are gated behind subscription.',
                        'tags' => ['Stripe Cashier', 'Subscriptions', 'Content gating', 'WYSIWYG'],
                    ],
                    [
                        'prompt' => 'An e-commerce MVP — product catalogue, cart, Stripe Checkout, order management, and email receipts. Admin can manage stock and orders.',
                        'tags' => ['Stripe Checkout', 'Admin panel', 'Cart', 'Orders', 'Emails'],
                    ],
                ] as $example)
                <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/[0.07] card-glow transition-all">
                    <p class="text-gray-300 text-sm leading-relaxed mb-4 italic">"{{ $example['prompt'] }}"</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($example['tags'] as $tag)
                        <span class="px-2 py-0.5 rounded-md bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- PRICING --}}
    <section id="pricing" class="py-20 border-t border-white/5">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <p class="text-indigo-400 text-sm font-medium uppercase tracking-widest mb-3">Pricing</p>
                <h2 class="text-3xl sm:text-4xl font-bold">Simple, honest pricing</h2>
                <p class="text-gray-500 mt-4 max-w-xl mx-auto text-sm">Cancel anytime. Every plan includes full source code, no watermarks, no code expiry.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto">

                {{-- Starter --}}
                <div class="p-8 rounded-2xl bg-white/[0.03] border border-white/[0.08]">
                    <p class="text-gray-400 text-sm font-medium mb-2">Starter</p>
                    <div class="flex items-end gap-2 mb-1">
                        <span class="text-5xl font-bold text-white">$49</span>
                        <span class="text-gray-500 mb-2">/mo</span>
                    </div>
                    <p class="text-gray-600 text-xs mb-6">3 app generations / month</p>
                    <ul class="space-y-3 mb-8">
                        @foreach([
                            'Up to 3 generations/mo',
                            'Full source code download',
                            'Standard Laravel stack',
                            'Basic integrations (auth, DB)',
                            'Community support',
                        ] as $feat)
                        <li class="flex items-center gap-3 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            {{ $feat }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('cleanslate.billing.plans') }}" class="block text-center w-full py-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 text-white font-semibold text-sm transition-all">
                        Get Started
                    </a>
                </div>

                {{-- Pro --}}
                <div class="p-8 rounded-2xl border border-indigo-500/40 bg-indigo-500/5 relative overflow-hidden">
                    <div class="absolute top-4 right-4 px-2 py-0.5 rounded-full bg-indigo-500/20 border border-indigo-500/30 text-indigo-300 text-xs font-medium">
                        Most Popular
                    </div>
                    <p class="text-gray-400 text-sm font-medium mb-2">Pro</p>
                    <div class="flex items-end gap-2 mb-1">
                        <span class="text-5xl font-bold text-white">$149</span>
                        <span class="text-gray-500 mb-2">/mo</span>
                    </div>
                    <p class="text-gray-600 text-xs mb-6">Unlimited generations</p>
                    <ul class="space-y-3 mb-8">
                        @foreach([
                            'Unlimited app generations',
                            'GitHub push on generation',
                            'Full integration library',
                            'Multi-tenancy scaffolding',
                            'Stripe, OAuth, queues',
                            'Priority support',
                        ] as $feat)
                        <li class="flex items-center gap-3 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            {{ $feat }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('cleanslate.billing.plans') }}" class="block text-center w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm transition-all">
                        Get Started
                    </a>
                </div>

                {{-- Agency --}}
                <div class="p-8 rounded-2xl border border-pink-500/30 bg-pink-500/5">
                    <p class="text-gray-400 text-sm font-medium mb-2">Agency</p>
                    <div class="flex items-end gap-2 mb-1">
                        <span class="text-5xl font-bold text-white">$399</span>
                        <span class="text-gray-500 mb-2">/mo</span>
                    </div>
                    <p class="text-gray-600 text-xs mb-6">5 seats + white-label</p>
                    <ul class="space-y-3 mb-8">
                        @foreach([
                            'Everything in Pro',
                            '5 team member seats',
                            'White-label output',
                            'Custom tech stack presets',
                            'Deploy-to-Forge integration',
                            'Dedicated support channel',
                        ] as $feat)
                        <li class="flex items-center gap-3 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-pink-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            {{ $feat }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('cleanslate.billing.plans') }}" class="block text-center w-full py-3 rounded-xl border border-pink-500/40 hover:border-pink-400 text-pink-300 hover:text-pink-200 font-semibold text-sm transition-all">
                        Get Started
                    </a>
                </div>
            </div>

            <p class="text-center text-gray-600 text-xs mt-8">
                Cancel anytime &nbsp;·&nbsp; Billed monthly via Stripe &nbsp;·&nbsp; All code is yours, no royalties
            </p>
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="py-20 border-t border-white/5">
        <div class="max-w-3xl mx-auto px-6">
            <div class="text-center mb-16">
                <p class="text-indigo-400 text-sm font-medium uppercase tracking-widest mb-3">FAQ</p>
                <h2 class="text-3xl sm:text-4xl font-bold">Questions answered</h2>
            </div>

            <div class="space-y-0">
                @foreach([
                    [
                        'q' => 'Is the generated code actually production-quality?',
                        'a' => "Yes — that's the whole point. Extreme generates idiomatic Laravel. Models use Eloquent relationships, controllers use Form Requests, routes are named and grouped properly, and every app ships with a Pest test suite. It's structured the way an experienced Laravel developer would build it.",
                    ],
                    [
                        'q' => 'Can I edit the generated code?',
                        'a' => "Absolutely. You own the code outright. Download it, push it to your own repo, open it in any editor. There's no runtime dependency on Extreme — once you have the code, it's just a standard Laravel project.",
                    ],
                    [
                        'q' => 'How accurate is it for complex prompts?',
                        'a' => "Very good for well-defined apps. The more specific your prompt — roles, integrations, UI expectations — the better the output. For highly complex or unusual requirements, Extreme generates the right scaffold and structure; you fill in the bespoke business logic.",
                    ],
                    [
                        'q' => 'Does it work with existing Laravel projects?',
                        'a' => "Currently Extreme generates new projects from scratch. We're building an extend mode that adds modules, Livewire components, and integrations into an existing codebase — coming in a future release.",
                    ],
                    [
                        'q' => 'What databases does it support?',
                        'a' => "MySQL, PostgreSQL, and SQLite out of the box. Migrations are generated for the database type you specify in your prompt. MongoDB support is on the roadmap.",
                    ],
                    [
                        'q' => 'How does this compare to Lovable or Bolt?',
                        'a' => "Lovable and Bolt generate React frontends and generic backends. Extreme is laser-focused on Laravel — Livewire components, Blade with Tailwind, proper Eloquent models, queue workers, the Artisan workflow. If you're a PHP developer who builds with Laravel, this is built for you.",
                    ],
                ] as $faq)
                <div class="faq-border" x-data="{ open: false }">
                    <button
                        @click="open = !open"
                        class="w-full flex items-center justify-between py-5 text-left group"
                    >
                        <span class="text-white font-medium group-hover:text-indigo-300 transition-colors pr-4">{{ $faq['q'] }}</span>
                        <svg
                            class="w-5 h-5 text-gray-500 flex-shrink-0 transition-transform duration-200"
                            :class="{ 'rotate-45': open }"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                    </button>
                    <div x-show="open" x-collapse class="pb-5">
                        <p class="text-gray-400 leading-relaxed text-sm">{{ $faq['a'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- INTAKE / WAITLIST FORM --}}
    <section id="start" class="py-20 border-t border-white/5">
        <div class="max-w-2xl mx-auto px-6">
            <div class="text-center mb-10">
                <p class="text-indigo-400 text-sm font-medium uppercase tracking-widest mb-3">Get Early Access</p>
                <h2 class="text-3xl sm:text-4xl font-bold mb-4">Join the waitlist</h2>
                <p class="text-gray-400 text-sm">Extreme is in active development. Leave your details and we'll reach out when you can start building.</p>
            </div>

            <div class="intake-card rounded-2xl p-8">
                @if(session('success'))
                    <div class="p-5 bg-green-500/10 border border-green-500/30 rounded-xl flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full bg-green-500/20 flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                        </div>
                        <div>
                            <p class="font-semibold text-green-400 mb-1">You're on the list!</p>
                            <p class="text-green-300 text-sm">{{ session('success') }}</p>
                        </div>
                    </div>
                @else

                @if(session('error'))
                    <div class="mb-5 p-4 bg-red-500/10 border border-red-500/30 text-red-400 rounded-xl text-sm flex items-center gap-3">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('extreme.intake') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-2 uppercase tracking-wide">Name <span class="text-red-400">*</span></label>
                            <input type="text" name="name" required value="{{ old('name') }}"
                                class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/30 text-white placeholder-gray-600 text-sm outline-none transition-all"
                                placeholder="Alex Ramsey">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-2 uppercase tracking-wide">Email <span class="text-red-400">*</span></label>
                            <input type="email" name="email" required value="{{ old('email') }}"
                                class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/30 text-white placeholder-gray-600 text-sm outline-none transition-all"
                                placeholder="alex@example.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-2 uppercase tracking-wide">What would you build first?</label>
                        <textarea name="message" rows="4"
                            class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/30 text-white placeholder-gray-600 text-sm outline-none transition-all resize-none"
                            placeholder="Describe the app you'd generate with Extreme — the type, features, integrations you'd want...">{{ old('message') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-2 uppercase tracking-wide">I'm a…</label>
                        <select name="engagement_type"
                            class="w-full px-4 py-3 rounded-xl bg-[#0a1628] border border-white/10 focus:border-indigo-500/50 focus:ring-1 focus:ring-indigo-500/30 text-gray-300 text-sm outline-none transition-all">
                            <option value="freelancer">Freelance Laravel developer</option>
                            <option value="agency">Agency or consultancy</option>
                            <option value="founder">Founder building a product</option>
                            <option value="internal">Internal developer / startup employee</option>
                            <option value="unsure">Not sure yet</option>
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full py-4 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-semibold text-sm transition-all shadow-lg shadow-indigo-500/20 hover:shadow-indigo-500/30 hover:-translate-y-0.5">
                        Request Early Access
                    </button>

                    <p class="text-center text-gray-600 text-xs">
                        No spam. We'll reach out personally when your spot is ready.
                    </p>
                </form>
                @endif
            </div>
        </div>
    </section>

    {{-- FOOTER --}}
    <footer class="border-t border-red-900/20 py-10">
        <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-6 h-6 rounded-lg bg-gradient-to-br from-red-600 to-red-900 border border-red-500/30 flex items-center justify-center shadow shadow-red-900/40">
                    <svg class="w-3.5 h-3.5 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                    </svg>
                </div>
                <span class="text-gray-500 text-sm"><span class="text-white font-bold uppercase tracking-wide" style="text-shadow:0 0 12px rgba(220,38,38,0.3);">EXTREME</span> by smbgen</span>
            </div>
            <div class="flex items-center gap-6">
                <a href="{{ route('extreme') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors">Home</a>
                <a href="#pricing" class="text-gray-500 hover:text-gray-300 text-sm transition-colors">Pricing</a>
                <a href="#start" class="text-gray-500 hover:text-gray-300 text-sm transition-colors">Get Access</a>
            </div>
            <p class="text-gray-700 text-xs">© {{ date('Y') }} smbgen. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
