<div class="bg-white dark:bg-gray-800/50 backdrop-blur-sm rounded-2xl p-6 border border-gray-200 dark:border-gray-700 shadow-xl" x-data="searchWidget()">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6 flex items-center gap-3">
        <div class="bg-gradient-to-r from-blue-600 dark:from-blue-500 to-purple-600 dark:to-purple-500 rounded-xl p-2">
            <i class="fas fa-search text-white"></i>
        </div>
        Quick Search
    </h2>

    <!-- Search Input -->
    <div class="relative mb-6">
        <div class="relative">
            <input 
                type="text" 
                x-model="searchQuery"
                @input.debounce.300ms="performSearch()"
                @focus="showResults = true"
                @keydown.escape="showResults = false"
                placeholder="Search clients, bookings, leads, invoices, users..."
                class="w-full pl-12 pr-12 py-4 bg-gray-100 dark:bg-gray-700 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 text-lg"
            >
            <i class="fas fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-400 text-lg"></i>
            <button 
                x-show="searchQuery.length > 0"
                @click="clearSearch()"
                class="absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Loading Indicator -->
        <div x-show="loading" class="absolute right-4 top-1/2 transform -translate-y-1/2">
            <i class="fas fa-spinner fa-spin text-purple-500"></i>
        </div>
    </div>

    <!-- Search Type Filters -->
    <div class="flex flex-wrap gap-2 mb-6">
        <button 
            @click="searchType = 'all'"
            :class="searchType === 'all' ? 'bg-purple-600 dark:bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        >
            <i class="fas fa-th-large mr-1"></i> All
        </button>
        <button 
            @click="searchType = 'clients'"
            :class="searchType === 'clients' ? 'bg-blue-600 dark:bg-blue-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        >
            <i class="fas fa-user-tie mr-1"></i> Clients
        </button>
        <button 
            @click="searchType = 'bookings'"
            :class="searchType === 'bookings' ? 'bg-green-600 dark:bg-green-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        >
            <i class="fas fa-calendar mr-1"></i> Bookings
        </button>
        <button 
            @click="searchType = 'leads'"
            :class="searchType === 'leads' ? 'bg-yellow-600 dark:bg-yellow-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        >
            <i class="fas fa-star mr-1"></i> Leads
        </button>
        <button 
            @click="searchType = 'invoices'"
            :class="searchType === 'invoices' ? 'bg-pink-600 dark:bg-pink-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        >
            <i class="fas fa-file-invoice-dollar mr-1"></i> Invoices
        </button>
        <button 
            @click="searchType = 'users'"
            :class="searchType === 'users' ? 'bg-purple-600 dark:bg-purple-500 text-white' : 'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600'"
            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
        >
            <i class="fas fa-users mr-1"></i> Users
        </button>
    </div>

    <!-- Search Results -->
    <div 
        x-show="showResults && searchQuery.length > 0" 
        x-transition
        class="bg-white dark:bg-gray-900 rounded-xl border border-gray-300 dark:border-gray-700 max-h-96 overflow-y-auto"
    >
        <!-- No Results -->
        <div x-show="!loading && !hasResults()" class="p-6 text-center text-gray-600 dark:text-gray-400">
            <i class="fas fa-search text-4xl mb-3"></i>
            <p>No results found for "<span x-text="searchQuery"></span>"</p>
        </div>

        <!-- Results -->
        <div x-show="!loading && hasResults()">
            <!-- Clients -->
            <template x-if="shouldShowSection('clients') && results.clients.length > 0">
                <div class="border-b border-gray-300 dark:border-gray-700">
                    <div class="px-4 py-3 bg-gray-100 dark:bg-gray-800/50 font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <i class="fas fa-user-tie text-blue-600 dark:text-blue-400"></i>
                        Clients (<span x-text="results.clients.length"></span>)
                    </div>
                    <div class="divide-y divide-gray-300 dark:divide-gray-700">
                        <template x-for="client in results.clients" :key="client.id">
                            <a :href="`/admin/clients/${client.id}`" class="block p-4 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <div class="font-medium text-gray-900 dark:text-gray-100" x-text="client.name"></div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    <i class="fas fa-envelope mr-1"></i>
                                    <span x-text="client.email"></span>
                                    <template x-if="client.phone">
                                        <span>
                                            <i class="fas fa-phone ml-3 mr-1"></i>
                                            <span x-text="client.phone"></span>
                                        </span>
                                    </template>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Bookings -->
            <template x-if="shouldShowSection('bookings') && results.bookings.length > 0">
                <div class="border-b border-gray-300 dark:border-gray-700">
                    <div class="px-4 py-3 bg-gray-100 dark:bg-gray-800/50 font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <i class="fas fa-calendar text-green-600 dark:text-green-400"></i>
                        Bookings (<span x-text="results.bookings.length"></span>)
                    </div>
                    <div class="divide-y divide-gray-300 dark:divide-gray-700">
                        <template x-for="booking in results.bookings" :key="booking.id">
                            <a :href="`/admin/bookings/${booking.id}`" class="block p-4 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <div class="font-medium text-gray-900 dark:text-gray-100" x-text="booking.client_name"></div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    <i class="fas fa-clock mr-1"></i>
                                    <span x-text="booking.booking_time"></span>
                                    <span :class="{
                                        'ml-2 px-2 py-0.5 rounded text-xs font-semibold': true,
                                        'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300': booking.status === 'confirmed',
                                        'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300': booking.status === 'pending',
                                        'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300': booking.status === 'cancelled'
                                    }" x-text="booking.status"></span>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Leads -->
            <template x-if="shouldShowSection('leads') && results.leads.length > 0">
                <div class="border-b border-gray-300 dark:border-gray-700">
                    <div class="px-4 py-3 bg-gray-100 dark:bg-gray-800/50 font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <i class="fas fa-star text-yellow-600 dark:text-yellow-400"></i>
                        Leads (<span x-text="results.leads.length"></span>)
                    </div>
                    <div class="divide-y divide-gray-300 dark:divide-gray-700">
                        <template x-for="lead in results.leads" :key="lead.id">
                            <a :href="`/admin/leads/${lead.id}`" class="block p-4 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <div class="font-medium text-gray-900 dark:text-gray-100" x-text="lead.name"></div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    <i class="fas fa-envelope mr-1"></i>
                                    <span x-text="lead.email"></span>
                                    <template x-if="lead.phone">
                                        <span>
                                            <i class="fas fa-phone ml-3 mr-1"></i>
                                            <span x-text="lead.phone"></span>
                                        </span>
                                    </template>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Invoices -->
            <template x-if="shouldShowSection('invoices') && results.invoices.length > 0">
                <div class="border-b border-gray-300 dark:border-gray-700">
                    <div class="px-4 py-3 bg-gray-100 dark:bg-gray-800/50 font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <i class="fas fa-file-invoice-dollar text-pink-600 dark:text-pink-400"></i>
                        Invoices (<span x-text="results.invoices.length"></span>)
                    </div>
                    <div class="divide-y divide-gray-300 dark:divide-gray-700">
                        <template x-for="invoice in results.invoices" :key="invoice.id">
                            <a :href="`/admin/invoices/${invoice.id}`" class="block p-4 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-gray-100">Invoice #<span x-text="invoice.invoice_number"></span></div>
                                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1" x-text="invoice.client_name"></div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-gray-900 dark:text-gray-100">$<span x-text="invoice.total"></span></div>
                                        <span :class="{
                                            'text-xs px-2 py-0.5 rounded font-semibold': true,
                                            'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300': invoice.status === 'paid',
                                            'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-300': invoice.status === 'sent',
                                            'bg-gray-300 dark:bg-gray-700 text-gray-700 dark:text-gray-300': invoice.status === 'draft'
                                        }" x-text="invoice.status"></span>
                                    </div>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </template>

            <!-- Users -->
            <template x-if="shouldShowSection('users') && results.users.length > 0">
                <div>
                    <div class="px-4 py-3 bg-gray-100 dark:bg-gray-800/50 font-semibold text-gray-700 dark:text-gray-300 flex items-center gap-2">
                        <i class="fas fa-users text-purple-600 dark:text-purple-400"></i>
                        Users (<span x-text="results.users.length"></span>)
                    </div>
                    <div class="divide-y divide-gray-300 dark:divide-gray-700">
                        <template x-for="user in results.users" :key="user.id">
                            <a :href="`/admin/users/${user.id}/edit`" class="block p-4 hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                                <div class="font-medium text-gray-900 dark:text-gray-100" x-text="user.name"></div>
                                <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                    <i class="fas fa-envelope mr-1"></i>
                                    <span x-text="user.email"></span>
                                    <span :class="{
                                        'ml-2 px-2 py-0.5 rounded text-xs font-semibold': true,
                                        'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300': user.role === 'company_administrator',
                                        'bg-gray-200 dark:bg-gray-700 text-gray-700 dark:text-gray-300': user.role === 'user'
                                    }">
                                        <i :class="user.role === 'company_administrator' ? 'fas fa-user-shield' : 'fas fa-user'" class="mr-1"></i>
                                        <span x-text="user.role === 'company_administrator' ? 'Admin' : 'User'"></span>
                                    </span>
                                </div>
                            </a>
                        </template>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Quick Stats -->
    <div x-show="!showResults || searchQuery.length === 0" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mt-6">
        <div class="bg-gray-100 dark:bg-gray-700/50 rounded-lg p-4 text-center">
            <i class="fas fa-user-tie text-blue-600 dark:text-blue-400 text-2xl mb-2"></i>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="stats.clients"></div>
            <div class="text-xs text-gray-600 dark:text-gray-400">Clients</div>
        </div>
        <div class="bg-gray-100 dark:bg-gray-700/50 rounded-lg p-4 text-center">
            <i class="fas fa-calendar text-green-600 dark:text-green-400 text-2xl mb-2"></i>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="stats.bookings"></div>
            <div class="text-xs text-gray-600 dark:text-gray-400">Bookings</div>
        </div>
        <div class="bg-gray-100 dark:bg-gray-700/50 rounded-lg p-4 text-center">
            <i class="fas fa-star text-yellow-600 dark:text-yellow-400 text-2xl mb-2"></i>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="stats.leads"></div>
            <div class="text-xs text-gray-600 dark:text-gray-400">Leads</div>
        </div>
        <div class="bg-gray-100 dark:bg-gray-700/50 rounded-lg p-4 text-center">
            <i class="fas fa-file-invoice-dollar text-pink-600 dark:text-pink-400 text-2xl mb-2"></i>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="stats.invoices"></div>
            <div class="text-xs text-gray-600 dark:text-gray-400">Invoices</div>
        </div>
        <div class="bg-gray-100 dark:bg-gray-700/50 rounded-lg p-4 text-center">
            <i class="fas fa-users text-purple-600 dark:text-purple-400 text-2xl mb-2"></i>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="stats.users"></div>
            <div class="text-xs text-gray-600 dark:text-gray-400">Users</div>
        </div>
        <div class="bg-gray-100 dark:bg-gray-700/50 rounded-lg p-4 text-center">
            <i class="fas fa-database text-indigo-600 dark:text-indigo-400 text-2xl mb-2"></i>
            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="stats.total"></div>
            <div class="text-xs text-gray-600 dark:text-gray-400">Total Records</div>
        </div>
    </div>
