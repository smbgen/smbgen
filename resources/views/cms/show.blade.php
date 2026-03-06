@extends('layouts.cms')

@push('head')
    {{-- Inject custom head content if present --}}
    @if($page->head_content)
        {!! $page->sanitizedHeadContent() !!}
    @endif
@endpush

@section('content')
    <div class="min-h-screen {{ $page->safeBackgroundClass() }} {{ $page->safeTextClass() }}">
        {!! $page->sanitizedBodyContent() !!}
        
        @if($page->cta_text && $page->cta_url)
            <a href="{{ $page->cta_url }}">
                {{ $page->cta_text }}
            </a>
        @endif

        {{-- Dynamic Form Rendering --}}
        @if($page->has_form && !empty($page->form_fields))
            <div class="max-w-2xl mx-auto mt-12 px-4">
                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                        <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                        <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('cms.form.submit', $page->slug) }}" method="POST" class="space-y-6 bg-white dark:bg-gray-800 p-8 rounded-lg shadow-lg">
                    @csrf

                    @foreach($page->form_fields as $field)
                        <div>
                            <label for="{{ $field['name'] }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                {{ $field['label'] ?? ucfirst($field['name']) }}
                                @if($field['required'] ?? false)
                                    <span class="text-red-500">*</span>
                                @endif
                            </label>

                            @if($field['type'] === 'textarea')
                                <textarea 
                                    name="{{ $field['name'] }}" 
                                    id="{{ $field['name'] }}" 
                                    rows="4"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                    {{ ($field['required'] ?? false) ? 'required' : '' }}
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error($field['name']) border-red-500 @enderror"
                                >{{ old($field['name']) }}</textarea>

                            @elseif($field['type'] === 'select')
                                <select 
                                    name="{{ $field['name'] }}" 
                                    id="{{ $field['name'] }}"
                                    {{ ($field['required'] ?? false) ? 'required' : '' }}
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error($field['name']) border-red-500 @enderror"
                                >
                                    <option value="">Select...</option>
                                    @if(!empty($field['options']))
                                        @foreach(explode(',', $field['options']) as $option)
                                            <option value="{{ trim($option) }}" {{ old($field['name']) === trim($option) ? 'selected' : '' }}>
                                                {{ trim($option) }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>

                            @elseif($field['type'] === 'radio')
                                <div class="space-y-2">
                                    @if(!empty($field['options']))
                                        @foreach(explode(',', $field['options']) as $option)
                                            <label class="flex items-center">
                                                <input 
                                                    type="radio" 
                                                    name="{{ $field['name'] }}" 
                                                    value="{{ trim($option) }}"
                                                    {{ old($field['name']) === trim($option) ? 'checked' : '' }}
                                                    {{ ($field['required'] ?? false) ? 'required' : '' }}
                                                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                                >
                                                <span class="ml-2 text-gray-700 dark:text-gray-300">{{ trim($option) }}</span>
                                            </label>
                                        @endforeach
                                    @endif
                                </div>

                            @elseif($field['type'] === 'checkbox')
                                <div class="flex items-center">
                                    <input 
                                        type="checkbox" 
                                        name="{{ $field['name'] }}" 
                                        id="{{ $field['name'] }}"
                                        value="1"
                                        {{ old($field['name']) ? 'checked' : '' }}
                                        {{ ($field['required'] ?? false) ? 'required' : '' }}
                                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                    >
                                    <label for="{{ $field['name'] }}" class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                                        {{ $field['placeholder'] ?? $field['label'] ?? ucfirst($field['name']) }}
                                    </label>
                                </div>

                            @else
                                <input 
                                    type="{{ $field['type'] }}" 
                                    name="{{ $field['name'] }}" 
                                    id="{{ $field['name'] }}"
                                    value="{{ old($field['name']) }}"
                                    placeholder="{{ $field['placeholder'] ?? '' }}"
                                    {{ ($field['required'] ?? false) ? 'required' : '' }}
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error($field['name']) border-red-500 @enderror"
                                >
                            @endif

                            @error($field['name'])
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach

                    <div class="pt-4">
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition duration-200 shadow-lg hover:shadow-xl">
                            {{ $page->form_submit_button_text ?? 'Submit' }}
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
@endsection
