<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Extreme — Full-Stack App Generator</title>
    <meta name="description" content="Describe your app in plain English. Get a production-ready full-stack application — reactive UI, styling, auth, database, and all. Built to ship.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .hero-bg {
            background: radial-gradient(ellipse at 60% 0%, rgba(220,38,38,0.12) 0%, transparent 60%),
                        radial-gradient(ellipse at 10% 80%, rgba(239,68,68,0.07) 0%, transparent 50%),
                        #060d1a;
        }
        .card-glow:hover {
            box-shadow: 0 0 0 1px rgba(220,38,38,0.25), 0 8px 32px rgba(220,38,38,0.07);
        }
        .gradient-text {
            background: linear-gradient(135deg, #f87171, #fbbf24);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .faq-border {
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .intake-card {
            background: linear-gradient(135deg, rgba(220,38,38,0.05) 0%, rgba(239,68,68,0.03) 100%);
            border: 1px solid rgba(220,38,38,0.2);
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
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-red-600/30 bg-red-600/10 text-red-400 text-xs font-medium mb-8">
                <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                Full Stack Instant Generation
            </div>

            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight leading-[1.1] mb-6">
                Describe your app.<br>
                <span class="gradient-text">Ship production code.</span>
            </h1>

            <p class="text-gray-400 text-lg sm:text-xl max-w-2xl mx-auto mb-4 leading-relaxed">
                Extreme turns a plain-English prompt into a fully-wired, production-ready full-stack application — reactive components, styled UI, authentication, database migrations, and API scaffolding included.
            </p>

            <p class="text-gray-500 text-sm max-w-xl mx-auto mb-10">
                No more boilerplate. No more generic starters. Get a real codebase you own, structured the right way, ready to customise and deploy.
            </p>

            <div class="flex items-center justify-center">
                <a href="{{ route('extreme.demo') }}"
                   class="inline-flex items-center gap-3 px-12 py-4 rounded-xl bg-red-700 hover:bg-red-600 text-white font-black uppercase tracking-widest text-xl transition-all shadow-xl shadow-red-900/50 hover:-translate-y-0.5 border border-red-600/40"
                   style="text-shadow: 0 1px 6px rgba(0,0,0,0.5);">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    LAUNCH
                </a>
            </div>

            <p class="mt-6 text-gray-600 text-xs">Full-Stack Framework &nbsp;·&nbsp; Reactive UI &nbsp;·&nbsp; Styled &amp; Tested &nbsp;·&nbsp; Ready to deploy</p>

            {{-- Prompt demo --}}
            <div class="mt-16 max-w-3xl mx-auto code-block rounded-2xl p-6 text-left">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-3 h-3 rounded-full bg-red-500/60"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500/60"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500/60"></div>
                    <span class="ml-2 text-gray-600 text-xs">extreme — prompt</span>
                </div>
                <p class="text-gray-500 text-sm mb-3">$ extreme generate</p>
                <p class="text-red-300 text-sm mb-1">
                    <span class="text-gray-600">›</span> Build me a multi-tenant SaaS for personal trainers. Clients can book sessions,
                </p>
                <p class="text-red-300 text-sm mb-1 pl-4">
                    trainers manage their schedule, and subscriptions are handled via billing.
                </p>
                <p class="text-red-300 text-sm mb-4 pl-4">
                    OAuth login. Dark mode. Mobile-first.
                </p>
                <div class="border-t border-white/5 pt-4 space-y-1">
                    <p class="text-green-400 text-xs">✓ Scaffolding models: User, Trainer, Client, Session, Subscription</p>
                    <p class="text-green-400 text-xs">✓ Generating 14 migrations…</p>
                    <p class="text-green-400 text-xs">✓ Creating reactive components: BookingCalendar, SubscriptionManager, TrainerDashboard…</p>
                    <p class="text-green-400 text-xs">✓ Wiring billing + OAuth providers…</p>
                    <p class="text-green-400 text-xs">✓ Writing 38 automated tests…</p>
                    <p class="text-emerald-300 text-xs font-semibold mt-2">→ Your app is ready. Download or deploy.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- WHY EXTREME --}}
    <section class="py-20 border-t border-white/5">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-14">
                <p class="text-red-500 text-sm font-medium uppercase tracking-widest mb-3">Why Extreme</p>
                <h2 class="text-3xl sm:text-4xl font-bold">Built for developers who ship</h2>
                <p class="text-gray-500 mt-4 max-w-xl mx-auto text-sm">Other AI builders generate toy apps or lock you into a platform. Extreme generates real, idiomatic code that you own outright.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                @foreach([
                    [
                        'icon' => '🧱',
                        'title' => 'Real code. Not a wrapper.',
                        'desc' => 'Every file follows established conventions — controllers, form requests, policies, factories, seeders. No proprietary abstractions. Open the project in your editor and it just makes sense.',
                    ],
                    [
                        'icon' => '⚡',
                        'title' => 'Reactive-first UI',
                        'desc' => 'Reactive components wired with a modern UI stack out of the box. No context switching — just clean, server-driven interactivity the way modern full-stack development intends.',
                    ],
                    [
                        'icon' => '🔒',
                        'title' => 'You own the code',
                        'desc' => "Download a clean zip or push directly to your own repo. No vendor lock-in. No platform dependency. The generated code is yours — host it wherever, modify it however.",
                    ],
                    [
                        'icon' => '🧪',
                        'title' => 'Tests included',
                        'desc' => 'Every generated app ships with a full test suite — feature tests for routes, unit tests for business logic, and factory-seeded test data ready to run.',
                    ],
                    [
                        'icon' => '🏗️',
                        'title' => 'Multi-tenancy ready',
                        'desc' => 'Describe a SaaS and Extreme scaffolds tenant isolation, subdomain routing, and per-tenant configuration — tested and working out of the box.',
                    ],
                    [
                        'icon' => '🔌',
                        'title' => 'Integrations on demand',
                        'desc' => 'Mention billing, OAuth, email delivery, file storage, or SMS in your prompt. Extreme wires the package, config, and integration layer automatically.',
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
                <p class="text-red-500 text-sm font-medium uppercase tracking-widest mb-3">Process</p>
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
                        'desc' => 'Extreme maps your prompt to routes, models, relationships, reactive components, and integrations before writing a single line.',
                        'icon' => '🗺️',
                    ],
                    [
                        'step' => '03',
                        'title' => 'Code is generated',
                        'desc' => 'A complete project is scaffolded — migrations, controllers, views, tests, config, and seed data — following best practices throughout.',
                        'icon' => '⚙️',
                    ],
                    [
                        'step' => '04',
                        'title' => 'Download & ship',
                        'desc' => 'Download a zip, clone from your own repo, or deploy to your preferred host. The app runs with one install command.',
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
                <p class="text-red-500 text-sm font-medium uppercase tracking-widest mb-3">Stack</p>
                <h2 class="text-3xl sm:text-4xl font-bold">Every app ships with a modern, proven stack</h2>
                <p class="text-gray-500 mt-4 max-w-xl mx-auto text-sm">No choices to make. No configuration. Just the stack modern full-stack development settled on.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach([
                    ['name' => 'Full-Stack Framework', 'detail' => 'Framework'],
                    ['name' => 'Modern PHP', 'detail' => 'Runtime'],
                    ['name' => 'Reactive UI', 'detail' => 'Reactive UI'],
                    ['name' => 'Utility CSS', 'detail' => 'Styling'],
                    ['name' => 'JS Interactivity', 'detail' => 'JS interactivity'],
                    ['name' => 'Test Suite', 'detail' => 'Testing'],
                    ['name' => 'Code Style', 'detail' => 'Code style'],
                    ['name' => 'Asset Bundling', 'detail' => 'Asset bundling'],
                    ['name' => 'ORM', 'detail' => 'Database'],
                    ['name' => 'Auth Scaffold', 'detail' => 'Auth scaffold'],
                    ['name' => 'API Auth', 'detail' => 'API auth'],
                    ['name' => 'Queues & Jobs', 'detail' => 'Background work'],
                    ['name' => 'Multi-tenancy', 'detail' => 'Multi-tenancy'],
                    ['name' => 'Billing', 'detail' => 'Billing'],
                    ['name' => 'OAuth', 'detail' => 'OAuth'],
                    ['name' => 'Monitoring', 'detail' => 'Monitoring'],
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
                <p class="text-red-500 text-sm font-medium uppercase tracking-widest mb-3">Examples</p>
                <h2 class="text-3xl sm:text-4xl font-bold">What can you build?</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach([
                    [
                        'prompt' => 'A client portal for a law firm — secure document sharing, appointment booking via calendar integration, invoice tracking with billing, and a client messaging thread.',
                        'tags' => ['Multi-auth', 'Billing', 'Calendar integration', 'Document uploads'],
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
                        'prompt' => 'An internal ops tool for a small team — task management, file attachments, time tracking, and a webhook for status changes.',
                        'tags' => ['Internal admin', 'File uploads', 'Webhooks', 'API'],
                    ],
                    [
                        'prompt' => 'A subscription newsletter platform where writers publish posts and readers subscribe via billing. Paid posts are gated behind subscription.',
                        'tags' => ['Billing', 'Subscriptions', 'Content gating', 'WYSIWYG'],
                    ],
                    [
                        'prompt' => 'An e-commerce MVP — product catalogue, cart, checkout, order management, and email receipts. Admin can manage stock and orders.',
                        'tags' => ['Checkout', 'Admin panel', 'Cart', 'Orders', 'Emails'],
                    ],
                ] as $example)
                <div class="p-5 rounded-2xl bg-white/[0.03] border border-white/[0.07] card-glow transition-all">
                    <p class="text-gray-300 text-sm leading-relaxed mb-4 italic">"{{ $example['prompt'] }}"</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach($example['tags'] as $tag)
                        <span class="px-2 py-0.5 rounded-md bg-red-600/10 border border-red-600/20 text-red-400 text-xs">{{ $tag }}</span>
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
                <p class="text-red-500 text-sm font-medium uppercase tracking-widest mb-3">Pricing</p>
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
                            'Standard full-stack',
                            'Basic integrations (auth, DB)',
                            'Community support',
                        ] as $feat)
                        <li class="flex items-center gap-3 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
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
                <div class="p-8 rounded-2xl border border-red-600/40 bg-red-600/5 relative overflow-hidden">
                    <div class="absolute top-4 right-4 px-2 py-0.5 rounded-full bg-red-600/20 border border-red-600/30 text-red-300 text-xs font-medium">
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
                            'Repo push on generation',
                            'Full integration library',
                            'Multi-tenancy scaffolding',
                            'Billing, OAuth, queues',
                            'Priority support',
                        ] as $feat)
                        <li class="flex items-center gap-3 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            {{ $feat }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('cleanslate.billing.plans') }}" class="block text-center w-full py-3 rounded-xl bg-red-700 hover:bg-red-600 text-white font-bold uppercase tracking-wide text-sm transition-all border border-red-600/40">
                        Get Started
                    </a>
                </div>

                {{-- Agency --}}
                <div class="p-8 rounded-2xl border border-red-700/30 bg-red-700/5">
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
                            'One-click deploy integration',
                            'Dedicated support channel',
                        ] as $feat)
                        <li class="flex items-center gap-3 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-red-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            {{ $feat }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('cleanslate.billing.plans') }}" class="block text-center w-full py-3 rounded-xl border border-red-600/40 hover:border-red-500 text-red-300 hover:text-red-200 font-semibold text-sm transition-all">
                        Get Started
                    </a>
                </div>
            </div>

            <p class="text-center text-gray-600 text-xs mt-8">
                Cancel anytime &nbsp;·&nbsp; Billed monthly &nbsp;·&nbsp; All code is yours, no royalties
            </p>
        </div>
    </section>

    {{-- FAQ --}}
    <section id="faq" class="py-20 border-t border-white/5">
        <div class="max-w-3xl mx-auto px-6">
            <div class="text-center mb-16">
                <p class="text-red-500 text-sm font-medium uppercase tracking-widest mb-3">FAQ</p>
                <h2 class="text-3xl sm:text-4xl font-bold">Questions answered</h2>
            </div>

            <div class="space-y-0">
                @foreach([
                    [
                        'q' => 'Is the generated code actually production-quality?',
                        'a' => "Yes — that's the whole point. Extreme generates idiomatic, well-structured code. Models use proper relationships, controllers use validated request objects, routes are named and grouped correctly, and every app ships with a full test suite. It's structured the way an experienced developer would build it.",
                    ],
                    [
                        'q' => 'Can I edit the generated code?',
                        'a' => "Absolutely. You own the code outright. Download it, push it to your own repo, open it in any editor. There's no runtime dependency on Extreme — once you have the code, it's a standard project.",
                    ],
                    [
                        'q' => 'How accurate is it for complex prompts?',
                        'a' => "Very good for well-defined apps. The more specific your prompt — roles, integrations, UI expectations — the better the output. For highly complex or unusual requirements, Extreme generates the right scaffold and structure; you fill in the bespoke business logic.",
                    ],
                    [
                        'q' => 'Does it work with existing projects?',
                        'a' => "Currently Extreme generates new projects from scratch. We're building an extend mode that adds modules, reactive components, and integrations into an existing codebase — coming in a future release.",
                    ],
                    [
                        'q' => 'What databases does it support?',
                        'a' => "MySQL, PostgreSQL, and SQLite out of the box. Migrations are generated for the database type you specify in your prompt. Additional database support is on the roadmap.",
                    ],
                    [
                        'q' => 'How does this compare to other AI app builders?',
                        'a' => "Most AI builders generate generic frontends and shallow backends. Extreme is laser-focused on generating production-quality, full-stack applications — reactive components, server-driven UI, proper data models, queue workers, and a complete test suite. If you're a developer who wants code you can actually own and ship, this is built for you.",
                    ],
                ] as $faq)
                <div class="faq-border" x-data="{ open: false }">
                    <button
                        @click="open = !open"
                        class="w-full flex items-center justify-between py-5 text-left group"
                    >
                        <span class="text-white font-medium group-hover:text-red-400 transition-colors pr-4">{{ $faq['q'] }}</span>
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

    {{-- CTA --}}
    <section id="start" class="py-28 border-t border-white/5">
        <div class="max-w-2xl mx-auto px-6 text-center">
            <div class="relative w-16 h-16 mx-auto mb-8">
                <div class="absolute inset-0 rounded-2xl bg-red-600 opacity-25 blur-md"></div>
                <div class="relative w-16 h-16 rounded-2xl bg-gradient-to-br from-red-600 to-red-900 border border-red-500/40 flex items-center justify-center shadow-xl shadow-red-900/50">
                    <svg class="w-8 h-8 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                    </svg>
                </div>
            </div>
            <h2 class="text-4xl sm:text-5xl font-black uppercase tracking-tight text-white mb-4"
                style="text-shadow: 0 0 40px rgba(220,38,38,0.3);">Ready to build?</h2>
            <p class="text-gray-500 text-base mb-10">Describe your app in plain English and watch it get built — live.</p>
            <a href="{{ route('extreme.demo') }}"
               class="inline-flex items-center gap-3 px-14 py-5 rounded-2xl bg-red-700 hover:bg-red-600 text-white font-black uppercase tracking-widest text-xl transition-all shadow-2xl shadow-red-900/50 hover:-translate-y-1 border border-red-600/40"
               style="text-shadow: 0 1px 6px rgba(0,0,0,0.4);">
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                LAUNCH
            </a>
            <p class="mt-8 text-gray-700 text-xs">Full-Stack Framework &nbsp;·&nbsp; Reactive UI &nbsp;·&nbsp; Styled &amp; Tested &nbsp;·&nbsp; Ready to deploy</p>
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
                <a href="{{ route('extreme.demo') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors">Launch</a>
            </div>
            <p class="text-gray-700 text-xs">© {{ date('Y') }} smbgen. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
