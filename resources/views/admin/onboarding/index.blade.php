@extends('layouts.admin')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">

    <div class="mb-8">
        <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">Integrations & Services</h2>
        <p class="text-gray-600 dark:text-gray-400">Connect the external services that power your platform.</p>
    </div>

    <!-- Progress Summary -->
    <div class="mb-8 rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900/50 p-5 flex flex-col sm:flex-row sm:items-center gap-4">
        <div class="flex-1">
            <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                {{ $connectedCount }} of {{ count($services) }} services connected
            </p>
            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                <div
                    class="bg-green-500 h-2 rounded-full transition-all duration-500"
                    style="width: {{ count($services) > 0 ? round(($connectedCount / count($services)) * 100) : 0 }}%"
                ></div>
            </div>
        </div>
        <div class="flex gap-6 text-sm shrink-0">
            <div class="flex items-center gap-2">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-green-500"></span>
                <span class="text-gray-600 dark:text-gray-400">Connected</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-block w-2.5 h-2.5 rounded-full bg-amber-400"></span>
                <span class="text-gray-600 dark:text-gray-400">Not configured</span>
            </div>
        </div>
    </div>

    <!-- Service Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        @foreach($services as $service)
            @php
                $colorMap = [
                    'red'    => ['icon_bg' => 'bg-red-100 dark:bg-red-900/30',    'icon_text' => 'text-red-600 dark:text-red-400'],
                    'green'  => ['icon_bg' => 'bg-green-100 dark:bg-green-900/30',  'icon_text' => 'text-green-600 dark:text-green-400'],
                    'blue'   => ['icon_bg' => 'bg-blue-100 dark:bg-blue-900/30',   'icon_text' => 'text-blue-600 dark:text-blue-400'],
                    'indigo' => ['icon_bg' => 'bg-indigo-100 dark:bg-indigo-900/30','icon_text' => 'text-indigo-600 dark:text-indigo-400'],
                    'purple' => ['icon_bg' => 'bg-purple-100 dark:bg-purple-900/30','icon_text' => 'text-purple-600 dark:text-purple-400'],
                    'orange' => ['icon_bg' => 'bg-orange-100 dark:bg-orange-900/30','icon_text' => 'text-orange-600 dark:text-orange-400'],
                    'teal'   => ['icon_bg' => 'bg-teal-100 dark:bg-teal-900/30',   'icon_text' => 'text-teal-600 dark:text-teal-400'],
                ];
                $colors = $colorMap[$service['color']] ?? $colorMap['blue'];
            @endphp
            <div class="rounded-xl border {{ $service['connected'] ? 'border-green-200 dark:border-green-800/50' : 'border-gray-200 dark:border-gray-700' }} bg-white dark:bg-gray-900/50 p-5 flex flex-col gap-4">

                <!-- Header row -->
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg {{ $colors['icon_bg'] }} flex items-center justify-center shrink-0">
                            <i class="{{ $service['icon'] }} {{ $colors['icon_text'] }} text-lg"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-white leading-tight">{{ $service['name'] }}</h3>
                        </div>
                    </div>
                    @if($service['connected'])
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-green-100 dark:bg-green-900/30 px-2.5 py-1 text-xs font-medium text-green-700 dark:text-green-400 shrink-0">
                            <i class="fas fa-check-circle text-xs"></i> Connected
                        </span>
                    @else
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 dark:bg-amber-900/30 px-2.5 py-1 text-xs font-medium text-amber-700 dark:text-amber-400 shrink-0">
                            <i class="fas fa-exclamation-circle text-xs"></i> Not configured
                        </span>
                    @endif
                </div>

                <!-- Description -->
                <p class="text-sm text-gray-600 dark:text-gray-400 leading-relaxed">{{ $service['description'] }}</p>

                <!-- Env keys -->
                <div class="flex flex-wrap gap-1.5">
                    @foreach($service['env_keys'] as $key)
                        <code class="text-xs rounded bg-gray-100 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 px-1.5 py-0.5 text-gray-600 dark:text-gray-400 font-mono">{{ $key }}</code>
                    @endforeach
                </div>

                <!-- Action buttons -->
                <div class="flex gap-2 mt-auto pt-1">
                    <a href="{{ route($service['config_route']) }}"
                       class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-sliders-h text-xs"></i> Configure
                    </a>
                    @if($service['manage_route'] && \Route::has($service['manage_route']))
                        <a href="{{ route($service['manage_route']) }}"
                           class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                            <i class="fas fa-arrow-right text-xs"></i> Manage
                        </a>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Help note -->
    <p class="mt-8 text-xs text-gray-500 dark:text-gray-500">
        Environment variables are set in your <code class="rounded bg-gray-100 dark:bg-gray-800 px-1">.env</code> file or via
        <a href="{{ route('admin.environment_settings.index') }}" class="text-blue-600 dark:text-blue-400 hover:underline">System Settings</a>.
    </p>

</div>
@endsection
