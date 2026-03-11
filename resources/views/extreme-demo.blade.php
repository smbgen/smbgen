@extends('layouts.extreme')

@section('title', 'Demo — Build a Laravel App')

@push('styles')
<style>
    .demo-glow {
        text-shadow: 0 0 24px rgba(220, 38, 38, 0.5);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-[#060d1a] py-8 px-4 sm:px-6" x-data="appGenerator()">

    {{-- Page header --}}
    <div class="max-w-7xl mx-auto mb-6">
        <div class="flex items-center gap-2 mb-3">
            <a href="{{ route('extreme') }}" class="text-red-900 hover:text-red-600 text-xs font-mono transition-colors">← extreme</a>
            <span class="text-red-900/50 text-xs">/</span>
            <span class="text-red-700/70 text-xs font-mono">demo</span>
        </div>
        <div class="flex items-center gap-3 mb-1">
            <div class="relative w-7 h-7 flex-shrink-0">
                <div class="absolute inset-0 rounded-lg bg-red-600 opacity-25 blur-sm"></div>
                <div class="relative w-7 h-7 rounded-lg bg-gradient-to-br from-red-600 to-red-900 border border-red-500/40 flex items-center justify-center shadow shadow-red-900/60">
                    <svg class="w-3.5 h-3.5 text-white" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/>
                    </svg>
                </div>
            </div>
            <h1 class="text-xl font-black uppercase tracking-wider text-white demo-glow">Laravel App Generator</h1>
        </div>
        <p class="text-gray-600 text-sm ml-10">Describe your app and watch it get built — live.</p>
    </div>

    {{-- Main grid --}}
    <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-5">

        {{-- ─── LEFT PANEL: Config ──────────────────────────────────────── --}}
        <div class="lg:w-72 xl:w-80 flex-shrink-0 space-y-4">

            {{-- Prompt box --}}
            <div class="bg-white/[0.03] border border-red-900/30 rounded-2xl p-4">
                <label class="block text-xs font-medium text-red-800 uppercase tracking-widest mb-3">Describe your app</label>

                {{-- Quick-fill examples --}}
                <div class="flex flex-wrap gap-1.5 mb-3">
                    <button @click="fillExample(0)" :disabled="phase === 'building'"
                        class="text-xs px-2.5 py-1 rounded-lg border border-red-900/40 text-red-700 hover:bg-red-900/20 hover:text-red-400 disabled:opacity-40 transition-colors">
                        Job board
                    </button>
                    <button @click="fillExample(1)" :disabled="phase === 'building'"
                        class="text-xs px-2.5 py-1 rounded-lg border border-red-900/40 text-red-700 hover:bg-red-900/20 hover:text-red-400 disabled:opacity-40 transition-colors">
                        Trainer SaaS
                    </button>
                    <button @click="fillExample(2)" :disabled="phase === 'building'"
                        class="text-xs px-2.5 py-1 rounded-lg border border-red-900/40 text-red-700 hover:bg-red-900/20 hover:text-red-400 disabled:opacity-40 transition-colors">
                        Newsletter
                    </button>
                    <button @click="fillExample(3)" :disabled="phase === 'building'"
                        class="text-xs px-2.5 py-1 rounded-lg border border-red-900/40 text-red-700 hover:bg-red-900/20 hover:text-red-400 disabled:opacity-40 transition-colors">
                        E-commerce
                    </button>
                </div>

                <textarea
                    x-model="prompt"
                    :disabled="phase === 'building'"
                    rows="5"
                    class="w-full px-3 py-2.5 rounded-xl bg-black/30 border border-white/10 focus:border-red-800/60 focus:ring-1 focus:ring-red-900/30 text-white placeholder-gray-700 text-sm outline-none transition-all disabled:opacity-50 resize-none"
                    placeholder="A client portal for a law firm — document sharing, Google Calendar booking, Stripe invoicing, and a messaging thread per client…"
                ></textarea>
                <p class="text-gray-700 text-xs mt-1.5 text-right"><span x-text="prompt.length"></span> / 500</p>
            </div>

            {{-- Options --}}
            <div class="bg-white/[0.03] border border-red-900/30 rounded-2xl p-4 space-y-4">
                <p class="text-xs font-medium text-red-800 uppercase tracking-widest">Stack options</p>

                <div>
                    <p class="text-gray-600 text-xs mb-2">Database</p>
                    <div class="space-y-1.5">
                        @foreach([['mysql','MySQL'],['pgsql','PostgreSQL'],['sqlite','SQLite']] as [$v,$l])
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="radio" value="{{ $v }}" x-model="dbChoice" :disabled="phase === 'building'" class="accent-red-600">
                            <span class="text-gray-300 text-sm group-hover:text-white transition-colors">{{ $l }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <p class="text-gray-600 text-xs mb-2">Auth scaffold</p>
                    <div class="space-y-1.5">
                        @foreach([['breeze','Laravel Breeze'],['fortify','Laravel Fortify'],['none','None']] as [$v,$l])
                        <label class="flex items-center gap-2.5 cursor-pointer group">
                            <input type="radio" value="{{ $v }}" x-model="authType" :disabled="phase === 'building'" class="accent-red-600">
                            <span class="text-gray-300 text-sm group-hover:text-white transition-colors">{{ $l }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-2 pt-2 border-t border-white/5">
                    @foreach([['stripe','Stripe billing'],['oauth','OAuth (Google)'],['multiTenant','Multi-tenancy']] as [$v,$l])
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="checkbox" x-model="{{ $v }}" :disabled="phase === 'building'" class="accent-red-600 rounded">
                        <span class="text-gray-300 text-sm group-hover:text-white transition-colors">{{ $l }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            {{-- Generate button --}}
            <button
                @click="generate()"
                :disabled="!canGenerate"
                class="w-full py-3.5 rounded-xl font-black uppercase tracking-widest text-sm transition-all bg-red-700 hover:bg-red-600 text-white disabled:opacity-40 disabled:cursor-not-allowed shadow-lg shadow-red-900/40 border border-red-600/40"
                style="text-shadow: 0 1px 4px rgba(0,0,0,0.4);"
            >
                <span x-show="phase === 'idle'" class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    Generate App
                </span>
                <span x-show="phase === 'building'" class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                    Generating…
                </span>
                <span x-show="phase === 'done'" class="flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" /></svg>
                    Generate Another
                </span>
            </button>

            {{-- Mobile done CTA --}}
            <div x-show="phase === 'done'" class="lg:hidden p-4 rounded-2xl bg-red-900/10 border border-red-900/30">
                <div class="flex items-center gap-2 mb-3">
                    <svg class="w-4 h-4 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    <span class="text-white font-semibold text-sm" x-text="appName + ' is ready'"></span>
                </div>
                <div class="flex gap-2">
                    <a href="https://github.com/smbgen/core-app" target="_blank" rel="noopener"
                       class="flex-1 flex items-center justify-center gap-1.5 py-2.5 rounded-xl bg-red-700 hover:bg-red-600 text-white font-bold text-xs uppercase tracking-wide transition-all border border-red-600/40">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                        Push to GitHub
                    </a>
                    <button @click="generate()" class="px-3 py-2.5 rounded-xl border border-white/10 text-gray-400 hover:text-white text-xs transition-colors">
                        Reset
                    </button>
                </div>
            </div>
        </div>

        {{-- ─── RIGHT PANEL: Output ──────────────────────────────────────── --}}
        <div class="flex-1 min-w-0 space-y-4">

            {{-- Progress bar --}}
            <div x-show="phase !== 'idle'" class="bg-white/[0.03] border border-white/[0.07] rounded-xl px-4 py-3">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-gray-400 font-mono" x-text="statusText || 'Initializing…'"></span>
                    <span class="text-xs font-mono" :class="progress >= 100 ? 'text-green-400' : 'text-red-500'" x-text="progress + '%'"></span>
                </div>
                <div class="h-1 rounded-full bg-white/5 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-500 ease-out"
                        :class="progress >= 100 ? 'bg-green-500 shadow-sm shadow-green-500/50' : 'bg-red-600'"
                        :style="`width: ${progress}%`">
                    </div>
                </div>
            </div>

            {{-- Terminal --}}
            <div class="bg-black/60 border border-white/[0.07] rounded-2xl overflow-hidden">
                <div class="flex items-center gap-2 px-4 py-2.5 border-b border-white/5 bg-white/[0.02]">
                    <div class="w-2.5 h-2.5 rounded-full bg-red-500/70"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-yellow-500/50"></div>
                    <div class="w-2.5 h-2.5 rounded-full bg-green-500/50"></div>
                    <span class="ml-2 text-gray-600 text-xs font-mono">extreme — build output</span>
                    <div class="ml-auto flex items-center gap-1.5" x-show="phase === 'building'">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500 animate-pulse"></span>
                        <span class="text-red-500 text-xs font-mono">live</span>
                    </div>
                    <div class="ml-auto flex items-center gap-1.5" x-show="phase === 'done'">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                        <span class="text-emerald-400 text-xs">complete</span>
                    </div>
                </div>
                <div id="build-terminal" class="h-72 overflow-y-auto p-4 font-mono text-xs leading-5 space-y-0.5 scroll-smooth">
                    <template x-if="phase === 'idle'">
                        <div class="text-gray-700 space-y-1">
                            <p>$ extreme generate</p>
                            <p>&nbsp;</p>
                            <p>Configure your app on the left, then click <span class="text-gray-500">Generate App</span>.</p>
                        </div>
                    </template>
                    <template x-for="l in lines" :key="l.id">
                        <div
                            :class="{
                                'text-green-400':        l.type === 'success',
                                'text-gray-500':         l.type === 'cmd' || l.type === 'dim',
                                'text-red-400 font-medium': l.type === 'section',
                                'text-emerald-300 font-bold':  l.type === 'complete',
                                'text-green-300':        l.type === 'test-pass',
                                'text-gray-300':         l.type === 'info',
                                'h-2 block':             l.type === 'blank',
                            }"
                            x-text="l.type !== 'blank' ? l.text : ''"
                        ></div>
                    </template>
                    <div x-show="phase === 'building'" class="text-red-700 animate-pulse">▊</div>
                </div>
            </div>

            {{-- File tree --}}
            <div class="bg-white/[0.03] border border-white/[0.07] rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between px-4 py-2.5 border-b border-white/5">
                    <div class="flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                        </svg>
                        <span class="text-gray-400 text-xs font-medium">Generated files</span>
                        <span x-show="fileCount > 0" class="text-gray-600 text-xs">(<span x-text="fileCount"></span>)</span>
                    </div>
                    <span x-show="phase === 'idle'" class="text-gray-700 text-xs font-mono">empty</span>
                </div>
                <div id="file-tree" class="h-52 overflow-y-auto p-3 font-mono text-xs space-y-0.5">
                    <template x-if="phase === 'idle'">
                        <p class="text-gray-700 p-2">Files will appear here as they are generated.</p>
                    </template>
                    <template x-for="item in treeItems" :key="item.id">
                        <div class="flex items-center gap-1.5 px-1.5 py-0.5 rounded-md transition-colors duration-700"
                            :class="item.fresh ? 'bg-red-900/20' : ''">
                            <span class="w-1.5 h-1.5 rounded-full flex-shrink-0" :class="dotColor(item.category)"></span>
                            <span class="text-gray-600" x-text="item.dir"></span><span
                                :class="item.fresh ? 'text-red-300' : 'text-gray-300'"
                                class="transition-colors duration-700"
                                x-text="item.filename"></span>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Done CTA (desktop) --}}
            <div x-show="phase === 'done'" class="hidden lg:block p-5 rounded-2xl bg-gradient-to-r from-red-900/10 to-red-800/5 border border-red-900/30">
                <div class="flex items-start justify-between gap-6">
                    <div class="min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4 text-green-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                            <h3 class="text-white font-semibold text-sm" x-text="appName + ' generated successfully'"></h3>
                        </div>
                        <p class="text-gray-500 text-xs mb-4">
                            <span x-text="fileCount"></span> files &nbsp;·&nbsp;
                            <span x-text="testsPassed"></span> Pest tests &nbsp;·&nbsp;
                            <span x-text="dbChoice.toUpperCase()"></span> &nbsp;·&nbsp; ready to deploy
                        </p>
                        <div class="flex flex-wrap gap-2">
                            <button disabled title="Full generation coming soon"
                                class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-white/10 text-gray-600 text-xs cursor-not-allowed">
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" /></svg>
                                Download ZIP
                                <span class="text-gray-700 ml-0.5">· soon</span>
                            </button>
                            <a href="https://forge.laravel.com/" target="_blank" rel="noopener"
                                class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-white/10 text-gray-400 hover:text-white hover:border-white/20 text-xs transition-colors">
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" /></svg>
                                Deploy to Forge
                            </a>
                            <a href="https://github.com/smbgen/core-app" target="_blank" rel="noopener"
                                class="flex items-center gap-1.5 px-3.5 py-2 rounded-lg border border-red-700/50 bg-red-900/20 text-red-300 hover:bg-red-900/40 hover:text-white text-xs font-medium transition-colors">
                                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="currentColor"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                                Push to GitHub
                            </a>
                        </div>
                    </div>
                    <div class="flex-shrink-0 text-right">
                        <p class="text-gray-600 text-xs font-mono mb-2">Generate another?</p>
                        <button @click="generate()" class="text-red-500 hover:text-red-400 text-xs underline underline-offset-2 transition-colors">
                            Reset →
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('appGenerator', () => ({
        prompt: '',
        dbChoice: 'mysql',
        authType: 'breeze',
        stripe: true,
        oauth: false,
        multiTenant: false,

        phase: 'idle',
        lines: [],
        treeItems: [],
        progress: 0,
        statusText: '',
        fileCount: 0,
        testsPassed: 0,

        examples: [
            'A job board where employers post listings and candidates apply. Employers can review applications and a queue sends email notifications on events. Include an admin dashboard.',
            'A multi-tenant SaaS for fitness trainers. Each trainer gets their own subdomain, clients book sessions via Google Calendar, and Stripe handles monthly subscriptions.',
            'A subscription newsletter platform. Writers publish posts and readers subscribe via Stripe. Paid posts are gated behind subscription tiers. Dark mode, mobile-first.',
            'An e-commerce store with a product catalogue, shopping cart, Stripe Checkout, order management, email receipts, and an admin panel to manage stock.',
        ],

        get appName() {
            const words = this.prompt.trim().split(/\s+/)
                .map(w => w.replace(/[^a-zA-Z]/g, ''))
                .filter(w => w.length > 2)
                .slice(0, 2)
                .map(w => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase());
            return words.join('') || 'MyApp';
        },

        get canGenerate() {
            return this.prompt.trim().length > 10 && this.phase !== 'building';
        },

        fillExample(idx) {
            if (this.phase === 'building') return;
            this.prompt = this.examples[idx] || '';
            const presets = [
                { stripe: false, oauth: false, multiTenant: false },
                { stripe: true,  oauth: true,  multiTenant: true  },
                { stripe: true,  oauth: false, multiTenant: false },
                { stripe: true,  oauth: false, multiTenant: false },
            ];
            Object.assign(this, presets[idx] || {});
        },

        async generate() {
            if (!this.canGenerate) return;
            if (this.phase === 'done') { this.reset(); return; }
            this.phase = 'building';
            this.lines = [];
            this.treeItems = [];
            this.progress = 0;
            this.fileCount = 0;
            this.testsPassed = 0;
            await this.runSequence();
            this.phase = 'done';
        },

        reset() {
            this.phase = 'idle';
            this.lines = [];
            this.treeItems = [];
            this.progress = 0;
            this.statusText = '';
            this.fileCount = 0;
            this.testsPassed = 0;
        },

        async runSequence() {
            const p = this.prompt.toLowerCase();

            const hasBooking  = /book|schedul|appointment|calendar/.test(p);
            const hasStripe   = this.stripe || /stripe|subscri|billing|payment|plan/.test(p);
            const hasBlog     = /blog|post|article|publish|newsletter/.test(p);
            const hasEcom     = /shop|product|cart|order|ecommerce|store/.test(p);
            const hasMulti    = this.multiTenant || /tenant|saas|subdomain/.test(p);
            const hasJobs     = /job.board|job listing|candidate|employer|applicant/.test(p);
            const hasOAuth    = this.oauth || /google|oauth|social/.test(p);
            const authLib     = this.authType !== 'none'
                ? (this.authType === 'breeze' ? 'Laravel Breeze' : 'Laravel Fortify')
                : null;

            const models = ['User'];
            if (hasBooking)  models.push('Booking', 'Appointment');
            if (hasStripe)   models.push('Subscription', 'Plan');
            if (hasBlog)     models.push('Post', 'Category');
            if (hasEcom)     models.push('Product', 'Order', 'CartItem');
            if (hasMulti)    models.push('Tenant');
            if (hasJobs)     models.push('JobListing', 'Application');
            if (models.length < 3) models.push('Activity', 'Setting');

            const migCount   = models.length + 2;
            const compCount  = Math.max(4, Math.floor(models.length * 1.5));
            const testCount  = models.length * 4 + 8;
            this.testsPassed = testCount;

            // ── Phase 1: Analysis ──────────────────────────────────────────
            this.statusText = 'Analyzing prompt…';

            await this.ln('$ extreme generate', 'cmd', 100);
            await this.ln('', 'blank', 60);
            await this.ln('  Analyzing prompt…', 'dim', 380);
            await this.ln('  ✓ Application type: Web Application', 'success', 420);
            await this.ln(`  ✓ Entities detected: ${models.join(', ')}`, 'success', 360);

            const integrations = [];
            if (authLib)   integrations.push(authLib);
            if (hasStripe) integrations.push('Stripe Cashier');
            if (hasOAuth)  integrations.push('Laravel Socialite');
            if (hasMulti)  integrations.push('Stancl/Tenancy');
            if (integrations.length) {
                await this.ln(`  ✓ Integrations: ${integrations.join(', ')}`, 'success', 340);
            }
            await this.ln(`  ✓ Plan: ${migCount} migrations · ${compCount} Livewire components · ${testCount} Pest tests`, 'success', 360);
            await this.ln('', 'blank', 80);
            this.prog(15);

            // ── Phase 2: Database ──────────────────────────────────────────
            this.statusText = 'Generating database layer…';
            await this.ln('  Generating migrations…', 'section', 180);

            let seq = 1;
            const pad = () => String(seq++).padStart(6, '0');
            const ts  = '2026_03_11';

            for (const m of models) {
                const tbl = m.replace(/([A-Z])/g, (c, l, i) => (i ? '_' : '') + l.toLowerCase()).replace(/^_/, '') + 's';
                await this.f(`database/migrations/${ts}_${pad()}_create_${tbl}_table.php`, 'migration', 65);
            }
            await this.f(`database/migrations/${ts}_${pad()}_add_indexes.php`, 'migration', 55);
            await this.f(`database/migrations/${ts}_${pad()}_add_foreign_keys.php`, 'migration', 55);
            await this.ln(`  ✓ ${migCount} migrations written`, 'success', 100);
            this.prog(24);

            await this.ln('  Generating Eloquent models…', 'dim', 160);
            for (const m of models) { await this.f(`app/Models/${m}.php`, 'php', 50); }
            await this.ln(`  ✓ ${models.length} models with relationships & casts`, 'success', 100);
            this.prog(32);

            await this.ln('  Generating factories & seeders…', 'dim', 140);
            for (const m of models) { await this.f(`database/factories/${m}Factory.php`, 'factory', 42); }
            await this.f('database/seeders/DatabaseSeeder.php', 'seeder', 42);
            await this.ln(`  ✓ ${models.length} factories`, 'success', 80);
            await this.ln('', 'blank', 60);
            this.prog(40);

            // ── Phase 3: Application layer ─────────────────────────────────
            this.statusText = 'Scaffolding application layer…';
            await this.ln('  Generating controllers & form requests…', 'section', 160);

            for (const m of models.filter(x => x !== 'User')) {
                await this.f(`app/Http/Controllers/${m}Controller.php`, 'php', 52);
                await this.f(`app/Http/Requests/Store${m}Request.php`, 'php', 38);
                await this.f(`app/Http/Requests/Update${m}Request.php`, 'php', 38);
            }
            if (authLib) {
                await this.f('app/Http/Controllers/Auth/AuthenticatedSessionController.php', 'php', 38);
                await this.f('app/Http/Controllers/Auth/RegisteredUserController.php', 'php', 38);
                if (hasOAuth) await this.f('app/Http/Controllers/Auth/SocialiteController.php', 'php', 38);
            }
            await this.f('app/Http/Controllers/DashboardController.php', 'php', 38);
            await this.ln('  ✓ Controllers & Form Requests', 'success', 100);
            this.prog(50);

            await this.ln('  Generating policies…', 'dim', 120);
            for (const m of models.filter(x => x !== 'User')) {
                await this.f(`app/Policies/${m}Policy.php`, 'php', 38);
            }
            await this.ln('  ✓ Policies written', 'success', 80);
            await this.f('routes/web.php', 'config', 52);
            await this.f('routes/auth.php', 'config', 42);
            if (hasStripe || hasBooking) await this.f('routes/api.php', 'config', 42);
            await this.ln('  ✓ Routes registered', 'success', 80);
            await this.ln('', 'blank', 60);
            this.prog(58);

            // ── Phase 4: Frontend ──────────────────────────────────────────
            this.statusText = 'Building Livewire components…';
            await this.ln('  Building Livewire components & views…', 'section', 160);

            const comps = ['Dashboard'];
            if (hasBooking) comps.push('BookingCalendar', 'AppointmentList');
            if (hasStripe)  comps.push('SubscriptionManager', 'BillingPortal');
            if (hasBlog)    comps.push('PostEditor', 'PostList');
            if (hasEcom)    comps.push('ProductCatalogue', 'ShoppingCart', 'OrderManager');
            if (hasMulti)   comps.push('TenantSwitcher', 'TenantSettings');
            if (hasJobs)    comps.push('JobBoard', 'ApplicationTracker');
            comps.push('UserSettings', 'NotificationCenter');
            const finalComps = comps.slice(0, compCount);

            for (const comp of finalComps) {
                const blade = comp.replace(/([A-Z])/g, (c, l, i) => (i ? '-' : '') + l.toLowerCase());
                await this.f(`app/Livewire/${comp}.php`, 'livewire', 52);
                await this.f(`resources/views/livewire/${blade}.blade.php`, 'blade', 38);
            }
            await this.f('resources/views/layouts/app.blade.php', 'blade', 38);
            await this.f('resources/views/layouts/guest.blade.php', 'blade', 38);
            await this.f('resources/views/dashboard.blade.php', 'blade', 38);
            await this.f('resources/css/app.css', 'css', 38);
            await this.f('tailwind.config.js', 'config', 38);
            await this.f('vite.config.js', 'config', 38);
            await this.ln(`  ✓ ${finalComps.length} Livewire components + views`, 'success', 100);

            if (hasStripe) {
                await this.f('app/Services/BillingService.php', 'php', 52);
                await this.ln('  ✓ Stripe Cashier wired', 'success', 80);
            }
            if (hasOAuth) { await this.ln('  ✓ Google OAuth via Socialite wired', 'success', 80); }
            if (hasMulti) {
                await this.f('app/Http/Middleware/InitializeTenancy.php', 'php', 52);
                await this.ln('  ✓ Multi-tenancy + subdomain routing wired', 'success', 80);
            }
            await this.ln('', 'blank', 60);
            this.prog(76);

            // ── Phase 5: Config ────────────────────────────────────────────
            this.statusText = 'Writing config & environment…';
            await this.ln('  Writing config & environment…', 'dim', 120);
            await this.f('config/app.php', 'config', 42);
            await this.f('config/database.php', 'config', 42);
            if (hasStripe) await this.f('config/cashier.php', 'config', 42);
            await this.f('.env.example', 'config', 42);
            await this.f('composer.json', 'config', 42);
            await this.f('package.json', 'config', 42);
            await this.ln(`  ✓ Configured for ${this.dbChoice.toUpperCase()}`, 'success', 80);
            await this.ln('', 'blank', 60);
            this.prog(84);

            // ── Phase 6: Tests ─────────────────────────────────────────────
            this.statusText = 'Writing Pest test suite…';
            await this.ln('  Writing Pest test suite…', 'section', 140);
            for (const m of models.filter(x => x !== 'User')) {
                await this.f(`tests/Feature/${m}Test.php`, 'test', 50);
            }
            await this.f('tests/Feature/AuthTest.php', 'test', 45);
            await this.f('tests/Feature/DashboardTest.php', 'test', 45);
            if (hasStripe) await this.f('tests/Feature/BillingTest.php', 'test', 45);
            for (const m of models.slice(0, 3)) {
                await this.f(`tests/Unit/${m}Test.php`, 'test', 42);
            }
            await this.ln(`  ✓ ${testCount} tests written`, 'success', 100);
            this.prog(91);

            // ── Phase 7: Quality checks ────────────────────────────────────
            this.statusText = 'Running quality checks…';
            await this.ln('  Running Laravel Pint…', 'dim', 380);
            await this.ln('  ✓ 0 style violations', 'success', 520);
            await this.ln('  Running test suite…', 'dim', 320);
            await this.wait(280);

            const ftests = models.filter(x => x !== 'User').length + 2 + (hasStripe ? 1 : 0);
            const utests = Math.min(models.length, 3);
            await this.ln(`  PASS  tests/Feature/  (${ftests} tests)`, 'test-pass', 160);
            await this.wait(200);
            await this.ln(`  PASS  tests/Unit/     (${utests} tests)`, 'test-pass', 160);
            await this.wait(320);
            await this.ln(`  Tests: ${testCount} passed, 0 failed`, 'success', 140);
            await this.ln(`  Assertions: ${testCount * 4}`, 'dim', 80);
            this.prog(99);

            await this.ln('', 'blank', 220);
            await this.wait(300);
            await this.ln(`✓ ${this.appName} — ${this.fileCount} files generated · ${testCount} tests passing`, 'complete', 0);
            this.prog(100);
            this.statusText = 'Complete!';
        },

        // ── Helpers ──────────────────────────────────────────────────────────

        async ln(text, type = 'info', delay = 80) {
            if (delay > 0) await this.wait(delay);
            this.lines.push({ id: Math.random(), text, type });
            setTimeout(() => {
                const el = document.getElementById('build-terminal');
                if (el) el.scrollTop = el.scrollHeight;
            }, 16);
        },

        async f(path, category = 'php', delay = 50) {
            if (delay > 0) await this.wait(delay);
            const slash = path.lastIndexOf('/');
            const self = this;
            const idx  = this.treeItems.length;
            this.treeItems.push({
                id:       Math.random(),
                path,
                dir:      slash >= 0 ? path.substring(0, slash + 1) : '',
                filename: slash >= 0 ? path.substring(slash + 1) : path,
                category,
                fresh:    true,
            });
            this.fileCount++;
            setTimeout(() => { if (self.treeItems[idx]) self.treeItems[idx].fresh = false; }, 750);
            setTimeout(() => {
                const el = document.getElementById('file-tree');
                if (el) el.scrollTop = el.scrollHeight;
            }, 16);
        },

        prog(n) { this.progress = n; },

        wait(ms) { return new Promise(r => setTimeout(r, ms)); },

        dotColor(cat) {
            return {
                php:       'bg-blue-400',
                livewire:  'bg-cyan-400',
                migration: 'bg-yellow-400',
                factory:   'bg-orange-400',
                seeder:    'bg-orange-300',
                blade:     'bg-purple-400',
                test:      'bg-green-400',
                config:    'bg-gray-400',
                css:       'bg-pink-400',
            }[cat] ?? 'bg-gray-500';
        },
    }));
});
</script>
@endpush
