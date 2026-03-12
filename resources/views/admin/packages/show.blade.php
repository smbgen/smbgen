@extends('layouts.admin')

@push('styles')
<style>
.prose-dark h1,.prose-dark h2,.prose-dark h3,.prose-dark h4 {
    color: #f3f4f6; font-weight: 700; margin-top: 1.5em; margin-bottom: 0.5em; line-height: 1.3;
}
.prose-dark h1 { font-size: 1.75rem; border-bottom: 1px solid #374151; padding-bottom: 0.4em; }
.prose-dark h2 { font-size: 1.35rem; border-bottom: 1px solid #374151; padding-bottom: 0.3em; }
.prose-dark h3 { font-size: 1.1rem; }
.prose-dark p  { color: #d1d5db; line-height: 1.8; margin-bottom: 1em; }
.prose-dark a  { color: #60a5fa; text-decoration: underline; }
.prose-dark strong { color: #f9fafb; font-weight: 600; }
.prose-dark em { color: #e5e7eb; font-style: italic; }
.prose-dark code {
    background: #1f2937; color: #a5f3fc; padding: 0.15em 0.4em;
    border-radius: 4px; font-size: 0.875em; font-family: ui-monospace, monospace;
}
.prose-dark pre {
    background: #111827; border: 1px solid #374151; border-radius: 8px;
    padding: 1rem 1.25rem; overflow-x: auto; margin-bottom: 1.25em;
}
.prose-dark pre code { background: none; color: #a5f3fc; padding: 0; font-size: 0.85rem; }
.prose-dark blockquote {
    border-left: 3px solid #4b5563; padding-left: 1rem; color: #9ca3af;
    font-style: italic; margin: 1em 0;
}
.prose-dark ul,.prose-dark ol { color: #d1d5db; padding-left: 1.5rem; margin-bottom: 1em; }
.prose-dark ul { list-style-type: disc; }
.prose-dark ol { list-style-type: decimal; }
.prose-dark li { margin-bottom: 0.3em; line-height: 1.7; }
.prose-dark table {
    width: 100%; border-collapse: collapse; margin-bottom: 1.25em; font-size: 0.9rem;
}
.prose-dark th {
    background: #1f2937; color: #f3f4f6; font-weight: 600;
    padding: 0.6em 0.9em; text-align: left; border: 1px solid #374151;
}
.prose-dark td {
    color: #d1d5db; padding: 0.5em 0.9em;
    border: 1px solid #374151;
}
.prose-dark tr:nth-child(even) td { background: #1a2030; }
.prose-dark hr { border-color: #374151; margin: 1.5em 0; }
</style>
@endpush

@section('content')
<div class="py-6 space-y-6" x-data="packageDetail()">

    {{-- Header --}}
    <div class="flex justify-between items-start flex-wrap gap-4">
        <div>
            <div class="flex items-center gap-3 mb-1">
                <a href="{{ route('admin.packages.index') }}" class="text-gray-400 hover:text-gray-200 text-sm">
                    <i class="fas fa-arrow-left mr-1"></i>Packages
                </a>
                <span class="text-gray-600">/</span>
                <span class="text-gray-300 text-sm">{{ $package->client->name }}</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-100">{{ $package->name }}</h1>
        </div>
        <div class="flex items-center gap-3 flex-wrap">
            {{-- Status badge + changer --}}
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open"
                    class="text-xs px-3 py-1.5 rounded-full font-medium {{ $package->status_badge_class }} flex items-center gap-1">
                    {{ ucfirst($package->status) }}
                    <i class="fas fa-chevron-down text-xs"></i>
                </button>
                <div x-show="open" @click.outside="open = false"
                    class="absolute right-0 top-8 z-10 bg-gray-800 border border-gray-700 rounded-lg shadow-xl py-1 min-w-32">
                    @foreach(['draft', 'ready', 'sent'] as $s)
                        @if($s !== $package->status)
                        <form method="POST" action="{{ route('admin.packages.status', $package) }}">
                            @csrf @method('PATCH')
                            <input type="hidden" name="status" value="{{ $s }}">
                            <button type="submit" class="w-full text-left px-4 py-2 text-sm text-gray-200 hover:bg-gray-700">
                                Mark as {{ ucfirst($s) }}
                            </button>
                        </form>
                        @endif
                    @endforeach
                </div>
            </div>

            {{-- Portal toggle --}}
            <form method="POST" action="{{ route('admin.packages.toggle-portal', $package) }}">
                @csrf @method('PATCH')
                <button type="submit"
                    class="text-xs px-3 py-1.5 rounded-full font-medium border transition-colors
                        {{ $package->portal_enabled
                            ? 'bg-green-900/40 text-green-300 border-green-700 hover:bg-green-900/60'
                            : 'bg-gray-700 text-gray-300 border-gray-600 hover:bg-gray-600' }}">
                    <i class="fas fa-globe mr-1"></i>
                    {{ $package->portal_enabled ? 'Portal: On' : 'Portal: Off' }}
                </button>
            </form>
        </div>
    </div>

    {{-- Flash --}}
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Meta row --}}
    <div class="flex flex-wrap gap-6 text-sm text-gray-400">
        <span><i class="fas fa-user mr-1"></i>{{ $package->client->name }}</span>
        <span><i class="fas fa-calendar mr-1"></i>{{ $package->created_at->format('M j, Y') }}</span>
        <span><i class="fas fa-user-shield mr-1"></i>{{ $package->createdBy->name ?? 'Unknown' }}</span>
        <span><i class="fas fa-source mr-1"></i>{{ str_replace('_', ' ', $package->source) }}</span>
        @if($package->original_filename)
            <span><i class="fas fa-file-archive mr-1"></i>{{ $package->original_filename }}</span>
        @endif
    </div>

    {{-- Tabs --}}
    <div class="border-b border-gray-700">
        <nav class="flex gap-1 -mb-px">
            @php
                $tabs = [
                    'deliverables' => ['label' => 'Deliverables', 'count' => count($deliverables), 'icon' => 'fa-desktop', 'color' => 'purple'],
                    'research'     => ['label' => 'Research', 'count' => count($researchFiles), 'icon' => 'fa-flask', 'color' => 'green'],
                    'email'        => ['label' => 'Email', 'count' => count($emailTemplates), 'icon' => 'fa-envelope', 'color' => 'blue'],
                ];
            @endphp
            @foreach($tabs as $key => $tab)
            <button @click="activeTab = '{{ $key }}'"
                :class="activeTab === '{{ $key }}'
                    ? 'border-b-2 border-blue-500 text-blue-400'
                    : 'text-gray-400 hover:text-gray-200'"
                class="px-5 py-3 text-sm font-medium transition-colors flex items-center gap-2">
                <i class="fas {{ $tab['icon'] }}"></i>
                {{ $tab['label'] }}
                @if($tab['count'] > 0)
                    <span class="text-xs px-1.5 py-0.5 rounded-full bg-gray-700 text-gray-300">{{ $tab['count'] }}</span>
                @endif
            </button>
            @endforeach
        </nav>
    </div>

    {{-- DELIVERABLES TAB --}}
    <div x-show="activeTab === 'deliverables'">
        @if($deliverables->isEmpty())
            <div class="admin-card">
                <div class="admin-card-body text-center py-12 text-gray-500">
                    <i class="fas fa-desktop text-3xl mb-3"></i>
                    <p>No deliverables in this package.</p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($deliverables as $file)
                <div class="admin-card group">
                    <div class="admin-card-body">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center gap-2">
                                <i class="fas {{ $file->type_icon }} text-xl
                                    {{ $file->type === 'HTML_PRESENTATION' ? 'text-purple-400' : 'text-red-400' }}"></i>
                                <div>
                                    <h4 class="text-gray-100 font-medium leading-tight">{{ $file->display_name }}</h4>
                                    <p class="text-xs text-gray-500 font-mono">{{ $file->original_name }}</p>
                                </div>
                            </div>
                            {{-- Portal promoted toggle --}}
                            <button
                                @click="togglePromote({{ $file->id }}, $el)"
                                data-promoted="{{ $file->portal_promoted ? 'true' : 'false' }}"
                                title="{{ $file->portal_promoted ? 'Remove from portal' : 'Add to portal' }}"
                                class="text-xs px-2 py-1 rounded border transition-colors
                                    {{ $file->portal_promoted
                                        ? 'bg-green-900/40 text-green-300 border-green-700'
                                        : 'bg-gray-700 text-gray-400 border-gray-600 hover:border-green-700 hover:text-green-400' }}">
                                <i class="fas {{ $file->portal_promoted ? 'fa-globe' : 'fa-eye-slash' }} mr-1"></i>
                                {{ $file->portal_promoted ? 'Portal' : 'Hidden' }}
                            </button>
                        </div>

                        <div class="flex items-center justify-between text-xs text-gray-400 mb-3">
                            <span class="{{ $file->type_badge_class }} px-2 py-0.5 rounded text-xs">{{ $file->type }}</span>
                            <span>{{ $file->formatted_size }}</span>
                        </div>

                        <button @click="previewFile({{ $file->id }}, '{{ addslashes($file->display_name) }}', '{{ $file->type }}')"
                            class="w-full btn-secondary text-sm">
                            <i class="fas fa-eye mr-2"></i>Preview
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- RESEARCH TAB --}}
    <div x-show="activeTab === 'research'">
        @if($researchFiles->isEmpty())
            <div class="admin-card">
                <div class="admin-card-body text-center py-12 text-gray-500">
                    <i class="fas fa-flask text-3xl mb-3"></i>
                    <p>No research files in this package.</p>
                </div>
            </div>
        @else
            <div class="space-y-3">
                @foreach($researchFiles as $file)
                <div class="admin-card">
                    <div class="admin-card-body">
                        <div class="flex items-center justify-between flex-wrap gap-3">
                            <div class="flex items-center gap-3">
                                <i class="fas {{ $file->type_icon }} text-lg
                                    {{ $file->type === 'MARKDOWN_RESEARCH' ? 'text-green-400' : 'text-yellow-400' }}"></i>
                                <div>
                                    <h4 class="text-gray-100 font-medium flex items-center gap-2">
                                        {{ $file->display_name }}
                                        @if($file->isIndexFile())
                                            <span class="text-xs px-1.5 py-0.5 rounded bg-blue-900/40 text-blue-300 border border-blue-700">Index</span>
                                        @endif
                                    </h4>
                                    <p class="text-xs text-gray-500 font-mono">{{ $file->original_name }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="{{ $file->type_badge_class }} px-2 py-0.5 rounded text-xs">{{ $file->type }}</span>
                                <span class="text-xs text-gray-400">{{ $file->formatted_size }}</span>
                                @if($file->group_label)
                                    <span class="text-xs px-2 py-0.5 rounded bg-gray-700 text-gray-400 border border-gray-600">
                                        <i class="fas fa-folder mr-1"></i>{{ $file->group_label }}
                                    </span>
                                @endif
                                <button @click="previewFile({{ $file->id }}, '{{ addslashes($file->display_name) }}', '{{ $file->type }}')"
                                    class="btn-secondary text-xs">
                                    <i class="fas fa-eye mr-1"></i>View
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- EMAIL TAB --}}
    <div x-show="activeTab === 'email'">
        @if($emailTemplates->isEmpty())
            <div class="admin-card">
                <div class="admin-card-body text-center py-12 text-gray-500">
                    <i class="fas fa-envelope text-3xl mb-3"></i>
                    <p>No email templates in this package.</p>
                </div>
            </div>
        @else
            <div class="space-y-3">
                @foreach($emailTemplates as $file)
                <div class="admin-card">
                    <div class="admin-card-body flex items-center justify-between flex-wrap gap-3">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-envelope text-lg text-blue-400"></i>
                            <div>
                                <h4 class="text-gray-100 font-medium">{{ $file->display_name }}</h4>
                                <p class="text-xs text-gray-500 font-mono">{{ $file->original_name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-400">{{ $file->formatted_size }}</span>
                            <button @click="previewFile({{ $file->id }}, '{{ addslashes($file->display_name) }}', '{{ $file->type }}')"
                                class="btn-secondary text-xs">
                                <i class="fas fa-eye mr-1"></i>Preview
                            </button>
                            <a href="{{ route('admin.email.index', ['package_file_id' => $file->id, 'client_id' => $package->client_id]) }}"
                                class="btn-primary text-xs">
                                <i class="fas fa-paper-plane mr-1"></i>Use in Compose
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>

{{-- Preview Modal --}}
<div x-show="previewOpen" x-cloak
    class="fixed inset-0 z-50 flex flex-col bg-gray-950/95"
    @keydown.escape.window="previewOpen = false">

    {{-- Modal toolbar --}}
    <div class="flex items-center justify-between px-4 py-3 bg-gray-900 border-b border-gray-700 shrink-0">
        <div class="flex items-center gap-3">
            <button @click="previewOpen = false"
                class="flex items-center gap-2 text-sm text-gray-400 hover:text-gray-100 px-3 py-1.5 rounded-lg hover:bg-gray-700 transition-colors border border-gray-700 hover:border-gray-500">
                <i class="fas fa-arrow-left"></i>
                Back
            </button>
            <span class="text-gray-100 font-medium" x-text="previewTitle"></span>
            <span class="text-xs px-2 py-0.5 rounded bg-gray-700 text-gray-400 border border-gray-600" x-text="previewType"></span>
        </div>
        <div class="flex items-center gap-2">
            <a :href="previewUrl" target="_blank"
                x-show="previewType === 'HTML_PRESENTATION' || previewType === 'HTML_EMAIL' || previewType === 'PDF_DOCUMENT'"
                class="flex items-center gap-2 text-sm text-gray-400 hover:text-gray-100 px-3 py-1.5 rounded-lg hover:bg-gray-700 transition-colors border border-gray-700 hover:border-gray-500">
                <i class="fas fa-external-link-alt"></i>
                Open in tab
            </a>
            <a :href="downloadUrl" download
                x-show="previewType === 'WORD_DOCUMENT' || previewType === 'POWERPOINT'"
                class="flex items-center gap-2 text-sm text-gray-400 hover:text-gray-100 px-3 py-1.5 rounded-lg hover:bg-gray-700 transition-colors border border-gray-700 hover:border-gray-500">
                <i class="fas fa-download"></i>
                Download
            </a>
            <button @click="previewOpen = false"
                class="text-gray-400 hover:text-gray-100 p-2 rounded-lg hover:bg-gray-700 transition-colors"
                title="Close (Esc)">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>
    </div>

    {{-- HTML iframe (sandboxed) --}}
    <template x-if="previewType === 'HTML_PRESENTATION' || previewType === 'HTML_EMAIL'">
        <iframe :src="previewUrl"
            class="flex-1 w-full border-0"
            sandbox="allow-scripts allow-same-origin"
            referrerpolicy="no-referrer">
        </iframe>
    </template>

    {{-- PDF — no sandbox, browser native renderer --}}
    <template x-if="previewType === 'PDF_DOCUMENT'">
        <iframe :src="previewUrl"
            class="flex-1 w-full border-0">
        </iframe>
    </template>

    {{-- Markdown rendered view --}}
    <template x-if="previewType === 'MARKDOWN_RESEARCH'">
        <div class="flex-1 overflow-auto">
            <div class="max-w-4xl mx-auto px-8 py-10">
                <div class="prose-dark" x-html="renderedMarkdown"></div>
            </div>
        </div>
    </template>

    {{-- JSON code viewer --}}
    <template x-if="previewType === 'JSON_DATA'">
        <div class="flex-1 overflow-auto p-6">
            <pre class="text-sm text-green-300 font-mono whitespace-pre-wrap bg-gray-900 rounded-lg p-6 border border-gray-700 leading-relaxed"
                x-text="previewContent"></pre>
        </div>
    </template>

    {{-- Word document rendered via mammoth.js --}}
    <template x-if="previewType === 'WORD_DOCUMENT'">
        <div class="flex-1 overflow-auto">
            <div class="max-w-4xl mx-auto px-8 py-10">
                <template x-if="wordLoading">
                    <p class="text-gray-400 text-center py-20">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Converting document…
                    </p>
                </template>
                <template x-if="wordError">
                    <div class="text-center py-20 space-y-4">
                        <i class="fas fa-exclamation-triangle text-yellow-400 text-3xl"></i>
                        <p class="text-gray-300" x-text="wordError"></p>
                        <a :href="downloadUrl" class="btn-secondary inline-flex items-center gap-2">
                            <i class="fas fa-download"></i> Download to view
                        </a>
                    </div>
                </template>
                <div class="prose-dark" x-html="renderedWord"></div>
            </div>
        </div>
    </template>

    {{-- PowerPoint — no browser preview, offer download --}}
    <template x-if="previewType === 'POWERPOINT'">
        <div class="flex-1 flex items-center justify-center">
            <div class="text-center space-y-4">
                <i class="fas fa-file-powerpoint text-orange-400 text-5xl"></i>
                <p class="text-gray-300 text-lg font-medium" x-text="previewTitle"></p>
                <p class="text-gray-500 text-sm">PowerPoint files can't be previewed in the browser.</p>
                <a :href="downloadUrl" class="btn-primary inline-flex items-center gap-2">
                    <i class="fas fa-download"></i> Download to view
                </a>
            </div>
        </div>
    </template>

</div>{{-- end modal --}}
</div>{{-- end x-data wrapper --}}

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/mammoth@1.8.0/mammoth.browser.min.js"></script>
<script>
function packageDetail() {
    return {
        activeTab: 'deliverables',
        previewOpen: false,
        previewTitle: '',
        previewType: '',
        previewUrl: '',
        previewContent: '',
        renderedMarkdown: '',
        renderedWord: '',
        wordLoading: false,
        wordError: '',
        downloadUrl: '',

        async previewFile(fileId, title, type) {
            this.previewTitle = title;
            this.previewType = type;
            this.previewOpen = true;
            this.renderedMarkdown = '';
            this.renderedWord = '';
            this.wordError = '';
            this.wordLoading = false;

            const baseUrl = `{{ route('admin.packages.show', $package) }}/files/${fileId}`;
            this.previewUrl  = `${baseUrl}/preview`;
            this.downloadUrl = `${baseUrl}/preview`;

            if (type === 'MARKDOWN_RESEARCH' || type === 'JSON_DATA') {
                this.previewContent = 'Loading…';
                this.renderedMarkdown = '<p class="text-gray-400">Loading…</p>';
                try {
                    const res = await fetch(`${baseUrl}/content`);
                    const data = await res.json();
                    const raw = data.content || '';
                    this.previewContent = raw;
                    if (type === 'MARKDOWN_RESEARCH') {
                        this.renderedMarkdown = marked.parse(raw);
                    }
                } catch (e) {
                    this.previewContent = 'Error loading file.';
                    this.renderedMarkdown = '<p class="text-red-400">Error loading file.</p>';
                }
            } else if (type === 'WORD_DOCUMENT') {
                this.wordLoading = true;
                try {
                    const res = await fetch(this.previewUrl);
                    if (!res.ok) throw new Error('Failed to fetch file');
                    const arrayBuffer = await res.arrayBuffer();
                    const result = await mammoth.convertToHtml({ arrayBuffer });
                    this.renderedWord = result.value || '<p class="text-gray-400">Document appears to be empty.</p>';
                } catch (e) {
                    this.wordError = 'Could not render document. Download it to view.';
                } finally {
                    this.wordLoading = false;
                }
            }
        },

        async togglePromote(fileId, btn) {
            try {
                const res = await fetch(`{{ route('admin.packages.show', $package) }}/files/${fileId}/promote`, {
                    method: 'PATCH',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                        'Accept': 'application/json',
                    },
                });
                const data = await res.json();
                const promoted = data.portal_promoted;
                btn.dataset.promoted = promoted ? 'true' : 'false';

                // Update button appearance
                btn.className = btn.className.replace(
                    /(?:bg-green-900\/40 text-green-300 border-green-700|bg-gray-700 text-gray-400 border-gray-600 hover:border-green-700 hover:text-green-400)/,
                    promoted
                        ? 'bg-green-900/40 text-green-300 border-green-700'
                        : 'bg-gray-700 text-gray-400 border-gray-600 hover:border-green-700 hover:text-green-400'
                );
                btn.innerHTML = `<i class="fas ${promoted ? 'fa-globe' : 'fa-eye-slash'} mr-1"></i>${promoted ? 'Portal' : 'Hidden'}`;
            } catch (e) {
                alert('Failed to update portal status.');
            }
        },
    };
}
</script>
@endpush
@endsection
