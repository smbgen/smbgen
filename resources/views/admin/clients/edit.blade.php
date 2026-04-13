@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Edit Client</h2>
        <a href="{{ route('clients.index') }}" class="btn-secondary">
            ← Back to Clients
        </a>
    </div>

    <div class="card">
        <div class="bg-white dark:bg-gray-800 px-6 py-4 rounded-t-lg">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-0">Update Client Information</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('clients.update', $client) }}" method="POST" class="space-y-6">
                @csrf
                @method('PATCH')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            👤 Full Name *
                        </label>
                        <input 
                            type="text" 
                            name="name" 
                            value="{{ old('name', $client->name) }}"
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
                            required
                            placeholder="Enter client's full name"
                        >
                        @error('name')
                            <div class="text-red-400 text-sm mt-2">⚠️ {{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            📧 Email Address *
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            value="{{ old('email', $client->email) }}"
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
                            required
                            placeholder="client@example.com"
                        >
                        @error('email')
                            <div class="text-red-400 text-sm mt-2">⚠️ {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            📞 Phone Number
                        </label>
                        <input 
                            type="text" 
                            name="phone" 
                            value="{{ old('phone', $client->phone) }}"
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
                            placeholder="(555) 123-4567"
                        >
                        @error('phone')
                            <div class="text-red-400 text-sm mt-2">⚠️ {{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            🏷️ Source Site
                        </label>
                        <input 
                            type="text" 
                            name="source_site" 
                            value="{{ old('source_site', $client->source_site) }}"
                            class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
                            placeholder="Website or referral source"
                        >
                        @error('source_site')
                            <div class="text-red-400 text-sm mt-2">⚠️ {{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        🏠 Property Address
                    </label>
                    <textarea 
                        name="property_address" 
                        rows="3"
                        class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
                        placeholder="123 Main St, City, State ZIP"
                    >{{ old('property_address', $client->property_address) }}</textarea>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Optional property address for real estate services</p>
                    @error('property_address')
                        <div class="text-red-400 text-sm mt-2">⚠️ {{ $message }}</div>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        📝 Notes
                    </label>
                    <textarea 
                        name="notes" 
                        rows="4"
                        class="w-full px-4 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-opacity-50"
                        placeholder="Additional notes about the client..."
                    >{{ old('notes', $client->notes) }}</textarea>
                    @error('notes')
                        <div class="text-red-400 text-sm mt-2">⚠️ {{ $message }}</div>
                    @enderror
                </div>

                <div class="flex items-center gap-4 pt-4">
                    <button type="submit" class="btn-primary px-6 py-3">
                        ✓ Update Client
                    </button>
                    <a href="{{ route('clients.index') }}" class="btn-secondary px-6 py-3">
                        ✕ Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
