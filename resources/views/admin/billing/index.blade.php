@extends('layouts.admin')

@section('content')
<div class="min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-green-600 via-emerald-600 to-teal-600 rounded-2xl p-8 mb-8 shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2 flex items-center gap-3">
                    <i class="fas fa-file-invoice-dollar"></i>
                    Billing & Invoices
                </h1>
                <p class="text-green-100 text-lg">Manage invoices and payment processing</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.billing.create') }}" class="bg-white hover:bg-green-50 text-green-700 rounded-xl px-6 py-3 transition-all font-semibold shadow-lg hover:shadow-xl border-2 border-white/50">
                    <i class="fas fa-plus mr-2"></i>Create Invoice
                </a>
                <a href="{{ route('admin.dashboard') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white rounded-xl px-6 py-3 transition-all font-medium border border-white/30">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Debug Panel -->
    @if(config('app.debug'))
    <div class="mb-6 bg-yellow-900/30 border-2 border-yellow-500 rounded-xl p-6">
        <h3 class="text-yellow-300 font-bold text-lg mb-4 flex items-center gap-2">
            <i class="fas fa-bug"></i>
            Billing Debug Info
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="bg-gray-900/50 rounded-lg p-4">
                <div class="text-yellow-200 font-semibold mb-2">Invoice Stats</div>
                <div class="space-y-1 text-yellow-100">
                    <div>Total: <span class="font-mono">{{ \App\Models\Invoice::count() }}</span></div>
                    <div>Paid: <span class="font-mono text-green-400">{{ \App\Models\Invoice::where('status', 'paid')->count() }}</span></div>
                    <div>Unpaid: <span class="font-mono text-red-400">{{ \App\Models\Invoice::where('status', 'unpaid')->count() }}</span></div>
                    <div>Sent: <span class="font-mono text-blue-400">{{ \App\Models\Invoice::where('status', 'sent')->count() }}</span></div>
                </div>
            </div>
            
            <div class="bg-gray-900/50 rounded-lg p-4">
                <div class="text-yellow-200 font-semibold mb-2">Revenue Stats</div>
                <div class="space-y-1 text-yellow-100">
                    @php
                        $totalRevenue = \App\Models\Invoice::where('status', 'paid')->sum('total_amount');
                        $pendingRevenue = \App\Models\Invoice::whereIn('status', ['sent', 'unpaid'])->sum('total_amount');
                    @endphp
                    <div>Paid: <span class="font-mono text-green-400">${{ number_format($totalRevenue / 100, 2) }}</span></div>
                    <div>Pending: <span class="font-mono text-yellow-400">${{ number_format($pendingRevenue / 100, 2) }}</span></div>
                    <div>This Month: <span class="font-mono">${{ number_format(\App\Models\Invoice::where('status', 'paid')->whereMonth('created_at', now()->month)->sum('total_amount') / 100, 2) }}</span></div>
                </div>
            </div>
        </div>
        
        <div class="mt-4 pt-4 border-t border-yellow-700">
            <div class="flex items-center gap-4">
                <span class="text-yellow-300 text-xs">Debug mode enabled in .env</span>
            </div>
        </div>
    </div>
    @endif

    <!-- Stripe Payment Integration Panel -->
    <div class="mb-8">
        <div class="bg-gray-800/50 rounded-xl p-6 border border-gray-700">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-semibold text-white flex items-center gap-3">
                    <svg class="w-8 h-8" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-2.618 0-5.357-1.159-7.41-2.273l-.928 5.555C4.864 22.73 7.545 24 10.717 24c2.554 0 4.664-.705 6.104-2.029 1.516-1.391 2.287-3.325 2.287-5.754 0-3.944-2.577-5.732-5.132-7.062z" fill="#635BFF"/>
                    </svg>
                    <span>Stripe Payments</span>
                </h3>
                @if($stripeStatus['success'])
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-green-900/40 text-green-300 ring-1 ring-green-800">
                        <i class="fas fa-check-circle mr-2"></i>Connected
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full bg-red-900/40 text-red-300 ring-1 ring-red-800">
                        <i class="fas fa-times-circle mr-2"></i>Not Connected
                    </span>
                @endif
            </div>

            @if($stripeStatus['success'])
                <div class="space-y-3">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-400">Status</span>
                        <span class="text-green-300 font-medium">Active</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-400">Invoices with Payment Links</span>
                        <span class="text-gray-200 font-medium">{{ \App\Models\Invoice::whereNotNull('stripe_payment_url')->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-400">Stripe Customers</span>
                        <span class="text-gray-200 font-medium">{{ \App\Models\Client::whereNotNull('stripe_customer_id')->count() }}</span>
                    </div>
                    
                    <div class="pt-3 border-t border-gray-700">
                        <a href="{{ url('/pay') }}" target="_blank" class="inline-flex items-center gap-2 text-blue-400 hover:text-blue-300 text-sm font-medium">
                            <i class="fas fa-external-link-alt"></i>
                            Quick Payment Collection
                        </a>
                    </div>
                </div>
            @else
                <div class="text-gray-400 text-sm mb-3">
                    Configure Stripe to accept payments directly through invoices
                </div>
                <div class="bg-gray-900/50 rounded-lg p-3 text-xs font-mono text-gray-500">
                    <div>STRIPE_PUBLIC_KEY=...</div>
                    <div>STRIPE_SECRET_KEY=...</div>
                </div>
            @endif
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold text-gray-100">All Invoices</h2>
        <a href="{{ route('admin.billing.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
            <i class="fas fa-plus mr-2"></i>Create Invoice
        </a>
    </div>
    @if($invoices->count() > 0)

    <div class="overflow-x-auto rounded-lg border border-gray-800 bg-gray-900/50">
        <table class="min-w-full divide-y divide-gray-800">
            <thead class="bg-gray-800/60">
                <tr>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">#</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">Client</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">Date</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">Status</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">Payment</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">Total</th>
                    <th scope="col" class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @foreach($invoices as $invoice)
                    <tr class="hover:bg-gray-800/40">
                        <td class="px-4 py-3 text-gray-300">{{ $invoice->id }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('admin.billing.show', $invoice->user) }}" class="text-blue-400 hover:text-blue-300">
                                {{ $invoice->user->name }}
                            </a>
                        </td>
                        <td class="px-4 py-3 text-gray-300">{{ $invoice->created_at->format('Y-m-d') }}</td>
                        <td class="px-4 py-3">
                            @php
                                $badge = match($invoice->status) {
                                    'paid' => 'bg-green-900/40 text-green-300 ring-1 ring-green-800',
                                    'sent' => 'bg-blue-900/40 text-blue-300 ring-1 ring-blue-800',
                                    default => 'bg-gray-800 text-gray-300 ring-1 ring-gray-700',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full {{ $badge }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex flex-col gap-1">
                                @if($invoice->stripe_payment_url)
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-purple-900/40 text-purple-300 ring-1 ring-purple-800">
                                        <svg class="w-3 h-3 mr-1" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-2.618 0-5.357-1.159-7.41-2.273l-.928 5.555C4.864 22.73 7.545 24 10.717 24c2.554 0 4.664-.705 6.104-2.029 1.516-1.391 2.287-3.325 2.287-5.754 0-3.944-2.577-5.732-5.132-7.062z"/>
                                        </svg>
                                        Stripe
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full bg-gray-800 text-gray-400 ring-1 ring-gray-700">
                                        <i class="fas fa-minus-circle mr-1"></i>
                                        None
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-100">${{ number_format($invoice->total_amount / 100, 2) }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if($invoice->status !== 'paid' && $stripeStatus['success'])
                                    <!-- Stripe Payment Button -->
                                    <form method="POST" action="{{ route('admin.billing.invoices.stripe-payment-link', $invoice) }}" class="inline">
                                        @csrf
                                        <button type="submit" class="text-purple-400 hover:text-purple-300 text-xs" title="Generate Stripe Payment Link">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-2.618 0-5.357-1.159-7.41-2.273l-.928 5.555C4.864 22.73 7.545 24 10.717 24c2.554 0 4.664-.705 6.104-2.029 1.516-1.391 2.287-3.325 2.287-5.754 0-3.944-2.577-5.732-5.132-7.062z"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif

                                <!-- View Invoice -->
                                <a href="{{ route('admin.billing.show', $invoice->user) }}" class="inline-flex items-center gap-1 px-2 py-1 text-blue-400 hover:text-blue-300 hover:bg-blue-400/10 rounded text-xs transition-colors" title="View Details">
                                    <i class="fas fa-eye"></i>
                                    <span>View</span>
                                </a>

                                <!-- Copy Payment Link -->
                                @if($invoice->stripe_payment_url)
                                    <button onclick="copyToClipboard('{{ $invoice->stripe_payment_url }}')" class="inline-flex items-center gap-1 px-2 py-1 text-gray-400 hover:text-gray-300 hover:bg-gray-700 rounded text-xs transition-colors" title="Copy Payment Link">
                                        <i class="fas fa-link"></i>
                                        <span>Copy</span>
                                    </button>
                                @endif

                                <!-- Delete Invoice -->
                                @if($invoice->status !== 'paid' || !$invoice->stripe_payment_intent_id)
                                    <form method="POST" action="{{ route('admin.billing.invoices.destroy', $invoice) }}" class="inline" onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-2 py-1 text-red-400 hover:text-red-300 hover:bg-red-400/10 rounded text-xs transition-colors" title="Delete Invoice">
                                            <i class="fas fa-trash"></i>
                                            <span>Delete</span>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
        <div class="text-center text-gray-400 py-8">No invoices found.</div>
    @endif

    <div class="mt-4">
        {{ $invoices->links() }}
    </div>
</div>

</div>

<!-- Payment URL Modal -->
@if(session('payment_url'))
<div id="payment-url-modal" class="fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded-xl p-6 max-w-lg w-full mx-4 border border-gray-700 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-semibold text-white flex items-center gap-2">
                <i class="fas fa-check-circle text-green-400"></i>
                Payment Link Generated
            </h3>
            <button onclick="closeModal()" class="text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <p class="text-gray-300 mb-4">Share this link with your client to collect payment:</p>
        
        <div class="bg-gray-900 rounded-lg p-4 mb-4 flex items-center gap-3">
            <input type="text" id="payment-url-input" value="{{ session('payment_url') }}" readonly class="flex-1 bg-transparent text-blue-400 text-sm outline-none">
            <button onclick="copyPaymentUrl()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-copy mr-2"></i>Copy
            </button>
        </div>
        
        <div class="flex gap-3">
            <a href="{{ session('payment_url') }}" target="_blank" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium text-center transition-colors">
                <i class="fas fa-external-link-alt mr-2"></i>Open Link
            </a>
            <button onclick="closeModal()" class="flex-1 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm font-medium transition-colors">
                Close
            </button>
        </div>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50';
        toast.innerHTML = '<i class="fas fa-check mr-2"></i>Payment link copied!';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }).catch(err => {
        console.error('Failed to copy:', err);
        alert('Failed to copy link. Please copy manually.');
    });
}

function copyPaymentUrl() {
    const input = document.getElementById('payment-url-input');
    input.select();
    navigator.clipboard.writeText(input.value).then(() => {
        // Change button text temporarily
        const btn = event.target.closest('button');
        const originalHTML = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check mr-2"></i>Copied!';
        btn.classList.add('bg-green-600', 'hover:bg-green-700');
        btn.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        
        setTimeout(() => {
            btn.innerHTML = originalHTML;
            btn.classList.remove('bg-green-600', 'hover:bg-green-700');
            btn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }, 2000);
    });
}

function closeModal() {
    document.getElementById('payment-url-modal').remove();
}

// Close modal on escape key
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && document.getElementById('payment-url-modal')) {
        closeModal();
    }
});

// Close modal on background click
@if(session('payment_url'))
document.getElementById('payment-url-modal').addEventListener('click', (e) => {
    if (e.target.id === 'payment-url-modal') {
        closeModal();
    }
});
@endif
</script>
@endpush


