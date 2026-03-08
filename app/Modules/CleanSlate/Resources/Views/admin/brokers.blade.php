@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.cleanslate.index') }}" class="text-gray-500 hover:text-gray-300 transition-colors text-sm">
        <i class="fas fa-arrow-left text-xs"></i> Back
    </a>
    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Data Brokers</h1>
    <span class="text-xs text-gray-500">{{ $brokers->count() }} total</span>
</div>

@if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-lg text-sm font-medium bg-green-500/10 text-green-400 border border-green-500/20">
        {{ session('success') }}
    </div>
@endif

<div class="bg-gray-800/50 border border-gray-700 rounded-xl overflow-hidden">
    <div class="px-5 py-3 border-b border-gray-700 grid grid-cols-12 gap-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">
        <div class="col-span-4">Broker</div>
        <div class="col-span-3">Domain</div>
        <div class="col-span-2 text-center">Tier</div>
        <div class="col-span-2 text-center">Status</div>
        <div class="col-span-1"></div>
    </div>

    <div class="divide-y divide-gray-700/50">
        @foreach($brokers as $broker)
        <form action="{{ route('admin.cleanslate.brokers.update', $broker) }}" method="POST"
              class="grid grid-cols-12 gap-4 items-center px-5 py-3 hover:bg-gray-700/30 transition-colors">
            @csrf
            @method('PATCH')

            <div class="col-span-4">
                <p class="text-sm font-medium text-white">{{ $broker->name }}</p>
            </div>

            <div class="col-span-3">
                <p class="text-xs text-gray-500">{{ $broker->domain }}</p>
            </div>

            <div class="col-span-2 text-center">
                <select name="tier" class="px-2 py-1 bg-gray-900 border border-gray-600 rounded text-white text-xs focus:outline-none focus:border-primary-500">
                    <option value="1" {{ $broker->tier === 1 ? 'selected' : '' }}>Basic</option>
                    <option value="2" {{ $broker->tier === 2 ? 'selected' : '' }}>Pro</option>
                    <option value="3" {{ $broker->tier === 3 ? 'selected' : '' }}>Executive</option>
                </select>
            </div>

            <div class="col-span-2 text-center">
                <select name="active" class="px-2 py-1 bg-gray-900 border border-gray-600 rounded text-white text-xs focus:outline-none focus:border-primary-500">
                    <option value="1" {{ $broker->active ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ ! $broker->active ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <div class="col-span-1 text-right">
                <button type="submit" class="text-xs text-primary-400 hover:text-primary-300 font-medium transition-colors">Save</button>
            </div>
        </form>
        @endforeach
    </div>
</div>
@endsection
