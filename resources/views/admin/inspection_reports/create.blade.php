@extends('layouts.admin')

@section('content')
<div class="p-6">
    <h1 class="text-2xl font-bold text-white mb-4">Create Inspection Report</h1>

    <div class="bg-gray-800 p-6 rounded-xl">
        <!-- Search and quick-select clients/bookings -->
        <div class="mb-6" x-data="inspectionReportSearch()">
            <label class="block text-sm text-gray-300 mb-2">Search clients or bookings</label>
            <div class="relative">
                <input type="text" x-model="query" @input.debounce.300ms="performSearch()" placeholder="Search by name, email, phone..." class="w-full pl-12 pr-12 py-3 bg-gray-700 border-2 border-gray-600 rounded-xl text-gray-100 placeholder-gray-400">
                <div x-show="loading" class="absolute right-4 top-1/2 transform -translate-y-1/2">
                    <i class="fas fa-spinner fa-spin text-purple-500"></i>
                </div>
            </div>

            <div x-show="resultsAvailable()" x-transition class="mt-3 bg-gray-900 rounded-xl border border-gray-700 max-h-72 overflow-y-auto">
                <template x-if="results.clients.length">
                    <div class="border-b border-gray-700">
                        <div class="px-4 py-3 bg-gray-800/50 font-semibold text-gray-300">Clients</div>
                        <div class="divide-y divide-gray-700">
                            <template x-for="client in results.clients" :key="client.id">
                                <button type="button" @click="selectClient(client)" class="block w-full text-left p-4 hover:bg-gray-800 transition-colors">
                                    <div class="font-medium text-gray-100" x-text="client.name"></div>
                                    <div class="text-sm text-gray-400 mt-1">
                                        <i class="fas fa-envelope mr-1"></i>
                                        <span x-text="client.email"></span>
                                        <template x-if="client.phone">
                                            <span>
                                                <i class="fas fa-phone ml-3 mr-1"></i>
                                                <span x-text="client.phone"></span>
                                            </span>
                                        </template>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>

                <template x-if="results.bookings.length">
                    <div class="border-b border-gray-700">
                        <div class="px-4 py-3 bg-gray-800/50 font-semibold text-gray-300">Bookings</div>
                        <div class="divide-y divide-gray-700">
                            <template x-for="booking in results.bookings" :key="booking.id">
                                <button type="button" @click="selectBooking(booking)" class="block w-full text-left p-4 hover:bg-gray-800 transition-colors">
                                    <div class="font-medium text-gray-100" x-text="booking.client_name"></div>
                                    <div class="text-sm text-gray-400 mt-1">
                                        <i class="fas fa-clock mr-1"></i>
                                        <span x-text="booking.booking_time"></span>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </template>
            </div>

            <div x-show="selected" class="mt-4 p-3 bg-gray-700 rounded-lg text-gray-100 flex items-center justify-between">
                <div>
                    <div class="font-semibold" x-text="selected.name"></div>
                    <div class="text-sm text-gray-300" x-text="selected.detail"></div>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" @click="clearSelection()" class="bg-gray-600 px-3 py-1 rounded text-sm">Clear</button>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('admin.inspection-reports.store') }}">
            @csrf

            <div class="grid grid-cols-1 gap-4">
                <div>
                    <label class="block text-sm text-gray-300">Client (optional)</label>
                    <select name="client_id" id="client_id" class="w-full bg-gray-700 text-white rounded p-2">
                        <option value="">-- Select client --</option>
                        @foreach(\App\Models\Client::orderBy('name')->get() as $client)
                            <option value="{{ $client->id }}">{{ $client->name }} ({{ $client->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-gray-300">Client Name</label>
                    <input type="text" id="client_name" name="client_name" class="w-full rounded p-2 bg-gray-700 text-white" required>
                </div>

                <div>
                    <label class="block text-sm text-gray-300">Client Email</label>
                    <input type="email" id="client_email" name="client_email" class="w-full rounded p-2 bg-gray-700 text-white">
                </div>

                <div>
                    <label class="block text-sm text-gray-300">Client Phone</label>
                    <input type="text" id="client_phone" name="client_phone" class="w-full rounded p-2 bg-gray-700 text-white">
                </div>

                <div>
                    <label class="block text-sm text-gray-300">Client Address</label>
                    <input type="text" id="client_address" name="client_address" class="w-full rounded p-2 bg-gray-700 text-white">
                </div>

                <div>
                    <label class="block text-sm text-gray-300">Consult Date</label>
                    <input type="datetime-local" id="consult_date" name="consult_date" class="w-full rounded p-2 bg-gray-700 text-white">
                </div>

                <div>
                    <label class="block text-sm text-gray-300">Summary Title</label>
                    <input type="text" name="summary_title" class="w-full rounded p-2 bg-gray-700 text-white" required>
                </div>

                <div>
                    <label class="block text-sm text-gray-300">Explanation</label>
                    <textarea name="body_explanation" rows="6" class="w-full rounded p-2 bg-gray-700 text-white"></textarea>
                </div>

                <div>
                    <label class="block text-sm text-gray-300">Suggested Actions</label>
                    <textarea name="body_suggested_actions" rows="4" class="w-full rounded p-2 bg-gray-700 text-white"></textarea>
                </div>

                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="send_email" value="1" class="form-check-input">
                        <span class="text-gray-300">Send email to client</span>
                    </label>
                </div>

                <div>
                    <button type="submit" class="bg-blue-600 px-4 py-2 rounded text-white">Create Report</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
function inspectionReportSearch() {
    return {
        query: '',
        loading: false,
        results: { clients: [], bookings: [] },
        selected: null,

        async performSearch() {
            if (this.query.length < 2) {
                this.results = { clients: [], bookings: [] };
                return;
            }

            this.loading = true;

            try {
                const params = new URLSearchParams({ q: this.query, type: 'all' });
                const res = await fetch(`/admin/search?${params}`);
                if (res.ok) {
                    const data = await res.json();
                    this.results.clients = data.clients || [];
                    this.results.bookings = data.bookings || [];
                }
            } catch (e) {
                console.error('Search failed', e);
            } finally {
                this.loading = false;
            }
        },

        resultsAvailable() {
            return (this.results.clients.length || this.results.bookings.length) > 0;
        },

        selectClient(client) {
            this.selected = { name: client.name, detail: client.email || client.phone };
            // populate fields
            document.getElementById('client_name').value = client.name || '';
            document.getElementById('client_email').value = client.email || '';
            document.getElementById('client_phone').value = client.phone || '';
            // set client select if exists
            const clientSelect = document.getElementById('client_id');
            if (clientSelect) {
                const opt = Array.from(clientSelect.options).find(o => o.value == client.id);
                if (opt) opt.selected = true;
            }
        },

        selectBooking(booking) {
            this.selected = { name: booking.client_name, detail: booking.booking_time };
            document.getElementById('client_name').value = booking.client_name || '';
            // try to parse booking.booking_time (e.g., "Mar 5, 2025 2:00 PM") into datetime-local
            try {
                const dt = new Date(booking.booking_time);
                if (!isNaN(dt)) {
                    const tzOffset = dt.getTimezoneOffset() * 60000;
                    const local = new Date(dt - tzOffset).toISOString().slice(0,16);
                    document.getElementById('consult_date').value = local;
                }
            } catch (e) {
                // ignore
            }
        },

        clearSelection() {
            this.selected = null;
            document.getElementById('client_id').value = '';
            document.getElementById('client_name').value = '';
            document.getElementById('client_email').value = '';
            document.getElementById('client_phone').value = '';
            document.getElementById('client_address').value = '';
            document.getElementById('consult_date').value = '';
        }
    }
}
</script>
@endpush
