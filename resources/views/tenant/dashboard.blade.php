@extends('layouts.tenant')

@section('breadcrumb', 'Overview')

@section('content')
<div class="max-w-6xl mx-auto space-y-8">

    {{-- Welcome --}}
    <div>
        <h1 class="text-2xl font-bold text-white">Welcome back, {{ auth()->user()->name }}</h1>
        <p class="text-gray-400 mt-1">{{ $tenant->name }} &mdash; {{ ucfirst($tenant->plan) }} plan</p>
    </div>

    {{-- Module grid --}}
    @php
        $moduleCards = [
            'signal' => [
                'icon'        => '📡',
                'label'       => 'SIGNAL',
                'description' => 'AI social media automation',
                'route'       => 'tenant.signal',
                'color'       => 'from-pink-600 to-rose-600',
                'stat_key'    => 'signal',
                'stat_label'  => fn($s) => ($s['published'] ?? 0) . ' published',
            ],
            'relay' => [
                'icon'        => '📬',
                'label'       => 'RELAY',
                'description' => 'Automated email sequences',
                'route'       => 'tenant.relay',
                'color'       => 'from-blue-600 to-indigo-600',
                'stat_key'    => 'relay',
                'stat_label'  => fn($s) => ($s['active'] ?? 0) . ' active sequences',
            ],
            'surge' => [
                'icon'        => '⚡',
                'label'       => 'SURGE',
                'description' => 'CRM & deal pipeline',
                'route'       => 'tenant.surge',
                'color'       => 'from-amber-600 to-orange-600',
                'stat_key'    => 'surge',
                'stat_label'  => fn($s) => '$' . number_format($s['pipeline'] ?? 0, 0) . ' pipeline',
            ],
            'cast' => [
                'icon'        => '🌐',
                'label'       => 'CAST',
                'description' => 'Websites & online presence',
                'route'       => 'tenant.cast',
                'color'       => 'from-emerald-600 to-teal-600',
                'stat_key'    => 'cast',
                'stat_label'  => fn($s) => ($s['live'] ?? 0) . ' sites live',
            ],
            'vault' => [
                'icon'        => '🔐',
                'label'       => 'VAULT',
                'description' => 'Backup & recovery',
                'route'       => 'tenant.vault',
                'color'       => 'from-gray-600 to-slate-600',
                'stat_key'    => null,
                'stat_label'  => fn($s) => 'Coming soon',
            ],
            'extreme' => [
                'icon'        => '🚀',
                'label'       => 'EXTREME',
                'description' => 'Custom deployments & dev tools',
                'route'       => 'tenant.extreme',
                'color'       => 'from-violet-600 to-purple-600',
                'stat_key'    => null,
                'stat_label'  => fn($s) => 'Coming soon',
            ],
        ];
    @endphp

    <div>
        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4">Your Modules</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">

            @foreach($moduleCards as $key => $card)
                @php $enabled = in_array($key, $modules); @endphp
                <div class="{{ $enabled ? 'cursor-pointer group' : 'opacity-40 cursor-not-allowed' }} relative rounded-xl border border-gray-800 bg-gray-900 overflow-hidden transition-all duration-200 {{ $enabled ? 'hover:border-gray-600 hover:shadow-lg hover:shadow-black/30' : '' }}">

                    {{-- Gradient bar --}}
                    <div class="h-1 w-full bg-gradient-to-r {{ $card['color'] }}"></div>

                    <div class="p-5">
                        <div class="flex items-start justify-between mb-3">
                            <span class="text-3xl">{{ $card['icon'] }}</span>
                            @if($enabled)
                                <span class="text-xs text-green-400 bg-green-400/10 px-2 py-0.5 rounded-full">Active</span>
                            @else
                                <span class="text-xs text-gray-600 bg-gray-800 px-2 py-0.5 rounded-full">Locked</span>
                            @endif
                        </div>

                        <h3 class="text-white font-bold text-lg">{{ $card['label'] }}</h3>
                        <p class="text-gray-400 text-sm mt-0.5">{{ $card['description'] }}</p>

                        @if($enabled && $card['stat_key'] && isset($stats[$card['stat_key']]))
                            <p class="text-gray-500 text-xs mt-3">{{ $card['stat_label']($stats[$card['stat_key']]) }}</p>
                        @endif

                        @if($enabled)
                            <a href="{{ route($card['route']) }}"
                               class="mt-4 flex items-center justify-between text-sm font-medium text-gray-300 group-hover:text-white transition-colors">
                                Open {{ $card['label'] }}
                                <span class="text-gray-600 group-hover:text-white transition-colors">→</span>
                            </a>
                        @else
                            <p class="mt-4 text-xs text-gray-600">Upgrade to unlock</p>
                        @endif
                    </div>
                </div>
            @endforeach

        </div>
    </div>

    {{-- Locked modules upgrade prompt --}}
    @php $lockedModules = array_diff(array_keys($moduleCards), $modules); @endphp
    @if(count($lockedModules) > 0)
        <div class="rounded-xl border border-violet-800/50 bg-violet-900/10 p-5 flex items-center justify-between gap-4">
            <div>
                <p class="text-white font-semibold text-sm">Unlock more modules</p>
                <p class="text-gray-400 text-xs mt-0.5">
                    {{ count($lockedModules) }} module{{ count($lockedModules) > 1 ? 's' : '' }} available on higher plans.
                </p>
            </div>
            <a href="{{ route('tenant.upgrade') }}" class="shrink-0 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition-colors">
                View Plans
            </a>
        </div>
    @endif

</div>
@endsection
