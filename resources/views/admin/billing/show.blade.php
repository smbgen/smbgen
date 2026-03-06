@extends('layouts.admin')

@section('content')
<div class="min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-2xl p-8 mb-8 shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2 flex items-center gap-3">
                    <i class="fas fa-user-circle"></i>
                    {{ $user->name }}
                </h1>
                <p class="text-blue-100 text-lg">Client Invoices & Payment History</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.billing.create', $user) }}" class="bg-white hover:bg-blue-50 text-blue-700 rounded-xl px-6 py-3 transition-all font-semibold shadow-lg hover:shadow-xl border-2 border-white/50">
                    <i class="fas fa-plus mr-2"></i>New Invoice
                </a>
                <a href="{{ route('admin.billing.index') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white rounded-xl px-6 py-3 transition-all font-medium border border-white/30">
                    <i class="fas fa-arrow-left mr-2"></i>All Invoices
                </a>
            </div>
        </div>
    </div>

    <!-- Invoices Grid -->
    <div class="space-y-4">
        @forelse($invoices as $invoice)
            <div class="bg-gray-800/50 rounded-xl border border-gray-700 hover:border-gray-600 transition-all shadow-lg hover:shadow-xl">
                <div class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex-1">
                            <div class="flex items-center gap-4 mb-2">
                                <h3 class="text-2xl font-bold text-white">Invoice #{{ $invoice->id }}</h3>
                                @php
                                    $badge = match($invoice->status) {
                                        'paid' => 'bg-green-900/40 text-green-300 ring-1 ring-green-800',
                                        'sent' => 'bg-blue-900/40 text-blue-300 ring-1 ring-blue-800',
                                        default => 'bg-gray-800 text-gray-300 ring-1 ring-gray-700',
                                    };
                                @endphp
                                <span class="inline-flex items-center px-3 py-1 text-sm font-medium rounded-full {{ $badge }}">
                                    @if($invoice->status === 'paid')
                                        <i class="fas fa-check-circle mr-2"></i>
                                    @elseif($invoice->status === 'sent')
                                        <i class="fas fa-paper-plane mr-2"></i>
                                    @else
                                        <i class="fas fa-file-invoice mr-2"></i>
                                    @endif
                                    {{ ucfirst($invoice->status) }}
                                </span>
                            </div>
                            <div class="flex items-center gap-6 text-sm text-gray-400">
                                <span><i class="fas fa-calendar mr-2"></i>{{ $invoice->created_at->format('M d, Y') }}</span>
                                @if($invoice->due_date)
                                    <span><i class="fas fa-clock mr-2"></i>Due: {{ $invoice->due_date->format('M d, Y') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold text-white">${{ number_format($invoice->total_amount / 100, 2) }}</div>
                            <div class="text-sm text-gray-400">Total Amount</div>
                        </div>
                    </div>

                    @if($invoice->memo)
                        <div class="mb-4 p-4 bg-gray-900/50 rounded-lg border border-gray-700">
                            <p class="text-gray-300 text-sm"><i class="fas fa-comment-alt mr-2 text-gray-500"></i>{{ $invoice->memo }}</p>
                        </div>
                    @endif

                    <!-- Invoice Items -->
                    @if($invoice->items && $invoice->items->count() > 0)
                        <div class="mb-4">
                            <h4 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-2">Items</h4>
                            <div class="space-y-2">
                                @foreach($invoice->items as $item)
                                    <div class="flex items-center justify-between p-3 bg-gray-900/30 rounded-lg">
                                        <div class="flex-1">
                                            <div class="text-white font-medium">{{ $item->description }}</div>
                                            <div class="text-sm text-gray-400">Qty: {{ $item->quantity }} × ${{ number_format($item->unit_amount / 100, 2) }}</div>
                                        </div>
                                        <div class="text-white font-semibold">${{ number_format($item->total_amount / 100, 2) }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-700">
                        @if($invoice->status !== 'paid')
                            <!-- Generate/View Payment Link -->
                            @if($invoice->hasStripePaymentUrl())
                                <a href="{{ $invoice->stripe_payment_url }}" target="_blank" class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors font-semibold shadow-lg">
                                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-2.618 0-5.357-1.159-7.41-2.273l-.928 5.555C4.864 22.73 7.545 24 10.717 24c2.554 0 4.664-.705 6.104-2.029 1.516-1.391 2.287-3.325 2.287-5.754 0-3.944-2.577-5.732-5.132-7.062z"/>
                                    </svg>
                                    <span>Open Payment Link</span>
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                                <button onclick="copyToClipboard('{{ $invoice->stripe_payment_url }}')" class="px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors font-medium" title="Copy Payment Link">
                                    <i class="fas fa-copy"></i>
                                </button>
                            @else
                                <form method="POST" action="{{ route('admin.billing.invoices.stripe-payment-link', $invoice) }}" class="flex-1">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center gap-2 px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors font-semibold shadow-lg">
                                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                            <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-2.618 0-5.357-1.159-7.41-2.273l-.928 5.555C4.864 22.73 7.545 24 10.717 24c2.554 0 4.664-.705 6.104-2.029 1.516-1.391 2.287-3.325 2.287-5.754 0-3.944-2.577-5.732-5.132-7.062z"/>
                                        </svg>
                                        <span>Generate Payment Link (${{ number_format($invoice->total_amount / 100, 2) }})</span>
                                    </button>
                                </form>
                            @endif

                            <!-- Copy Payment Link -->
                            @if($invoice->stripe_payment_url && $invoice->stripe_payment_url !== 'direct')
                                <button onclick="copyToClipboard('{{ $invoice->stripe_payment_url }}')" class="px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors font-medium" title="Copy Payment Link">
                                    <i class="fas fa-copy mr-2"></i>Copy Link
                                </button>
                            @endif

                            <!-- Send Invoice -->
                            <form method="POST" action="{{ route('admin.billing.invoices.send-stripe', $invoice) }}">
                                @csrf
                                <button type="submit" class="px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-medium" title="Email invoice to client">
                                    <i class="fas fa-envelope mr-2"></i>Email Invoice
                                </button>
                            </form>

                            <!-- Delete Invoice -->
                            <form method="POST" action="{{ route('admin.billing.invoices.destroy', $invoice) }}" onsubmit="return confirm('Are you sure you want to delete this invoice?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-medium" title="Delete invoice">
                                    <i class="fas fa-trash mr-2"></i>Delete
                                </button>
                            </form>
                        @else
                            <div class="flex-1 flex items-center gap-3 px-4 py-3 bg-green-900/30 border border-green-800 rounded-lg">
                                <i class="fas fa-check-circle text-green-400 text-xl"></i>
                                <div class="flex-1">
                                    <div class="text-green-300 font-semibold">Payment Received</div>
                                    @if($invoice->paid_at)
                                        <div class="text-xs text-green-400">Paid on {{ $invoice->paid_at->format('M d, Y') }}</div>
                                    @endif
                                </div>
                                @if($invoice->stripe_payment_intent_id)
                                    <button 
                                        onclick="showRefundModal({{ $invoice->id }}, '{{ $invoice->total_amount / 100 }}')" 
                                        class="px-3 py-2 bg-red-600/20 hover:bg-red-600/30 border border-red-600/50 text-red-400 rounded-lg transition-colors text-sm font-medium"
                                        title="Refund payment"
                                    >
                                        <i class="fas fa-undo mr-1"></i>Refund
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-gray-800/50 rounded-xl border border-gray-700 p-12 text-center">
                <i class="fas fa-file-invoice text-gray-600 text-5xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-400 mb-2">No Invoices Yet</h3>
                <p class="text-gray-500 mb-6">Create your first invoice for this client</p>
                <a href="{{ route('admin.billing.create', $user) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors font-semibold">
                    <i class="fas fa-plus"></i>
                    <span>Create Invoice</span>
                </a>
            </div>
        @endforelse
    </div>
</div>

<!-- Refund Modal -->
<div id="refundModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50">
    <div class="bg-gray-800 rounded-2xl shadow-2xl max-w-md w-full mx-4 border border-gray-700">
        <div class="p-6 border-b border-gray-700">
            <h3 class="text-xl font-bold text-white">Refund Payment</h3>
        </div>
        <form id="refundForm" method="POST" action="">
            @csrf
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Refund Amount</label>
                    <div class="relative">
                        <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg">$</span>
                        <input 
                            type="number" 
                            name="amount" 
                            id="refundAmount"
                            step="0.01" 
                            min="0.01"
                            class="w-full pl-8 pr-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-red-500 focus:border-red-500"
                            placeholder="Full refund"
                        >
                    </div>
                    <p class="text-sm text-gray-400 mt-2">Leave blank for full refund</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">Reason (Optional)</label>
                    <textarea 
                        name="reason" 
                        rows="3"
                        class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none"
                        placeholder="Reason for refund..."
                    ></textarea>
                </div>
            </div>
            <div class="p-6 border-t border-gray-700 flex gap-3">
                <button 
                    type="button" 
                    onclick="closeRefundModal()" 
                    class="flex-1 px-4 py-3 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors font-medium"
                >
                    Cancel
                </button>
                <button 
                    type="submit" 
                    class="flex-1 px-4 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-medium"
                >
                    <i class="fas fa-undo mr-2"></i>Process Refund
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 flex items-center gap-2';
        toast.innerHTML = '<i class="fas fa-check"></i> Payment link copied to clipboard!';
        document.body.appendChild(toast);
        setTimeout(() => toast.remove(), 3000);
    }).catch(err => {
        console.error('Failed to copy:', err);
        alert('Failed to copy link. Please copy manually.');
    });
}

function showRefundModal(invoiceId, maxAmount) {
    const modal = document.getElementById('refundModal');
    const form = document.getElementById('refundForm');
    const amountInput = document.getElementById('refundAmount');
    
    form.action = `/admin/billing/invoices/${invoiceId}/refund`;
    amountInput.max = maxAmount;
    modal.classList.remove('hidden');
}

function closeRefundModal() {
    const modal = document.getElementById('refundModal');
    const form = document.getElementById('refundForm');
    modal.classList.add('hidden');
    form.reset();
}

// Close modal on escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeRefundModal();
    }
});

// Close modal on background click
document.getElementById('refundModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeRefundModal();
    }
});
</script>
@endpush
@endsection