</div>

<script>
function searchWidget() {
    return {
        searchQuery: '',
        searchType: 'all',
        showResults: false,
        loading: false,
        results: {
            clients: [],
            bookings: [],
            leads: [],
            invoices: [],
            users: []
        },
        stats: {
            clients: 0,
            bookings: 0,
            leads: 0,
            invoices: 0,
            users: 0,
            total: 0
        },

        init() {
            this.loadStats();
        },

        async loadStats() {
            try {
                const response = await fetch('/admin/search/stats');
                if (response.ok) {
                    this.stats = await response.json();
                }
            } catch (error) {
                console.error('Failed to load search stats:', error);
            }
        },

        async performSearch() {
            if (this.searchQuery.length < 2) {
                this.results = {
                    clients: [],
                    bookings: [],
                    leads: [],
                    invoices: [],
                    users: []
                };
                return;
            }

            this.loading = true;
            this.showResults = true;

            try {
                const params = new URLSearchParams({
                    q: this.searchQuery,
                    type: this.searchType
                });

                const response = await fetch(`/admin/search?${params}`);
                if (response.ok) {
                    this.results = await response.json();
                }
            } catch (error) {
                console.error('Search failed:', error);
            } finally {
                this.loading = false;
            }
        },

        clearSearch() {
            this.searchQuery = '';
            this.showResults = false;
            this.results = {
                clients: [],
                bookings: [],
                leads: [],
                invoices: [],
                users: []
            };
        },

        hasResults() {
            return Object.values(this.results).some(arr => arr.length > 0);
        },

        shouldShowSection(type) {
            return this.searchType === 'all' || this.searchType === type;
        }
    }
}
</script>
