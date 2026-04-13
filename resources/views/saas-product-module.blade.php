<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SaaS Product Module — Digital Reputation & Data Suppression</title>
    <meta name="description" content="A personal, done-for-you service that identifies and pursues removal of your data from public broker sites. Honest work. Real follow-through.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .hero-bg {
            background: radial-gradient(ellipse at 60% 0%, rgba(6,182,212,0.12) 0%, transparent 60%),
                        radial-gradient(ellipse at 10% 80%, rgba(139,92,246,0.10) 0%, transparent 50%),
                        #060d1a;
        }
        .card-glow:hover {
            box-shadow: 0 0 0 1px rgba(6,182,212,0.3), 0 8px 32px rgba(6,182,212,0.08);
        }
        .step-line::after {
            content: '';
            position: absolute;
            top: 24px;
            left: calc(50% + 32px);
            width: calc(100% - 64px);
            height: 1px;
            background: linear-gradient(to right, rgba(6,182,212,0.4), rgba(139,92,246,0.2));
        }
        @media (max-width: 768px) {
            .step-line::after { display: none; }
        }
        .gradient-text {
            background: linear-gradient(135deg, #06b6d4, #8b5cf6);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .faq-border {
            border-bottom: 1px solid rgba(255,255,255,0.07);
        }
        .intake-card {
            background: linear-gradient(135deg, rgba(6,182,212,0.06) 0%, rgba(139,92,246,0.06) 100%);
            border: 1px solid rgba(6,182,212,0.2);
        }
    </style>
</head>
<body class="bg-[#060d1a] text-gray-100 antialiased font-sans">

    {{-- ─── NAV ─────────────────────────────────────────────────── --}}
    <nav class="sticky top-0 z-50 border-b border-white/5 backdrop-blur-md bg-[#060d1a]/80">
        <div class="max-w-6xl mx-auto px-6 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-md bg-gradient-to-br from-cyan-500 to-violet-500 flex items-center justify-center">
                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 10c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.286z" />
                    </svg>
                </div>
                <span class="text-white font-semibold text-lg tracking-tight">SaaS Product Module</span>
                <span class="hidden sm:block text-gray-500 text-sm">by L7 Media Labs</span>
            </div>
            <div class="flex items-center gap-4">
                <a href="#how-it-works" class="hidden md:block text-gray-400 hover:text-white text-sm transition-colors">How It Works</a>
                <a href="#pricing" class="hidden md:block text-gray-400 hover:text-white text-sm transition-colors">Pricing</a>
                <a href="#faq" class="hidden md:block text-gray-400 hover:text-white text-sm transition-colors">FAQ</a>
                <a href="{{ route('saasproductmodule.billing.plans') }}" class="px-4 py-2 rounded-lg bg-cyan-500 hover:bg-cyan-400 text-white text-sm font-medium transition-colors">
                    Get Started
                </a>
            </div>
        </div>
    </nav>

    {{-- ─── HERO ────────────────────────────────────────────────── --}}
    <section class="hero-bg min-h-[90vh] flex items-center">
        <div class="max-w-6xl mx-auto px-6 py-24 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-cyan-500/30 bg-cyan-500/10 text-cyan-400 text-xs font-medium mb-8">
                <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 animate-pulse"></span>
                Personal. Persistent. Honest.
            </div>

            <h1 class="text-5xl sm:text-6xl lg:text-7xl font-bold tracking-tight leading-[1.1] mb-6">
                Your data is out there.<br>
                <span class="gradient-text">We go after it.</span>
            </h1>

            <p class="text-gray-400 text-lg sm:text-xl max-w-2xl mx-auto mb-4 leading-relaxed">
                SaaS Product Module is a done-for-you data suppression service. A real person finds your exposed personal information across public data broker sites and pursues removal on your behalf — relentlessly.
            </p>

            <p class="text-gray-500 text-sm max-w-xl mx-auto mb-10">
                We're a consulting service. We can't guarantee every removal — but we don't stop until we've exhausted every option.
            </p>

            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('saasproductmodule.billing.plans') }}" class="w-full sm:w-auto px-8 py-4 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-white font-semibold text-base transition-all shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/30 hover:-translate-y-0.5">
                    View Plans & Get Started
                </a>
                <a href="#how-it-works" class="w-full sm:w-auto px-8 py-4 rounded-xl border border-white/10 hover:border-white/20 text-gray-300 hover:text-white font-medium text-base transition-all">
                    See How It Works
                </a>
            </div>

            <p class="mt-6 text-gray-600 text-xs">One person per engagement &nbsp;·&nbsp; 2–4 week timeline &nbsp;·&nbsp; Up to 3 removal attempts per listing</p>
        </div>
    </section>

    {{-- ─── THE PROBLEM ─────────────────────────────────────────── --}}
    <section class="py-20 border-t border-white/5">
        <div class="max-w-6xl mx-auto px-6">
            <div class="grid md:grid-cols-2 gap-16 items-center">
                <div>
                    <p class="text-cyan-400 text-sm font-medium uppercase tracking-widest mb-4">The Problem</p>
                    <h2 class="text-3xl sm:text-4xl font-bold leading-tight mb-6">
                        Data brokers make a business out of your private life.
                    </h2>
                    <p class="text-gray-400 leading-relaxed mb-4">
                        Hundreds of data broker sites scrape, compile, and publish personal records — home addresses, phone numbers, family members, financial history — and sell access to anyone who pays. Most people don't know how many sites have their information, or how to get it removed.
                    </p>
                    <p class="text-gray-400 leading-relaxed">
                        Even when you do get something removed, brokers re-scrape their sources and the listing reappears weeks later. Staying on top of it is a second job. We take that on for you.
                    </p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    @foreach([
                        ['icon' => '🏠', 'label' => 'Home addresses'],
                        ['icon' => '📞', 'label' => 'Phone numbers'],
                        ['icon' => '👨‍👩‍👧', 'label' => 'Family members'],
                        ['icon' => '📍', 'label' => 'Location history'],
                        ['icon' => '💼', 'label' => 'Employer records'],
                        ['icon' => '🔗', 'label' => 'Associated accounts'],
                    ] as $item)
                    <div class="flex items-center gap-3 p-4 rounded-xl bg-white/[0.03] border border-white/[0.06]">
                        <span class="text-2xl">{{ $item['icon'] }}</span>
                        <span class="text-gray-300 text-sm">{{ $item['label'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- ─── HOW IT WORKS ────────────────────────────────────────── --}}
    <section id="how-it-works" class="py-20 border-t border-white/5">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <p class="text-cyan-400 text-sm font-medium uppercase tracking-widest mb-3">Process</p>
                <h2 class="text-3xl sm:text-4xl font-bold">How it works</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-6 relative">
                @foreach([
                    [
                        'step' => '01',
                        'title' => 'Intake & Scan',
                        'desc' => 'You fill out a short intake form. We use your details to run a comprehensive scan across known data broker networks, documenting every active listing with screenshots and direct URLs.',
                        'duration' => '1–2 business days',
                    ],
                    [
                        'step' => '02',
                        'title' => 'Submit & Pursue',
                        'desc' => 'We submit formal opt-out and removal requests to each broker on your behalf — and follow up. When brokers ignore requests or require identity verification, we handle the coordination.',
                        'duration' => '1–3 weeks',
                    ],
                    [
                        'step' => '03',
                        'title' => 'Verify & Document',
                        'desc' => 'We confirm removals, re-pursue any listings that resurface (up to 3 attempts), and deliver a final report documenting every action taken and every confirmed removal.',
                        'duration' => 'Ongoing through 4 weeks',
                    ],
                ] as $i => $s)
                <div class="relative p-6 rounded-2xl bg-white/[0.03] border border-white/[0.07] card-glow transition-all {{ $i < 2 ? 'step-line' : '' }}">
                    <div class="text-4xl font-bold text-white/[0.06] mb-4 font-mono">{{ $s['step'] }}</div>
                    <h3 class="text-lg font-semibold text-white mb-3">{{ $s['title'] }}</h3>
                    <p class="text-gray-400 text-sm leading-relaxed mb-4">{{ $s['desc'] }}</p>
                    <span class="inline-block text-xs text-cyan-400/70 font-medium">⏱ {{ $s['duration'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ─── WHAT'S INCLUDED ─────────────────────────────────────── --}}
    <section class="py-20 border-t border-white/5">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <p class="text-cyan-400 text-sm font-medium uppercase tracking-widest mb-3">Scope</p>
                <h2 class="text-3xl sm:text-4xl font-bold">What's included in your engagement</h2>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach([
                    ['title' => 'Dedicated point of contact', 'desc' => 'One person handles your entire engagement start to finish — not a ticket queue.'],
                    ['title' => 'Up to 5 removal actions', 'desc' => 'Across multiple broker sites. Each action includes submission, tracking, and follow-up.'],
                    ['title' => 'Up to 3 attempts per listing', 'desc' => 'If a removal is rejected or the listing reappears, we go after it again — up to 3 times.'],
                    ['title' => 'Full documentation', 'desc' => 'Screenshots, direct URLs, and a record of every request submitted and every outcome confirmed.'],
                    ['title' => 'Identity verification coordination', 'desc' => 'Some brokers require ID verification to process removal. We walk you through it safely.'],
                    ['title' => 'Final summary report', 'desc' => 'A clean deliverable at engagement close covering all findings, actions taken, and confirmed removals.'],
                ] as $item)
                <div class="flex gap-4 p-5 rounded-xl bg-white/[0.03] border border-white/[0.06]">
                    <div class="mt-0.5 w-5 h-5 rounded-full bg-cyan-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-3 h-3 text-cyan-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-white text-sm font-medium mb-1">{{ $item['title'] }}</p>
                        <p class="text-gray-500 text-xs leading-relaxed">{{ $item['desc'] }}</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ─── PRICING ─────────────────────────────────────────────── --}}
    <section id="pricing" class="py-20 border-t border-white/5">
        <div class="max-w-6xl mx-auto px-6">
            <div class="text-center mb-16">
                <p class="text-cyan-400 text-sm font-medium uppercase tracking-widest mb-3">Pricing</p>
                <h2 class="text-3xl sm:text-4xl font-bold">Monthly plans, cancel anytime</h2>
            </div>

            <div class="grid md:grid-cols-3 gap-6 max-w-5xl mx-auto">

                {{-- Basic --}}
                <div class="p-8 rounded-2xl bg-white/[0.03] border border-white/[0.08]">
                    <p class="text-gray-400 text-sm font-medium mb-2">Basic</p>
                    <div class="flex items-end gap-2 mb-6">
                        <span class="text-5xl font-bold text-white">$300</span>
                        <span class="text-gray-500 mb-2">/mo</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        @foreach([
                            '18 top data brokers',
                            'Monthly scans',
                            'Web form opt-outs',
                            'Email support',
                        ] as $feat)
                        <li class="flex items-center gap-3 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-cyan-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            {{ $feat }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('saasproductmodule.billing.plans') }}" class="block text-center w-full py-3 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 hover:border-white/20 text-white font-semibold text-sm transition-all">
                        Get Started
                    </a>
                </div>

                {{-- Professional --}}
                <div class="p-8 rounded-2xl border border-cyan-500/40 bg-cyan-500/5 relative overflow-hidden">
                    <div class="absolute top-4 right-4 px-2 py-0.5 rounded-full bg-cyan-500/20 border border-cyan-500/30 text-cyan-300 text-xs font-medium">
                        Most Popular
                    </div>
                    <p class="text-gray-400 text-sm font-medium mb-2">Professional</p>
                    <div class="flex items-end gap-2 mb-6">
                        <span class="text-5xl font-bold text-white">$750</span>
                        <span class="text-gray-500 mb-2">/mo</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        @foreach([
                            '24 data brokers',
                            'Weekly scans',
                            'Email opt-outs included',
                            'Priority support',
                        ] as $feat)
                        <li class="flex items-center gap-3 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-cyan-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            {{ $feat }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('saasproductmodule.billing.plans') }}" class="block text-center w-full py-3 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-white font-semibold text-sm transition-all">
                        Get Started
                    </a>
                </div>

                {{-- Executive --}}
                <div class="p-8 rounded-2xl border border-violet-500/30 bg-violet-500/5 relative overflow-hidden">
                    <p class="text-gray-400 text-sm font-medium mb-2">Executive</p>
                    <div class="flex items-end gap-2 mb-6">
                        <span class="text-5xl font-bold text-white">$1,500</span>
                        <span class="text-gray-500 mb-2">/mo</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        @foreach([
                            'All 25 brokers',
                            'Continuous monitoring',
                            'Manual removals',
                            'Dedicated specialist',
                        ] as $feat)
                        <li class="flex items-center gap-3 text-sm text-gray-300">
                            <svg class="w-4 h-4 text-violet-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            {{ $feat }}
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('saasproductmodule.billing.plans') }}" class="block text-center w-full py-3 rounded-xl border border-violet-500/40 hover:border-violet-400 text-violet-300 hover:text-violet-200 font-semibold text-sm transition-all">
                        Get Started
                    </a>
                </div>

            </div>

            <p class="text-center text-gray-600 text-xs mt-8">
                Cancel anytime &nbsp;·&nbsp; Billed monthly via Stripe
            </p>
        </div>
    </section>

    {{-- ─── FAQ ─────────────────────────────────────────────────── --}}
    <section id="faq" class="py-20 border-t border-white/5" x-data="{}">
        <div class="max-w-3xl mx-auto px-6">
            <div class="text-center mb-16">
                <p class="text-cyan-400 text-sm font-medium uppercase tracking-widest mb-3">FAQ</p>
                <h2 class="text-3xl sm:text-4xl font-bold">Honest answers</h2>
            </div>

            <div class="space-y-0">
                @foreach([
                    [
                        'q' => 'Can you guarantee my data will be removed?',
                        'a' => "No — and we won't pretend otherwise. Data brokers are independent third-party platforms. Each has its own removal process, compliance posture, and timeline. Some respond within days; others ignore requests or require multiple follow-ups; a few are nearly impossible to move. What we guarantee is that a real person is actively pursuing every removal, documenting every attempt, and following up until the engagement closes — or until you want to continue."
                    ],
                    [
                        'q' => 'What happens if my data reappears after removal?',
                        'a' => "It happens — brokers continuously re-scrape their source databases, so removed listings can resurface. Within your 4-week engagement, if a listing comes back we go after it again. Each listing gets up to 3 removal attempts included in your engagement."
                    ],
                    [
                        'q' => 'How long does an engagement take?',
                        'a' => "Most engagements run 2–4 weeks. Broker response times vary — some process removal requests in days, others take 30+ days. We stay active throughout and keep you updated. Your engagement runs for a full 4 weeks regardless."
                    ],
                    [
                        'q' => "What does the \$1,500 cover?",
                        'a' => "A full 4-week personal engagement for one subject. That includes a dedicated point of contact, up to 5 removal actions across multiple broker sites, up to 3 attempts per listing, identity verification coordination where required, and a final documented report. This isn't a software tool or automated system — it's a person doing the work on your behalf."
                    ],
                    [
                        'q' => 'Can I continue after the initial engagement?',
                        'a' => "Yes. Clients who want ongoing coverage can set up an annual retainer. Retainer pricing starts at $1,500/year for continued monitoring and re-removal, scaling up to $10,000 for specialized reputation management projects with broader scope. We'll scope it to your situation."
                    ],
                ] as $i => $faq)
                <div class="faq-border" x-data="{ open: false }">
                    <button
                        @click="open = !open"
                        class="w-full flex items-center justify-between py-5 text-left group"
                    >
                        <span class="text-white font-medium group-hover:text-cyan-300 transition-colors pr-4">{{ $faq['q'] }}</span>
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

    {{-- ─── INTAKE FORM ─────────────────────────────────────────── --}}
    <section id="intake" class="py-20 border-t border-white/5">
        <div class="max-w-2xl mx-auto px-6">
            <div class="text-center mb-10">
                <p class="text-cyan-400 text-sm font-medium uppercase tracking-widest mb-3">Questions?</p>
                <h2 class="text-3xl sm:text-4xl font-bold mb-4">Not sure where to start?</h2>
                <p class="text-gray-400 text-sm">Send us a message and we'll reach out within one business day. Or, if you're ready, <a href="{{ route('saasproductmodule.billing.plans') }}" class="text-cyan-400 hover:text-cyan-300 underline underline-offset-2">go straight to plans</a>.</p>
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
                            <p class="font-semibold text-green-400 mb-1">Request received</p>
                            <p class="text-green-300 text-sm">{{ session('success') }}</p>
                            <p class="mt-2 text-green-600 text-xs">Check your inbox — a confirmation has been sent to your email address.</p>
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

                <form action="{{ route('saas-product-module.intake') }}" method="POST" class="space-y-5">
                    @csrf

                    <div class="grid sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-2 uppercase tracking-wide">Your Name <span class="text-red-400">*</span></label>
                            <input type="text" name="name" required value="{{ old('name') }}"
                                class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30 text-white placeholder-gray-600 text-sm outline-none transition-all"
                                placeholder="Jane Smith">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-400 mb-2 uppercase tracking-wide">Email Address <span class="text-red-400">*</span></label>
                            <input type="email" name="email" required value="{{ old('email') }}"
                                class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30 text-white placeholder-gray-600 text-sm outline-none transition-all"
                                placeholder="jane@example.com">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-2 uppercase tracking-wide">Phone Number</label>
                        <input type="tel" name="phone" value="{{ old('phone') }}"
                            class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30 text-white placeholder-gray-600 text-sm outline-none transition-all"
                            placeholder="+1 (555) 000-0000">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-2 uppercase tracking-wide">What are you looking to protect?</label>
                        <textarea name="message" rows="4"
                            class="w-full px-4 py-3 rounded-xl bg-white/5 border border-white/10 focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30 text-white placeholder-gray-600 text-sm outline-none transition-all resize-none"
                            placeholder="Briefly describe what you've found or what you're concerned about — home address, phone number, family members, etc.">{{ old('message') }}</textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-400 mb-2 uppercase tracking-wide">Engagement type</label>
                        <select name="engagement_type"
                            class="w-full px-4 py-3 rounded-xl bg-[#0a1628] border border-white/10 focus:border-cyan-500/50 focus:ring-1 focus:ring-cyan-500/30 text-gray-300 text-sm outline-none transition-all">
                            <option value="standard">Standard Engagement — $1,500</option>
                            <option value="retainer">Annual Retainer — Let's talk</option>
                            <option value="unsure">Not sure yet</option>
                        </select>
                    </div>

                    <button type="submit"
                        class="w-full py-4 rounded-xl bg-cyan-500 hover:bg-cyan-400 text-white font-semibold text-sm transition-all shadow-lg shadow-cyan-500/20 hover:shadow-cyan-500/30 hover:-translate-y-0.5">
                        Submit — We'll be in touch within 1 business day
                    </button>

                    <p class="text-center text-gray-600 text-xs">
                        All information is kept strictly confidential and will never be shared with any third party.
                    </p>
                </form>
                @endif
            </div>
        </div>
    </section>

    {{-- ─── FOOTER ──────────────────────────────────────────────── --}}
    <footer class="border-t border-white/5 py-10">
        <div class="max-w-6xl mx-auto px-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-6 h-6 rounded-md bg-gradient-to-br from-cyan-500 to-violet-500 flex items-center justify-center">
                    <svg class="w-3.5 h-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.955 11.955 0 003 10c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.286z" />
                    </svg>
                </div>
                <span class="text-gray-400 text-sm">SaaS Product Module by <span class="text-white">L7 Media Labs</span></span>
            </div>
            <div class="flex items-center gap-6">
                <a href="mailto:chat@l7medialabs.com" class="text-gray-500 hover:text-gray-300 text-sm transition-colors">chat@l7medialabs.com</a>
                <a href="{{ route('saasproductmodule.billing.plans') }}" class="text-gray-500 hover:text-gray-300 text-sm transition-colors">Get Started</a>
            </div>
            <p class="text-gray-700 text-xs">© {{ date('Y') }} L7 Labs, LLC. All rights reserved.</p>
        </div>
    </footer>

</body>
</html>
