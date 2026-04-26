@extends('layouts.client')

@section('content')
<div class="space-y-8">
    @php
        $showSmbgenServiceMenu = \App\Support\ModuleRegistry::isEnabled('frontend_site')
            && \App\Support\ModuleRegistry::isSelectedFrontend('frontend_site');

        $serviceMenuCards = [
            [
                'key' => 'free_open_source',
                'title' => 'Free',
                'subtitle' => 'Open source AI Web Design Workbench',
                'price' => '$0 / month',
                'description' => 'Get download links and access to the free and open source smbgen toolkit.',
                'enter_url' => 'https://github.com/smbgen',
                'enter_label' => 'Open Downloads',
            ],
            [
                'key' => 'consulting',
                'title' => 'Free + Consulting',
                'subtitle' => 'Guided setup and strategy support',
                'price' => '$0 + consulting',
                'description' => 'Engage our team for onboarding, architecture, and implementation planning.',
                'enter_url' => Route::has('booking.wizard') ? route('booking.wizard') : route('contact'),
                'enter_label' => 'Book Consulting',
            ],
            [
                'key' => 'smb_core',
                'title' => 'smb Core',
                'subtitle' => 'Essential business automation',
                'price' => '$50 / month',
                'description' => 'Start with core workflows for leads, booking, payments, and client operations.',
                'enter_url' => route('contact', ['plan' => 'smb_core']),
                'enter_label' => 'Enter smb Core',
            ],
            [
                'key' => 'smb_pro',
                'title' => 'smb Pro',
                'subtitle' => 'Growth-focused automation and insights',
                'price' => '$100 / month',
                'description' => 'Add deeper growth tooling, integrations, and optimization workflows.',
                'enter_url' => route('contact', ['plan' => 'smb_pro']),
                'enter_label' => 'Enter smb Pro',
            ],
            [
                'key' => 'smb_max',
                'title' => 'smb Max',
                'subtitle' => 'Full platform engagement',
                'price' => '$200 / month',
                'description' => 'Unlock the complete smbgen service stack with the highest support level.',
                'enter_url' => route('contact', ['plan' => 'smb_max']),
                'enter_label' => 'Enter smb Max',
            ],
        ];

        $tierOptions = [
            ['key' => 'free', 'label' => 'Free', 'price' => '$0 / month', 'summary' => 'Open source downloads and starter toolkit access.'],
            ['key' => 'free_consulting', 'label' => 'Free + Consulting', 'price' => '$0 + consulting', 'summary' => 'Free tools plus guided planning and expert help.'],
            ['key' => 'smb_core', 'label' => 'smb Core', 'price' => '$50 / month', 'summary' => 'Essential workflows for growing SMB operations.'],
            ['key' => 'smb_pro', 'label' => 'smb Pro', 'price' => '$100 / month', 'summary' => 'Advanced growth and automation capabilities.'],
            ['key' => 'smb_max', 'label' => 'smb Max', 'price' => '$200 / month', 'summary' => 'Full platform with highest level of engagement.'],
        ];

        $selectedTier = auth()->user()->account_tier ?? 'free';
        $enabledServices = collect(auth()->user()->enabled_services ?? [])->values()->all();
    @endphp

    <!-- Welcome Header -->
    <div class="text-center">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">
            Welcome back, {{ auth()->user()->name }}!
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            {{ auth()->user()->email }} • Client Portal
        </p>
    </div>

    @if($workspace)
        @php
            $workspaceStatusLabels = [
                'not_started' => 'Domain Setup Not Started',
                'pending_dns' => 'Domain Pending DNS',
                'verified' => 'Domain Verified',
                'using_subdomain' => 'Using Platform Subdomain',
            ];

            $workspaceStatusClasses = [
                'not_started' => 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300',
                'pending_dns' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
                'verified' => 'bg-green-100 text-green-800 dark:bg-green-900/40 dark:text-green-300',
                'using_subdomain' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
            ];

            $workspaceStatus = $workspace['domainStatus'] ?? 'not_started';
            $workspaceHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';
        @endphp

        <div class="card p-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100">Workspace Overview</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">You are active in {{ $workspace['name'] }}.</p>
                </div>
                <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ $workspaceStatusClasses[$workspaceStatus] ?? $workspaceStatusClasses['not_started'] }}">
                    {{ $workspaceStatusLabels[$workspaceStatus] ?? $workspaceStatusLabels['not_started'] }}
                </span>
            </div>

            <div class="mt-5 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Workspace Domain</p>
                    <p class="mt-2 font-mono text-gray-900 dark:text-gray-100">{{ $workspace['subdomain'] }}.{{ $workspaceHost }}</p>
                </div>

                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Custom Domain</p>
                    <p class="mt-2 font-mono text-gray-900 dark:text-gray-100">{{ $workspace['customDomain'] ?: 'Not configured yet' }}</p>
                </div>

                <div class="rounded-xl border border-gray-200 dark:border-gray-700 p-4">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Unread Messages</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $unreadCount }}</p>
                </div>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Unread Messages</p>
            <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $unreadCount }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Services Enabled</p>
            <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ count($enabledServices) }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Current Tier</p>
            <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ collect($tierOptions)->firstWhere('key', $selectedTier)['label'] ?? 'Free' }}</p>
        </div>
        <div class="card p-5">
            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Recent Messages</p>
            <p class="mt-2 text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $messages->count() }}</p>
        </div>
    </div>

    <!-- Service Menu and Tier Selection -->
    @if($showSmbgenServiceMenu)
    <details class="card p-6 md:p-8">
        <summary class="cursor-pointer list-none">
            <div class="flex items-center justify-between gap-4">
                <div class="flex flex-col gap-2">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">SMBGEN Service Menu</h2>
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Toggle services and choose the account tier that fits your business.
                    </p>
                </div>
                <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-300">Expand</span>
            </div>
        </summary>

        <div class="mt-6">
            @if (session('status'))
                <div class="mb-6 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm font-medium text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('portal.service-menu.update') }}" class="space-y-8">
                @csrf
                @method('PATCH')

            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Engage Services</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($serviceMenuCards as $card)
                        @php
                            $enabled = in_array($card['key'], $enabledServices, true);
                        @endphp
                        <div class="rounded-2xl border {{ $enabled ? 'border-indigo-300 bg-indigo-50/70 dark:border-indigo-700 dark:bg-indigo-900/20' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900' }} p-5 flex flex-col gap-4">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-wider text-indigo-600 dark:text-indigo-300">{{ $card['price'] }}</p>
                                <h4 class="mt-1 text-lg font-bold text-gray-900 dark:text-gray-100">{{ $card['title'] }}</h4>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $card['subtitle'] }}</p>
                            </div>

                            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">{{ $card['description'] }}</p>

                            <div class="mt-auto flex flex-col gap-3">
                                <label class="inline-flex items-center gap-3 text-sm font-medium text-gray-700 dark:text-gray-200">
                                    <input
                                        type="checkbox"
                                        name="enabled_services[]"
                                        value="{{ $card['key'] }}"
                                        @checked($enabled)
                                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                    >
                                    Turn on this service
                                </label>

                                <a href="{{ $card['enter_url'] }}"
                                   @if(str_starts_with($card['enter_url'], 'http')) target="_blank" rel="noreferrer" @endif
                                   class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-3 py-2 text-sm font-semibold text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800">
                                    {{ $card['enter_label'] }}
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
                @error('enabled_services.*')
                    <p class="mt-3 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Choose Your Tier</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-3">
                    @foreach($tierOptions as $tier)
                        <label class="rounded-xl border p-4 cursor-pointer transition {{ $selectedTier === $tier['key'] ? 'border-indigo-400 bg-indigo-50 dark:border-indigo-700 dark:bg-indigo-900/20' : 'border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900 hover:border-gray-300 dark:hover:border-gray-600' }}">
                            <input type="radio" name="account_tier" value="{{ $tier['key'] }}" class="sr-only" @checked($selectedTier === $tier['key'])>
                            <p class="text-sm font-bold text-gray-900 dark:text-gray-100">{{ $tier['label'] }}</p>
                            <p class="text-xs font-semibold text-indigo-600 dark:text-indigo-300 mt-1">{{ $tier['price'] }}</p>
                            <p class="mt-2 text-xs text-gray-600 dark:text-gray-300">{{ $tier['summary'] }}</p>
                        </label>
                    @endforeach
                </div>
                @error('account_tier')
                    <p class="mt-3 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

                <div class="flex flex-wrap items-center gap-3">
                    <button type="submit" class="btn-success">
                        Save Service Menu
                    </button>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Anyone with a smbgen login can view this menu and choose how they want to engage.
                    </p>
                </div>
            </form>
        </div>
    </details>
    @endif

    <!-- Quick Actions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Email Card -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Your Email</h3>
            <p class="text-gray-600 dark:text-gray-400">{{ auth()->user()->email }}</p>
        </div>

        <!-- Profile Settings -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Profile Settings</h3>
            <a href="{{ route('profile.edit') }}" class="btn-secondary inline-block">
                Edit Profile
            </a>
        </div>

        <!-- Billing Actions -->
        @if(config('business.features.billing'))
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">💳 Billing</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">View invoices, pay outstanding balances, and manage billing.</p>
                <div class="flex flex-col sm:flex-row sm:items-center sm:gap-3">
                    <a href="{{ route('billing.index') }}" class="btn-secondary inline-flex justify-center w-full sm:w-auto">
                        View Invoices
                    </a>
                    <a href="{{ route('payment.collect') }}" class="btn-success inline-flex justify-center w-full sm:w-auto">
                        Pay Now
                    </a>
                </div>
            </div>
        @endif

        <!-- Cyber Audit -->
        @if ((bool) data_get(config('business'), 'features.cyber_audit', false))
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">🔒 Cyber Audit</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">AI-powered cybersecurity assessment</p>
                <a href="{{ route('cyber-audit.index') }}" class="btn-success inline-block">
                    Start Audit
                </a>
            </div>
        @endif

        <!-- My Documents - Unavailable -->
        @if(config('business.features.file_management'))
            <div class="card p-6 opacity-50 pointer-events-none">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">📁 My Documents</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4">Currently unavailable</p>
                <button disabled class="btn-secondary inline-block cursor-not-allowed">
                    Open Documents
                </button>
            </div>
        @endif
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Messages -->
        <div class="card">
            <div class="bg-green-600 text-white px-6 py-4 rounded-t-lg flex justify-between items-center">
                <h3 class="text-lg font-semibold">📬 Recent Messages</h3>
                <a href="{{ route('messages.index') }}" class="text-white hover:text-green-200 text-sm">
                    View All
                </a>
            </div>
            <div class="p-6">
                @forelse($messages as $message)
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4 last:border-b-0">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center mb-1">
                                    @if($message->sender_id === auth()->id())
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mr-2">Sent</span>
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">To: {{ $message->recipient->name }}</h4>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $message->is_read ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} mr-2">
                                            {{ $message->is_read ? 'Read' : 'Unread' }}
                                        </span>
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">From: {{ $message->sender->name }}</h4>
                                    @endif
                                </div>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">{{ $message->subject ?? 'No Subject' }}</p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">
                                    📅 {{ $message->created_at->format('M j, g:i A') }}
                                </p>
                                <p class="text-gray-600 dark:text-gray-400 text-sm truncate">{{ Str::limit($message->body, 60) }}</p>
                            </div>
                            <a href="{{ route('messages.show', $message) }}" class="btn-secondary text-xs">View</a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-600 dark:text-gray-400 mb-4">No recent messages.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Recent Invoices -->
        @if(config('business.features.billing'))
            <div class="card">
                <div class="bg-blue-600 text-white px-6 py-4 rounded-t-lg flex justify-between items-center">
                    <h3 class="text-lg font-semibold">💳 Recent Invoices</h3>
                    <a href="{{ route('billing.index') }}" class="text-white hover:text-blue-200 text-sm">
                        View All
                    </a>
                </div>
                <div class="p-6">
                    @forelse($recentInvoices as $invoice)
                        <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4 last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center mb-1">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->status === 'sent' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }} mr-2">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">Invoice #{{ $invoice->id }}</h4>
                                    </div>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">
                                        Amount: ${{ number_format($invoice->total_amount / 100, 2) }}
                                    </p>
                                    <p class="text-gray-600 dark:text-gray-400 text-sm mb-1">
                                        📅 {{ $invoice->created_at->format('M j, Y') }}
                                    </p>
                                    @if($invoice->memo)
                                        <p class="text-gray-600 dark:text-gray-400 text-sm truncate">{{ Str::limit($invoice->memo, 60) }}</p>
                                    @endif
                                </div>
                                @if($invoice->status !== 'paid')
                                    <form action="{{ route('billing.pay', $invoice) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn-success text-xs">Pay Now</button>
                                    </form>
                                @else
                                    <span class="text-green-400 text-xs">✓ Paid</span>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <p class="text-gray-600 dark:text-gray-400 mb-4">No invoices yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
