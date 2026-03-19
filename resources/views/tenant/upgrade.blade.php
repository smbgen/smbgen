@extends('layouts.tenant')

@section('breadcrumb', 'Upgrade Plan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-white mb-2">Choose a Plan</h1>
        <p class="text-gray-400">Unlock more modules as your business grows.</p>
    </div>

    @php
        $plans = [
            'starter' => ['price' => '$49/mo', 'modules' => ['cast'],                                    'color' => 'border-gray-700'],
            'growth'  => ['price' => '$99/mo', 'modules' => ['cast', 'relay', 'signal'],                 'color' => 'border-blue-700'],
            'scale'   => ['price' => '$199/mo','modules' => ['cast', 'relay', 'signal', 'surge', 'vault'],'color' => 'border-violet-700'],
            'agency'  => ['price' => '$399/mo','modules' => ['cast', 'relay', 'signal', 'surge', 'vault', 'extreme'],'color' => 'border-amber-700'],
        ];
        $icons = ['cast' => '🌐', 'relay' => '📬', 'signal' => '📡', 'surge' => '⚡', 'vault' => '🔐', 'extreme' => '🚀'];
        $currentPlan = tenant('plan') ?? 'starter';
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($plans as $slug => $plan)
            <div class="rounded-xl border {{ $plan['color'] }} bg-gray-900 p-5 {{ $currentPlan === $slug ? 'ring-2 ring-violet-500' : '' }}">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-white font-bold text-lg capitalize">{{ $slug }}</h3>
                    @if($currentPlan === $slug)
                        <span class="text-xs text-violet-400 bg-violet-400/10 px-2 py-0.5 rounded-full">Current</span>
                    @endif
                </div>
                <p class="text-2xl font-bold text-white mb-4">{{ $plan['price'] }}</p>
                <ul class="space-y-2 mb-6">
                    @foreach($plan['modules'] as $mod)
                        <li class="flex items-center gap-2 text-sm text-gray-300">
                            <span>{{ $icons[$mod] }}</span>
                            <span class="uppercase font-medium">{{ $mod }}</span>
                        </li>
                    @endforeach
                </ul>
                @if($currentPlan !== $slug)
                    <button class="w-full py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition-colors">
                        Upgrade
                    </button>
                @else
                    <button disabled class="w-full py-2 bg-gray-800 text-gray-500 text-sm font-medium rounded-lg cursor-not-allowed">
                        Current Plan
                    </button>
                @endif
            </div>
        @endforeach
    </div>
</div>
@endsection
