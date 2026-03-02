@props(['invoices'])

@if($invoices->count() > 0)
<div class="bg-white dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-xl">
    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
        <i class="fas fa-file-invoice-dollar text-yellow-600 dark:text-yellow-400"></i>
        Pending Invoices
        <span class="bg-yellow-100 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-400 text-xs font-bold px-2 py-1 rounded-full">
            {{ $invoices->count() }}
        </span>
    </h3>
    <div class="space-y-3">
        @foreach($invoices as $invoice)
        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
            <div class="flex items-center justify-between mb-2">
                <div>
                    <div class="text-gray-900 dark:text-white font-medium">Invoice #{{ $invoice->invoice_number }}</div>
                    <div class="text-gray-600 dark:text-gray-400 text-sm">{{ optional($invoice->user)->name ?? 'N/A' }}</div>
                </div>
                <div class="text-right">
                    <div class="text-gray-900 dark:text-white font-bold">${{ number_format($invoice->amount, 2) }}</div>
                    <div class="text-gray-600 dark:text-gray-400 text-xs">Due: {{ $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('M d') : 'N/A' }}</div>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-2 py-1 bg-yellow-100 dark:bg-yellow-500/20 text-yellow-700 dark:text-yellow-400 rounded text-xs font-medium">
                    {{ ucfirst($invoice->status) }}
                </span>
                @if($invoice->due_date && \Carbon\Carbon::parse($invoice->due_date)->isPast())
                <span class="px-2 py-1 bg-red-100 dark:bg-red-500/20 text-red-700 dark:text-red-400 rounded text-xs font-medium">
                    Overdue
                </span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    <div class="mt-4">
        <a href="{{ route('admin.billing.index') }}" class="block text-center bg-yellow-600 hover:bg-yellow-700 dark:bg-yellow-600 dark:hover:bg-yellow-700 text-white rounded-xl py-2 transition-colors font-medium">
            Manage Invoices
        </a>
    </div>
</div>
@endif
