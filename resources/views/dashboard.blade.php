@extends('layouts.client')

@section('content')
<div class="space-y-8">
    <!-- Welcome Header -->
    <div class="text-center">
        <h1 class="text-3xl font-bold text-gray-100 mb-2">
            Welcome back, {{ auth()->user()->name }}!
        </h1>
        <p class="text-gray-400">
            {{ auth()->user()->email }} • Client Portal
        </p>
    </div>

    <!-- Quick Actions Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Email Card -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-100 mb-2">Your Email</h3>
            <p class="text-gray-400">{{ auth()->user()->email }}</p>
        </div>

        <!-- Profile Settings -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-100 mb-4">Profile Settings</h3>
            <a href="{{ route('profile.edit') }}" class="btn-secondary inline-block">
                Edit Profile
            </a>
        </div>

        <!-- Invoices & Billing -->
        @if(config('business.features.billing'))
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-100 mb-2">💳 Invoices</h3>
                <p class="text-gray-400 text-sm mb-4">View invoices and make payments</p>
                <a href="{{ route('billing.index') }}" class="btn-secondary inline-block">
                    View Invoices
                </a>
            </div>
        @endif

        <!-- Make a Payment -->
        <div class="card p-6">
            <h3 class="text-lg font-semibold text-gray-100 mb-2">💰 Make a Payment</h3>
            <p class="text-gray-400 text-sm mb-4">Quick payment portal</p>
            <a href="{{ route('payment.collect') }}" class="btn-success inline-block">
                Pay Now
            </a>
        </div>

        <!-- Cyber Audit -->
        @if ((bool) data_get(config('business'), 'features.cyber_audit', false))
            <div class="card p-6">
                <h3 class="text-lg font-semibold text-gray-100 mb-2">🔒 Cyber Audit</h3>
                <p class="text-gray-400 text-sm mb-4">AI-powered cybersecurity assessment</p>
                <a href="{{ route('cyber-audit.index') }}" class="btn-success inline-block">
                    Start Audit
                </a>
            </div>
        @endif

        <!-- My Documents - Unavailable -->
        @if(config('business.features.file_management'))
            <div class="card p-6 opacity-50 pointer-events-none">
                <h3 class="text-lg font-semibold text-gray-100 mb-2">📁 My Documents</h3>
                <p class="text-gray-400 text-sm mb-4">Currently unavailable</p>
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
                    <div class="border-b border-gray-700 pb-4 mb-4 last:border-b-0">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center mb-1">
                                    @if($message->sender_id === auth()->id())
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800 mr-2">Sent</span>
                                        <h4 class="font-medium text-gray-100">To: {{ $message->recipient->name }}</h4>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $message->is_read ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }} mr-2">
                                            {{ $message->is_read ? 'Read' : 'Unread' }}
                                        </span>
                                        <h4 class="font-medium text-gray-100">From: {{ $message->sender->name }}</h4>
                                    @endif
                                </div>
                                <p class="text-gray-400 text-sm mb-1">{{ $message->subject ?? 'No Subject' }}</p>
                                <p class="text-gray-400 text-sm mb-1">
                                    📅 {{ $message->created_at->format('M j, g:i A') }}
                                </p>
                                <p class="text-gray-400 text-sm truncate">{{ Str::limit($message->body, 60) }}</p>
                            </div>
                            <a href="{{ route('messages.show', $message) }}" class="btn-secondary text-xs">View</a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <p class="text-gray-400 mb-4">No recent messages.</p>
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
                    @php
                        $recentInvoices = \App\Models\Invoice::where('user_id', auth()->id())
                            ->orderBy('created_at', 'desc')
                            ->take(3)
                            ->get();
                    @endphp
                    @forelse($recentInvoices as $invoice)
                        <div class="border-b border-gray-700 pb-4 mb-4 last:border-b-0">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center mb-1">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $invoice->status === 'paid' ? 'bg-green-100 text-green-800' : ($invoice->status === 'sent' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }} mr-2">
                                            {{ ucfirst($invoice->status) }}
                                        </span>
                                        <h4 class="font-medium text-gray-100">Invoice #{{ $invoice->id }}</h4>
                                    </div>
                                    <p class="text-gray-400 text-sm mb-1">
                                        Amount: ${{ number_format($invoice->total_amount / 100, 2) }}
                                    </p>
                                    <p class="text-gray-400 text-sm mb-1">
                                        📅 {{ $invoice->created_at->format('M j, Y') }}
                                    </p>
                                    @if($invoice->memo)
                                        <p class="text-gray-400 text-sm truncate">{{ Str::limit($invoice->memo, 60) }}</p>
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
                            <p class="text-gray-400 mb-4">No invoices yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
