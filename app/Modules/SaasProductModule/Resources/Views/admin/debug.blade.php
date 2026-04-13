@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">SaaS Product Module — Debug</h1>
        <p class="text-xs text-gray-500 mt-1">Routes, environment, database stats, and quick links</p>
    </div>
    <a href="{{ route('admin.saasproductmodule.index') }}" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">
        <i class="fas fa-arrow-left text-xs mr-1"></i> Back
    </a>
</div>

{{-- ── QUICK LINKS ─────────────────────────────────────────────────────────── --}}
<div class="mb-8">
    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Quick Links</h2>
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
        @foreach([
            ['label' => 'Landing Page',       'route' => 'saas-product-module',                    'icon' => 'fa-globe',          'color' => 'text-cyan-400'],
            ['label' => 'Pricing (public)',    'route' => 'saasproductmodule.billing.plans',       'icon' => 'fa-tag',            'color' => 'text-cyan-400'],
            ['label' => 'Intake Form',         'route' => 'saas-product-module',                    'icon' => 'fa-file-lines',     'color' => 'text-cyan-400', 'hash' => '#intake'],
            ['label' => 'Customer Entry',      'route' => 'saasproductmodule.entry',               'icon' => 'fa-door-open',      'color' => 'text-violet-400'],
            ['label' => 'Onboarding Step 1',   'route' => 'saasproductmodule.onboarding.profile',  'icon' => 'fa-user',           'color' => 'text-violet-400'],
            ['label' => 'Onboarding Step 2',   'route' => 'saasproductmodule.onboarding.contact',  'icon' => 'fa-envelope',       'color' => 'text-violet-400'],
            ['label' => 'Onboarding Step 3',   'route' => 'saasproductmodule.onboarding.addresses','icon' => 'fa-map-marker-alt', 'color' => 'text-violet-400'],
            ['label' => 'Onboarding Step 4',   'route' => 'saasproductmodule.onboarding.confirm',  'icon' => 'fa-check-circle',   'color' => 'text-violet-400'],
            ['label' => 'Customer Dashboard',  'route' => 'saasproductmodule.dashboard',           'icon' => 'fa-shield-halved',  'color' => 'text-violet-400'],
            ['label' => 'Admin: Customers',    'route' => 'admin.saasproductmodule.index',         'icon' => 'fa-users',          'color' => 'text-primary-400'],
            ['label' => 'Admin: Brokers',      'route' => 'admin.saasproductmodule.brokers',       'icon' => 'fa-database',       'color' => 'text-primary-400'],
            ['label' => 'Admin: Debug',        'route' => 'admin.saasproductmodule.debug',         'icon' => 'fa-bug',            'color' => 'text-primary-400'],
        ] as $link)
        @php $url = route($link['route']) . ($link['hash'] ?? ''); @endphp
        <a href="{{ $url }}" target="_blank"
           class="flex items-center gap-3 p-3 bg-gray-800/50 border border-gray-700 rounded-xl hover:border-gray-600 transition-all group">
            <i class="fas {{ $link['icon'] }} {{ $link['color'] }} text-sm w-4 text-center"></i>
            <div class="min-w-0">
                <p class="text-xs font-medium text-white truncate">{{ $link['label'] }}</p>
                <p class="text-[10px] text-gray-600 truncate">{{ $url }}</p>
            </div>
            <i class="fas fa-arrow-up-right-from-square text-[9px] text-gray-600 group-hover:text-gray-400 ml-auto flex-shrink-0"></i>
        </a>
        @endforeach
    </div>
</div>

