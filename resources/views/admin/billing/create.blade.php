@extends('layouts.admin')

@section('content')
<div class="min-h-screen">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 via-indigo-600 to-blue-600 rounded-2xl p-8 mb-8 shadow-2xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold text-white mb-2 flex items-center gap-3">
                    <i class="fas fa-file-invoice-dollar"></i>
                    @if(isset($user))
                        New Invoice for {{ $user->name }}
                    @else
                        Create New Invoice
                    @endif
                </h1>
                <p class="text-purple-100 text-lg">Generate an invoice and collect payment from your clients</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.billing.index') }}" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white rounded-xl px-6 py-3 transition-all font-medium border border-white/30">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Billing
                </a>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ isset($user) ? route('admin.billing.store', $user) : route('admin.billing.store', ['user' => null]) }}" x-data="invoiceForm({{ isset($user) ? $user->id : 'null' }}, '{{ isset($user) ? $user->email : '' }}', '{{ isset($user) ? $user->name : '' }}')" @submit="calculateTotals">
        @csrf
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Form -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Client Selection (only show if no user pre-selected) -->
                @if(!isset($user))
                <div class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-300 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-user-circle text-blue-400"></i>
                        Select Client
                    </h2>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Client *</label>
                        <select name="user_id" required class="w-full bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent" x-model="selectedClient" @change="updateClientInfo">
                            <option value="">-- Select a client --</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" data-email="{{ $client->email }}" data-name="{{ $client->name }}">
                                    {{ $client->name }} ({{ $client->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                        
                        <div x-show="selectedClient" x-cloak class="mt-3 p-3 bg-blue-100 dark:bg-blue-900/20 border border-blue-300 dark:border-blue-800 rounded-lg">
                            <p class="text-sm text-blue-800 dark:text-blue-300">
                                <i class="fas fa-info-circle mr-2"></i>
                                <span x-text="'Invoice will be sent to: ' + clientEmail"></span>
                            </p>
                        </div>
                    </div>
                </div>
                @else
                <div class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-300 dark:border-gray-700 p-6">
                    <div class="flex items-center gap-3">
                        <div class="bg-blue-500/20 dark:bg-blue-500/10 p-3 rounded-xl">
                            <i class="fas fa-user-circle text-blue-400 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $user->name }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm">{{ $user->email }}</p>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Invoice Details -->
                <div class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-300 dark:border-gray-700 p-6">
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                        <i class="fas fa-file-alt text-green-400"></i>
                        Invoice Details
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Memo / Notes</label>
                            <textarea name="memo" rows="2" class="w-full bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Optional notes for the invoice..."></textarea>
                        </div>
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Due Date</label>
                                <input type="date" name="due_date" class="w-full bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent" :min="new Date().toISOString().split('T')[0]">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                <select name="status" class="w-full bg-white dark:bg-gray-900 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-3 focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    <option value="draft">Draft</option>
                                    <option value="sent" selected>Sent</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Line Items -->
                <div class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-300 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white flex items-center gap-2">
                            <i class="fas fa-list text-purple-400"></i>
                            Line Items
                        </h2>
                        <button type="button" @click="addItem" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors font-medium">
                            <i class="fas fa-plus"></i>
                            <span>Add Item</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(item, index) in items" :key="index">
                            <div class="bg-gray-100 dark:bg-gray-900/50 rounded-lg p-4 border border-gray-300 dark:border-gray-700">
                                <div class="grid grid-cols-12 gap-3">
                                    <div class="col-span-12 sm:col-span-6">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Description *</label>
                                        <input type="text" :name="'items[' + index + '][description]'" x-model="item.description" required class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent" placeholder="Service or product description">
                                    </div>
                                    <div class="col-span-4 sm:col-span-2">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Qty *</label>
                                        <input type="number" :name="'items[' + index + '][quantity]'" x-model.number="item.quantity" @input="calculateItemTotal(index)" required min="1" class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    </div>
                                    <div class="col-span-4 sm:col-span-2">
                                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Price ($) *</label>
                                        <input type="number" :name="'items[' + index + '][unit_amount]'" x-model.number="item.unit_amount" @input="calculateItemTotal(index)" required min="0" step="0.01" class="w-full bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-purple-500 focus:border-transparent">
                                    </div>
                                    <div class="col-span-4 sm:col-span-2 flex items-end">
                                        <div class="w-full">
                                            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Total</label>
                                            <div class="bg-gray-200 dark:bg-gray-700/50 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 text-sm text-gray-900 dark:text-gray-200 font-semibold" x-text="'$' + item.total.toFixed(2)"></div>
                                        </div>
                                        <button type="button" @click="removeItem(index)" x-show="items.length > 1" class="ml-2 px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div x-show="items.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>No items added yet. Click "Add Item" to get started.</p>
                    </div>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div class="lg:col-span-1">
                <div class="sticky top-6 space-y-6">
                    <!-- Total Summary -->
                    <div class="bg-gradient-to-br from-purple-600 to-indigo-600 rounded-xl p-6 shadow-2xl">
                        <h3 class="text-white text-sm font-medium mb-2 opacity-90">Invoice Total</h3>
                        <div class="text-4xl font-bold text-white mb-4" x-text="'$' + totalAmount.toFixed(2)">$0.00</div>
                        <div class="space-y-2 text-sm text-white/80">
                            <div class="flex justify-between">
                                <span>Items:</span>
                                <span x-text="items.length">0</span>
                            </div>
                            <div class="flex justify-between">
                                <span>Subtotal:</span>
                                <span x-text="'$' + totalAmount.toFixed(2)">$0.00</span>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Options -->
                    <div class="bg-white dark:bg-gray-800/50 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <h3 class="text-gray-900 dark:text-white font-semibold mb-4 flex items-center gap-2">
                            <i class="fas fa-credit-card text-purple-400"></i>
                            Payment Options
                        </h3>
                        <div class="space-y-3">
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <input type="checkbox" name="generate_payment_link" value="1" class="mt-1 w-4 h-4 text-purple-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-purple-500 focus:ring-2">
                                <div>
                                    <div class="text-sm font-medium text-gray-700 dark:text-gray-200 group-hover:text-gray-900 dark:group-hover:text-white">Generate Stripe Payment Link</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Create a secure payment link for the client</div>
                                </div>
                            </label>
                            <label class="flex items-start gap-3 cursor-pointer group">
                                <input type="checkbox" name="send_email" value="1" class="mt-1 w-4 h-4 text-purple-600 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-purple-500 focus:ring-2">
                                <div>
                                    <div class="text-sm font-medium text-gray-700 dark:text-gray-200 group-hover:text-gray-900 dark:group-hover:text-white">Email Invoice to Client</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">Send invoice via email immediately</div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="w-full bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 flex items-center justify-center gap-3">
                        <i class="fas fa-paper-plane"></i>
                        <span>Create Invoice</span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
function invoiceForm(preselectedUserId = null, preselectedEmail = '', preselectedName = '') {
    return {
        selectedClient: preselectedUserId || '',
        clientEmail: preselectedEmail || '',
        clientName: preselectedName || '',
        items: [
            { description: '', quantity: 1, unit_amount: 0, total: 0 }
        ],
        totalAmount: 0,
        
        addItem() {
            this.items.push({ description: '', quantity: 1, unit_amount: 0, total: 0 });
        },
        
        removeItem(index) {
            if (this.items.length > 1) {
                this.items.splice(index, 1);
                this.calculateTotal();
            }
        },
        
        calculateItemTotal(index) {
            const item = this.items[index];
            item.total = (item.quantity || 0) * (item.unit_amount || 0);
            this.calculateTotal();
        },
        
        calculateTotal() {
            this.totalAmount = this.items.reduce((sum, item) => sum + (item.total || 0), 0);
        },
        
        calculateTotals() {
            this.calculateTotal();
        },
        
        updateClientInfo(event) {
            const option = event.target.selectedOptions[0];
            this.clientEmail = option.dataset.email || '';
            this.clientName = option.dataset.name || '';
        }
    };
}
</script>
@endpush
@endsection


