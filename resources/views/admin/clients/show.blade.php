@extends('layouts.admin')

@section('content')
<div class="px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-0">{{ $client->name }}</h2>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Client Details & History</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('clients.edit', $client) }}" class="btn-primary">
                <i class="fas fa-edit mr-2"></i>Edit Client
            </a>
            <a href="{{ route('clients.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Clients
            </a>
        </div>
    </div>

    @if(!$client->user)
        <div class="mb-6 bg-yellow-100 border border-yellow-400 text-yellow-800 px-4 py-3 rounded">
            <strong>Note:</strong> This client record does not have a linked user account. Some actions (like sending magic links or direct messages) are disabled until a user is provisioned for this client.
        </div>
    @endif

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-400 dark:border-green-500 text-green-800 dark:text-green-300 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-400 dark:border-red-500 text-red-800 dark:text-red-300 px-4 py-3 rounded-lg mb-6">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <!-- Client Information -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Client Details Card -->
        <div class="card">
            <div class="bg-blue-600 text-white px-6 py-4 rounded-t-lg">
                <h3 class="text-lg font-semibold">👤 Client Information</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Name</label>
                        <p class="text-gray-900 dark:text-gray-100 font-medium">{{ $client->name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Email</label>
                        <p class="text-gray-900 dark:text-gray-100">{{ $client->email }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Phone</label>
                        <p class="text-gray-900 dark:text-gray-100">{{ $client->phone ?? 'Not provided' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Source</label>
                        <p class="text-gray-100">
                            @if($client->source_site)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                    {{ $client->source_site }}
                                </span>
                            @else
                                <span class="text-gray-400">Manual Entry</span>
                            @endif
                        </p>
                    </div>
                    @if($client->property_address)
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">🏠 Property Address</label>
                            <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $client->property_address }}</p>
                        </div>
                    @endif
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Status</label>
                    </div>
                    @if($client->notes)
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Notes</label>
                            <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $client->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card">
            <div class="bg-purple-600 text-white px-6 py-4 rounded-t-lg">
                <h3 class="text-lg font-semibold">🔐 Account Provisioning</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-2">Status</label>
                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $client->provisioning_status_badge }}">
                            {{ $client->provisioning_status_label }}
                        </span>
                    </div>

                    @if($client->user_provisioned_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">User Provisioned</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $client->user_provisioned_at->format('M j, Y g:i A') }}</p>
                            <p class="text-xs text-gray-500">{{ $client->user_provisioned_at->diffForHumans() }}</p>
                        </div>
                    @else
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">User Provisioned</label>
                            <p class="text-gray-500">Not yet provisioned</p>
                        </div>
                    @endif

                    @if($client->account_activated_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Account Activated</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $client->account_activated_at->format('M j, Y g:i A') }}</p>
                            <p class="text-xs text-gray-500">{{ $client->account_activated_at->diffForHumans() }}</p>
                        </div>
                    @else
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Account Activated</label>
                            <p class="text-gray-500">Not yet activated (no login)</p>
                        </div>
                    @endif

                    @if($client->last_login_at)
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Last Login</label>
                            <p class="text-gray-900 dark:text-gray-100">{{ $client->last_login_at->format('M j, Y g:i A') }}</p>
                            <p class="text-xs text-gray-500">{{ $client->last_login_at->diffForHumans() }}</p>
                        </div>
                    @else
                        <div>
                            <label class="block text-sm font-medium text-gray-600 dark:text-gray-400">Last Login</label>
                            <p class="text-gray-500">Never</p>
                        </div>
                    @endif
                    
                    <div class="pt-4 border-t border-gray-200 dark:border-gray-600">
                        <label class="block text-sm font-medium text-gray-600 dark:text-gray-400 mb-3">Portal Access Control</label>
                        <form method="POST" action="{{ route('clients.toggle-access', $client) }}" class="flex items-center justify-between">
                            @csrf
                            @method('PATCH')
                            <div class="flex items-center gap-2">
                                <button type="submit" 
                                    class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $client->is_active ? 'bg-green-600' : 'bg-gray-600' }}"
                                    onclick="return confirm('{{ $client->is_active ? 'Disable portal access? User will not be able to login.' : 'Enable portal access? User will be able to login.' }}')">
                                    <span class="inline-block h-4 w-4 transform rounded-full bg-white transition {{ $client->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                                </button>
                                <span class="text-sm {{ $client->is_active ? 'text-green-400' : 'text-gray-400' }}">
                                    {{ $client->is_active ? 'Enabled' : 'Disabled' }}
                                </span>
                            </div>
                        </form>
                        <p class="text-xs text-gray-500 mt-2">
                            {{ $client->is_active ? 'Client can login to their portal' : 'Client cannot login (account locked)' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="card">
            <div class="bg-green-600 text-white px-6 py-4 rounded-t-lg">
                <h3 class="text-lg font-semibold">📊 Quick Stats</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-4 bg-gray-100 dark:bg-gray-700/50 rounded-lg border border-gray-300 dark:border-gray-600">
                        <div class="text-3xl font-bold text-blue-400">{{ $messages->count() }}</div>
                        <div class="text-gray-600 dark:text-gray-400 text-xs mt-1">Messages</div>
                    </div>
                    @if(config('business.features.file_management'))
                        <div class="text-center p-4 bg-gray-100 dark:bg-gray-700/50 rounded-lg border border-gray-300 dark:border-gray-600">
                            <div class="text-3xl font-bold text-cyan-400">{{ $client->files->count() }}</div>
                            <div class="text-gray-600 dark:text-gray-400 text-xs mt-1">Files</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="bg-purple-600 text-white px-6 py-4 rounded-t-lg">
                <h3 class="text-lg font-semibold">⚡ Quick Actions</h3>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @if(config('business.features.file_management'))
                        <a href="{{ route('admin.client.files', $client) }}" class="block btn-secondary text-sm hover:bg-cyan-600 hover:border-cyan-600 transition-colors">
                            <i class="fas fa-folder-open mr-2"></i>Manage Files
                        </a>
                    @endif
                    <a href="{{ route('messages.create') }}?recipient_id={{ $client->user?->id ?? '' }}" class="block btn-secondary text-sm hover:bg-green-600 hover:border-green-600 transition-colors">
                        <i class="fas fa-envelope mr-2"></i>Send Message
                    </a>
                    <a href="{{ route('clients.edit', $client) }}" class="block btn-secondary text-sm">
                        <i class="fas fa-user-edit mr-2"></i>Edit Client
                    </a>
                    <form method="POST" action="{{ route('clients.destroy', $client) }}" class="block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full btn-danger text-sm" onclick="return confirm('Are you sure you want to delete this client? This action cannot be undone.')">
                            <i class="fas fa-trash mr-2"></i>Delete Client
                        </button>
                    </form>
                    @if($client->user)
                        <form method="POST" action="{{ route('admin.users.magiclink.send', $client->user) }}" class="mt-3">
                            @csrf
                            <button type="submit" class="w-full btn-secondary text-sm">
                                <i class="fas fa-magic mr-2"></i>Send Magic Link
                            </button>
                        </form>
                    @else
                        <form method="POST" action="{{ route('clients.provision', $client) }}" class="mt-3">
                            @csrf
                            <button type="submit" class="w-full btn-primary text-sm">
                                <i class="fas fa-user-plus mr-2"></i>Provision User Account
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Messages Section -->
    <div class="card">
        <div class="bg-green-600 text-white px-6 py-4 rounded-t-lg flex justify-between items-center">
            <h3 class="text-lg font-semibold">📬 Messages</h3>
            <a href="{{ route('messages.create') }}?recipient_id={{ $client->user_id ?? '' }}" class="btn-primary text-xs">
                <i class="fas fa-plus mr-1"></i>New Message
            </a>
        </div>
        <div class="p-6">
            @if($messages->count() > 0)
                <div class="space-y-4">
                    @foreach($messages as $message)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center space-x-2">
                                    @if($message->sender_id === $client->user_id)
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">Sent</span>
                                        <span class="text-gray-700 dark:text-gray-300">To: {{ $message->recipient->name ?? 'Unknown' }}</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $message->is_read ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                            {{ $message->is_read ? 'Read' : 'Unread' }}
                                        </span>
                                        <span class="text-gray-700 dark:text-gray-300">From: {{ $message->sender->name ?? 'Unknown' }}</span>
                                    @endif
                                </div>
                                <div class="text-sm text-gray-400">
                                    {{ $message->created_at->format('M j, g:i A') }}
                                </div>
                            </div>
                            <div class="mb-2">
                                <h4 class="font-medium text-gray-900 dark:text-gray-100">{{ $message->subject ?? 'No Subject' }}</h4>
                            </div>
                            <div class="text-gray-700 dark:text-gray-300 text-sm mb-3">
                                {{ Str::limit($message->body, 150) }}
                            </div>
                            <div class="flex justify-end">
                                <a href="{{ route('messages.show', $message) }}" class="btn-secondary text-xs">
                                    <i class="fas fa-eye mr-1"></i>View Full Message
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <p class="text-gray-400 mb-4">No messages found for this client.</p>
                    <a href="{{ route('messages.create') }}?recipient_id={{ $client->user_id ?? '' }}" class="btn-primary">
                        <i class="fas fa-envelope mr-2"></i>Send First Message
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
