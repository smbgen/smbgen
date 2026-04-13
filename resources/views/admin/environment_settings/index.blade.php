@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto py-6">

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info">
            {{ session('info') }}
        </div>
    @endif

    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Environment Settings</h1>
            <p class="admin-page-subtitle">Configure application settings. Values set in .env file take precedence over database values.</p>
        </div>
    </div>

    <!-- Legend -->
    <div class="mb-6 flex gap-4 text-xs">
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 bg-blue-500 rounded"></div>
            <span class="text-gray-600 dark:text-gray-400">Set via .env (read-only)</span>
        </div>
        <div class="flex items-center gap-2">
            <div class="w-3 h-3 bg-gray-600 rounded"></div>
            <span class="text-gray-600 dark:text-gray-400">Editable (no .env override)</span>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.environment_settings.update') }}" class="space-y-8">
        @csrf
        @method('PATCH')

        @foreach($settings as $categoryKey => $category)
            <!-- {{ $category['title'] }} -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div>
                        <h3 class="admin-card-title">{{ $category['title'] }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $category['description'] }}</p>
                    </div>
                </div>
                <div class="admin-card-body">
                    <div class="space-y-6">
                    @foreach($category['items'] as $key => $setting)
                        @php
                            $isEnvControlled = !is_null($setting['env_value']);
                            $isReadonly = $isEnvControlled || ($setting['readonly'] ?? false);
                            $displayValue = $isEnvControlled ? $setting['env_value'] : ($setting['db_value'] ?? $setting['current_value']);
                        @endphp
                        <div class="form-group">
                            <label for="{{ $categoryKey }}_{{ $key }}" class="form-label flex items-center gap-2">
                                {{ $setting['label'] }}
                                @if($isEnvControlled)
                                    <span class="text-xs px-2 py-0.5 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300 rounded border border-blue-300 dark:border-blue-800">
                                        .env: {{ $setting['env_key'] }}
                                    </span>
                                @endif
                            </label>
                            
                            @if($setting['type'] === 'boolean')
                                <label class="flex items-center gap-3 p-3 bg-gray-100 dark:bg-gray-700/30 rounded-lg border border-gray-300 dark:border-gray-600 cursor-pointer hover:bg-gray-200 dark:hover:bg-gray-700/50 transition-colors">
                                    <input 
                                        type="checkbox" 
                                        id="{{ $categoryKey }}_{{ $key }}" 
                                        name="feature_{{ $key }}" 
                                        value="1"
                                        {{ $displayValue ? 'checked' : '' }}
                                        {{ $isReadonly ? 'disabled' : '' }}
                                        class="form-checkbox {{ $isReadonly ? 'cursor-not-allowed opacity-60' : '' }}"
                                    >
                                    <div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-200">Enable {{ $setting['label'] }}</span>
                                        @if(isset($setting['description']))
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $setting['description'] }}</p>
                                        @endif
                                    </div>
                                </label>
                            @else
                                <input 
                                    id="{{ $categoryKey }}_{{ $key }}" 
                                    type="{{ $setting['type'] }}" 
                                    name="{{ $key }}" 
                                    value="{{ $displayValue }}" 
                                    {{ $isReadonly ? 'readonly' : '' }}
                                    class="form-input {{ $isReadonly ? 'cursor-not-allowed bg-gray-100 dark:bg-gray-700/30 text-gray-600 dark:text-gray-400' : '' }}"
                                >
                                @if(isset($setting['description']))
                                    <p class="form-help text-gray-600 dark:text-gray-400">
                                        {{ $setting['description'] }}
                                    </p>
                                @endif
                            @endif
                            
                            @if($isEnvControlled)
                                <p class="form-help text-blue-700 dark:text-blue-400 text-xs mt-1">
                                    🔒 Controlled by .env file - cannot be changed via UI
                                </p>
                            @elseif($setting['readonly'] ?? false)
                                <p class="form-help text-gray-600 dark:text-gray-500 text-xs mt-1">
                                    ℹ️ Read-only setting
                                </p>
                            @endif
                        </div>
                    @endforeach
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Information Notice -->
        <div class="alert alert-warning">
            <div class="flex items-start">
                <svg class="h-5 w-5 text-yellow-400 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div>
                    <h4 class="text-sm font-medium text-yellow-800 dark:text-yellow-300 mb-1">How Environment Settings Work</h4>
                    <ul class="text-sm text-yellow-900 dark:text-yellow-200/80 space-y-1 list-disc list-inside">
                        <li>Settings with values in the <code class="bg-yellow-100 dark:bg-gray-700 px-1 rounded">.env</code> file <strong>always take precedence</strong> and cannot be changed here.</li>
                        <li>Settings without .env values can be configured in the database via this form.</li>
                        <li>To make a setting editable, remove it from your <code class="bg-yellow-100 dark:bg-gray-700 px-1 rounded">.env</code> file.</li>
                        <li>To lock a setting, add it to your <code class="bg-yellow-100 dark:bg-gray-700 px-1 rounded">.env</code> file with your desired value.</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="action-buttons pt-2">
            <button type="submit" class="btn-primary">
                <i class="fas fa-save mr-2"></i>Save Editable Settings
            </button>
        </div>
    </form>

</div>
@endsection
