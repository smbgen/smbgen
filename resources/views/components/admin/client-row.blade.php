@props(['client'])

<tr class="hover:bg-gray-700 transition-colors" 
    data-client-email="{{ $client->email }}"
    data-client-name="{{ $client->name }}">
    <td class="px-4 py-4 w-12" onclick="event.stopPropagation()">
        <input type="checkbox" 
               class="client-email-checkbox w-4 h-4 text-blue-600 bg-gray-700 border-gray-600 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer"
               data-email="{{ $client->email }}"
               data-name="{{ $client->name }}"
               onchange="toggleClientEmail(this)">
    </td>
    <td class="px-6 py-4 cursor-pointer" onclick="window.location='{{ route('clients.show', $client) }}'">
        <div class="font-medium text-gray-100">{{ $client->name }}</div>
        @if($client->notes && !empty(trim($client->notes)))
            <div class="text-sm text-gray-400 max-w-xs truncate">{{ Str::limit(trim($client->notes), 50) }}</div>
        @endif
    </td>
    <td class="px-6 py-4 cursor-pointer" onclick="window.location='{{ route('clients.show', $client) }}'">
        <div class="text-gray-100">{{ $client->email }}</div>
    </td>
    <td class="px-6 py-4 cursor-pointer" onclick="window.location='{{ route('clients.show', $client) }}'">
        <div class="text-gray-100">{{ $client->phone ?? 'N/A' }}</div>
    </td>
    <td class="px-6 py-4 cursor-pointer" onclick="window.location='{{ route('clients.show', $client) }}'">
        @if($client->source_site)
            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                {{ $client->source_site }}
            </span>
        @else
            <span class="text-gray-400">Manual Entry</span>
        @endif
    </td>
    <td class="px-6 py-4 cursor-pointer" onclick="window.location='{{ route('clients.show', $client) }}'">
        <div class="text-gray-100">{{ $client->created_at->format('M j, Y') }}</div>
        <div class="text-sm text-gray-400">{{ $client->created_at->format('g:i A') }}</div>
    </td>
    @if(config('business.features.file_management'))
        <td class="px-6 py-4" onclick="event.stopPropagation()">
            <a href="{{ route('admin.client.files', $client) }}" class="inline-flex items-center text-sm text-gray-300 hover:text-blue-400 transition-colors group">
                <svg class="w-4 h-4 mr-1.5 group-hover:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                </svg>
                <span class="font-medium">{{ $client->files_count ?? 0 }}</span>
                <span class="ml-1">{{ Str::plural('file', $client->files_count ?? 0) }}</span>
            </a>
        </td>
    @endif
    <td class="px-6 py-4" onclick="event.stopPropagation()">
        <div class="flex items-center justify-end space-x-2">
            <a href="{{ route('clients.show', $client) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-blue-400 hover:text-blue-300 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
                View
            </a>
            <a href="{{ route('clients.edit', $client) }}" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Edit
            </a>
            
            @if(!$client->user)
                <form action="{{ route('clients.provision', $client) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-green-400 hover:text-green-300 transition-colors" onclick="return confirm('Provision user account for {{ addslashes($client->name) }}? This will create a login and send a magic link to {{ addslashes($client->email) }}')">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        Provision
                    </button>
                </form>
            @else
                <span class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-full bg-green-900/30 text-green-400 border border-green-700" title="User account exists">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Active
                </span>
            @endif
            
            <form action="{{ route('clients.destroy', $client) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-red-400 hover:text-red-300 transition-colors" onclick="return confirm('Are you sure you want to delete this client?')">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Delete
                </button>
            </form>
        </div>
    </td>
</tr>