{{-- ── DB STATS ─────────────────────────────────────────────────────────────── --}}
<div class="mb-8">
    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Database</h2>
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        @foreach([
            ['label' => 'Profiles',          'value' => $dbStats['profiles'],          'color' => 'text-white'],
            ['label' => 'Brokers (total)',    'value' => $dbStats['brokers_total'],     'color' => 'text-white'],
            ['label' => 'Brokers (active)',   'value' => $dbStats['brokers_active'],    'color' => 'text-cyan-400'],
            ['label' => 'Scan Jobs',          'value' => $dbStats['scan_jobs'],         'color' => 'text-white'],
            ['label' => 'Removal Requests',   'value' => $dbStats['removal_requests'],  'color' => 'text-white'],
        ] as $stat)
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl p-4 text-center">
            <p class="text-2xl font-extrabold {{ $stat['color'] }}">{{ $stat['value'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>
</div>

{{-- ── ENV / CONFIG ─────────────────────────────────────────────────────────── --}}
<div class="mb-8">
    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Environment</h2>
    <div class="bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden">
        @foreach($envValues as $key => $value)
        <div class="flex items-center justify-between px-5 py-2.5 border-b border-gray-700/50 last:border-0">
            <span class="text-xs font-mono text-gray-400">{{ $key }}</span>
            @if($value)
                <span class="text-xs font-mono text-cyan-400">{{ $value }}</span>
            @else
                <span class="text-xs font-mono text-red-400/70 flex items-center gap-1.5">
                    <i class="fas fa-triangle-exclamation text-[10px]"></i> not set
                </span>
            @endif
        </div>
        @endforeach
    </div>
</div>

{{-- ── BROKER TIERS ─────────────────────────────────────────────────────────── --}}
<div class="mb-8">
    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Brokers by Tier</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @foreach([1 => 'Basic', 2 => 'Professional', 3 => 'Executive'] as $tier => $label)
        <div class="bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden">
            <div class="px-4 py-3 border-b border-gray-700 flex items-center justify-between">
                <span class="text-xs font-semibold text-white">Tier {{ $tier }} — {{ $label }}</span>
                <span class="text-xs text-gray-500">{{ ($brokersByTier[$tier] ?? collect())->count() }} brokers</span>
            </div>
            <div class="px-4 py-2 divide-y divide-gray-700/40">
                @forelse($brokersByTier[$tier] ?? [] as $broker)
                <div class="py-2 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-white">{{ $broker->name }}</p>
                        <p class="text-[10px] text-gray-600">{{ $broker->domain }}</p>
                    </div>
                    @if(! $broker->active)
                        <span class="text-[10px] text-red-400/70 font-medium">inactive</span>
                    @endif
                </div>
                @empty
                <p class="py-3 text-xs text-gray-600">None</p>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- ── ROUTES ───────────────────────────────────────────────────────────────── --}}
<div>
    <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-3">Routes ({{ $routes->count() }})</h2>
    <div class="bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden">
        <div class="grid grid-cols-12 gap-2 px-4 py-2 border-b border-gray-700 text-[10px] font-semibold text-gray-500 uppercase tracking-wider">
            <div class="col-span-2">Method</div>
            <div class="col-span-3">URI</div>
            <div class="col-span-2">Name</div>
            <div class="col-span-2">Middleware</div>
            <div class="col-span-3">Action</div>
        </div>
        <div class="divide-y divide-gray-700/40">
            @foreach($routes->sortBy('uri') as $route)
            @php
                $isAdmin  = str_starts_with($route['name'] ?? '', 'admin.saasproductmodule.');
                $isPublic = in_array($route['name'], ['saas-product-module', 'saas-product-module.intake', 'saasproductmodule.billing.plans']);
            @endphp
            <div class="grid grid-cols-12 gap-2 px-4 py-2.5 items-start hover:bg-gray-700/20 transition-colors">
                <div class="col-span-2">
                    @foreach(explode('|', $route['methods']) as $method)
                        @if(! in_array($method, ['HEAD']))
                        <span class="inline-block text-[10px] font-bold mr-1
                            {{ $method === 'GET'   ? 'text-cyan-400' :
                               ($method === 'POST'  ? 'text-green-400' :
                               ($method === 'PATCH' ? 'text-yellow-400' :
                               ($method === 'DELETE'? 'text-red-400' : 'text-gray-400'))) }}">
                            {{ $method }}
                        </span>
                        @endif
                    @endforeach
                </div>
                <div class="col-span-3">
                    <code class="text-[10px] text-gray-300 break-all">/{{ $route['uri'] }}</code>
                    @if($isPublic)
                        <span class="ml-1 text-[9px] text-cyan-500 font-medium">public</span>
                    @elseif($isAdmin)
                        <span class="ml-1 text-[9px] text-primary-400 font-medium">admin</span>
                    @endif
                </div>
                <div class="col-span-2">
                    <code class="text-[10px] text-violet-400 break-all">{{ $route['name'] ?? '—' }}</code>
                </div>
                <div class="col-span-2">
                    <p class="text-[10px] text-gray-500 break-all">{{ $route['middleware'] ?: '—' }}</p>
                </div>
                <div class="col-span-3">
                    @php $action = class_basename(explode('@', $route['action'])[0] ?? '') . '@' . (explode('@', $route['action'])[1] ?? ''); @endphp
                    <code class="text-[10px] text-gray-500 break-all">{{ $action }}</code>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
