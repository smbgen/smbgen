@extends('layouts.client')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-gray-800/60 border border-gray-700 rounded-xl shadow-xl overflow-hidden">
        <div class="px-6 py-4 bg-gray-800/80 border-b border-gray-700 text-white font-semibold">Billing</div>
        <div class="p-6">
            @if(session('success'))
                <div class="mb-4 rounded-md border border-emerald-500/30 bg-emerald-500/10 px-4 py-2 text-emerald-200">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 rounded-md border border-red-500/30 bg-red-500/10 px-4 py-2 text-red-200">{{ session('error') }}</div>
            @endif

            <h5 class="text-white font-semibold mb-3">Your Invoices</h5>
            <div class="overflow-hidden rounded-lg border border-gray-700">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-800/80">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-300">#</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-300">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-300">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-300">Total</th>
                            <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-300">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 bg-gray-900/30">
                        @forelse($invoices as $invoice)
                            <tr class="hover:bg-gray-800/40">
                                <td class="px-4 py-3 text-gray-100">{{ $invoice->id }}</td>
                                <td class="px-4 py-3 text-gray-300">{{ $invoice->created_at->format('Y-m-d') }}</td>
                                <td class="px-4 py-3">
                                    @php $st = $invoice->status; @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium 
                                        {{ $st === 'paid' ? 'bg-emerald-500/20 text-emerald-300' : ($st === 'sent' ? 'bg-indigo-500/20 text-indigo-300' : 'bg-gray-500/20 text-gray-300') }}">
                                        {{ ucfirst($invoice->status) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-gray-100">{{ $invoice->formatted_total }}</td>
                                <td class="px-4 py-3 text-right">
                                    @if($invoice->status !== 'paid' && $invoice->total_amount > 0)
                                        <form action="{{ route('billing.pay', $invoice) }}" method="POST" class="inline">
                                            @csrf
                                            <button class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-medium text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/70">Pay</button>
                                        </form>
                                    @else
                                        <span class="text-gray-500">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center text-gray-400">No invoices yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <h5 class="text-white font-semibold mt-6 mb-3">Recent Payments</h5>
            <div class="overflow-hidden rounded-lg border border-gray-700">
                <table class="min-w-full divide-y divide-gray-700">
                    <thead class="bg-gray-800/80">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-300">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-300">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-300">Amount</th>
                            <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-300">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 bg-gray-900/30">
                        @forelse($payments as $payment)
                            <tr class="hover:bg-gray-800/40">
                                <td class="px-4 py-3 text-gray-300">{{ $payment->created_at->format('Y-m-d') }}</td>
                                <td class="px-4 py-3 text-gray-100">{{ $payment->description }}</td>
                                <td class="px-4 py-3 text-gray-100">{{ $payment->formatted_amount }}</td>
                                <td class="px-4 py-3">
                                    @php $ps = $payment->status; @endphp
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium 
                                        {{ $ps === 'completed' ? 'bg-emerald-500/20 text-emerald-300' : ($ps === 'pending' ? 'bg-amber-500/20 text-amber-300' : 'bg-red-500/20 text-red-300') }}">
                                        {{ ucfirst($payment->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-12 text-center text-gray-400">No payments yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


