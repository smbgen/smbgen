@extends('layouts.admin')

@section('content')
<div class="py-6 space-y-6">

    {{-- Header --}}
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Review Package</h1>
            <p class="admin-page-subtitle">Confirm file classification for <strong class="text-gray-900 dark:text-gray-200">{{ $client->name }}</strong> before saving</p>
        </div>
        <a href="{{ route('admin.packages.create', ['client_id' => $client->id]) }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Re-upload
        </a>
    </div>

    <form method="POST" action="{{ route('admin.packages.store') }}">
        @csrf
        <input type="hidden" name="session_key" value="{{ $sessionKey }}">

        {{-- Package name --}}
        <div class="admin-card mb-6">
            <div class="admin-card-body">
                <div class="flex items-center gap-4">
                    <label class="text-sm font-medium text-gray-700 dark:text-gray-300 whitespace-nowrap">Package Name</label>
                    <input type="text" name="name" value="{{ old('name', $draft['name']) }}" required
                        class="flex-1 px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 text-lg font-semibold">
                </div>
            </div>
        </div>

        {{-- File classification table --}}
        <div class="admin-card mb-6">
            <div class="admin-card-header">
                <h3 class="admin-card-title">
                    <i class="fas fa-list-check mr-2 text-blue-400"></i>
                    {{ count($draft['files']) }} files detected
                </h3>
                <p class="text-xs text-gray-600 dark:text-gray-400">Review and correct the auto-classification, then save.</p>
            </div>
            <div class="admin-card-body p-0">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-gray-200 dark:border-gray-700">
                            <tr class="text-left text-xs text-gray-600 dark:text-gray-400 uppercase tracking-wider">
                                <th class="px-4 py-3">Filename</th>
                                <th class="px-4 py-3">Display Name</th>
                                <th class="px-4 py-3">Type</th>
                                <th class="px-4 py-3">Role</th>
                                <th class="px-4 py-3 text-right">Size</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700/50">
                            @foreach($draft['files'] as $i => $file)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        @php
                                            $typeIcon = match($file['type']) {
                                                'HTML_PRESENTATION' => 'fa-desktop text-purple-400',
                                                'HTML_EMAIL' => 'fa-envelope text-blue-400',
                                                'PDF_DOCUMENT' => 'fa-file-pdf text-red-400',
                                                'MARKDOWN_RESEARCH' => 'fa-file-alt text-green-400',
                                                'JSON_DATA' => 'fa-code text-yellow-400',
                                                'WORD_DOCUMENT' => 'fa-file-word text-blue-300',
                                                'POWERPOINT' => 'fa-file-powerpoint text-orange-400',
                                                default => 'fa-file text-gray-400',
                                            };
                                        @endphp
                                        <i class="fas {{ $typeIcon }}"></i>
                                        <span class="text-gray-800 dark:text-gray-200 font-mono text-xs">{{ $file['original_name'] }}</span>
                                    </div>
                                    @if($file['group_label'] ?? null)
                                        <div class="text-xs text-gray-600 dark:text-gray-500 mt-0.5 ml-5">
                                            <i class="fas fa-folder mr-1"></i>{{ $file['group_label'] }}
                                        </div>
                                    @endif
                                    <input type="hidden" name="files[{{ $i }}][original_name]" value="{{ $file['original_name'] }}">
                                    <input type="hidden" name="files[{{ $i }}][tmp_relative_path]" value="{{ $file['tmp_relative_path'] ?? '' }}">
                                    <input type="hidden" name="files[{{ $i }}][group_label]" value="{{ $file['group_label'] ?? '' }}">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="text" name="files[{{ $i }}][display_name]" value="{{ $file['display_name'] }}"
                                        class="w-full px-2 py-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded text-gray-900 dark:text-gray-100 text-sm focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </td>
                                <td class="px-4 py-3">
                                    <select name="files[{{ $i }}][type]"
                                        class="px-2 py-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded text-gray-900 dark:text-gray-100 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        @foreach($fileTypes as $ft)
                                            <option value="{{ $ft }}" @selected($file['type'] === $ft)>{{ $ft }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <select name="files[{{ $i }}][role]"
                                        class="px-2 py-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded text-gray-900 dark:text-gray-100 text-xs focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        @foreach($roles as $r)
                                            <option value="{{ $r }}" @selected($file['role'] === $r)>{{ $r }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3 text-right text-xs text-gray-600 dark:text-gray-400">
                                    @php
                                        $kb = round($file['size_bytes'] / 1024, 1);
                                        $display = $kb >= 1024 ? round($kb / 1024, 1).' MB' : $kb.' KB';
                                    @endphp
                                    {{ $display }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex justify-between items-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                <i class="fas fa-info-circle mr-1 text-blue-400"></i>
                Classification can be changed after saving from the package detail view.
            </p>
            <div class="flex gap-3">
                <a href="{{ route('admin.packages.create', ['client_id' => $client->id]) }}" class="btn-secondary">
                    Cancel
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Save Package
                </button>
            </div>
        </div>

    </form>
</div>
@endsection
