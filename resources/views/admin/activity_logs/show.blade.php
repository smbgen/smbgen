<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Activity Log Details') }}
            </h2>
            <a href="{{ route('admin.activity-logs.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-md font-semibold text-xs text-gray-900 dark:text-gray-100 uppercase tracking-widest hover:bg-gray-300 dark:hover:bg-gray-600 focus:bg-gray-300 dark:focus:bg-gray-600 active:bg-gray-400 dark:active:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                Back to Logs
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <!-- Action Badge -->
                    <div class="mb-6">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $activityLog->action_color }}">
                            <span class="mr-2 text-lg">{{ $activityLog->action_icon }}</span>
                            {{ $activityLog->formatted_action }}
                        </span>
                    </div>

                    <!-- Description -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">Description</h3>
                        <p class="text-gray-700 dark:text-gray-300">{{ $activityLog->description }}</p>
                    </div>

                    <!-- Details Grid -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <!-- User -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">User</h4>
                            @if($activityLog->user)
                                <p class="text-gray-900 dark:text-gray-100 font-medium">{{ $activityLog->user->name }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $activityLog->user->email }}</p>
                            @else
                                <p class="text-gray-500 dark:text-gray-400">System</p>
                            @endif
                        </div>

                        <!-- Timestamp -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Timestamp</h4>
                            <p class="text-gray-900 dark:text-gray-100">{{ $activityLog->created_at->format('F d, Y') }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $activityLog->created_at->format('h:i:s A') }}</p>
                            <p class="text-xs text-gray-400 dark:text-gray-500">{{ $activityLog->created_at->diffForHumans() }}</p>
                        </div>

                        <!-- IP Address -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">IP Address</h4>
                            <p class="text-gray-900 dark:text-gray-100 font-mono">{{ $activityLog->ip_address ?? 'N/A' }}</p>
                        </div>

                        <!-- Related Model -->
                        @if($activityLog->model_type)
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Related To</h4>
                                <p class="text-gray-900 dark:text-gray-100">{{ class_basename($activityLog->model_type) }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">ID: {{ $activityLog->model_id }}</p>
                            </div>
                        @endif
                    </div>

                    <!-- User Agent -->
                    @if($activityLog->user_agent)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">User Agent</h4>
                            <p class="text-sm text-gray-700 dark:text-gray-300 font-mono break-all">{{ $activityLog->user_agent }}</p>
                        </div>
                    @endif

                    <!-- Properties (Metadata) -->
                    @if($activityLog->properties && count($activityLog->properties) > 0)
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Additional Details</h4>
                            <div class="bg-gray-100 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                                <dl class="space-y-2">
                                    @foreach($activityLog->properties as $key => $value)
                                        <div class="flex">
                                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 w-1/3">{{ ucwords(str_replace('_', ' ', $key)) }}:</dt>
                                            <dd class="text-sm text-gray-900 dark:text-gray-100 w-2/3">
                                                @if(is_array($value))
                                                    <pre class="text-xs font-mono">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </dd>
                                        </div>
                                    @endforeach
                                </dl>
                            </div>
                        </div>
                    @endif

                    <!-- Actions -->
                    <div class="flex justify-end gap-2">
                        <form method="POST" action="{{ route('admin.activity-logs.destroy', $activityLog) }}" onsubmit="return confirm('Are you sure you want to delete this activity log?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:bg-red-700 active:bg-red-900 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Delete Log
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
