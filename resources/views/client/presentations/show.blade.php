@extends('layouts.client')

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
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6" x-data="clientPresentationDetail()">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <a href="{{ route('client.presentations.index') }}" class="inline-flex items-center text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">
                <span aria-hidden="true" class="mr-2">←</span>Back to Presentations
            </a>
            <h1 class="mt-3 text-2xl font-semibold text-gray-900 dark:text-white">{{ $package->name }}</h1>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Shared on {{ $package->created_at->format('F j, Y') }} for your account.</p>
        </div>
        <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-semibold {{ $package->status_badge_class }}">
            {{ ucfirst($package->status) }}
        </span>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
        @foreach($visibleFiles as $file)
            <div class="rounded-xl border border-gray-300 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900/40">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $file->display_name }}</p>
                        <p class="mt-1 text-xs font-mono text-gray-500 dark:text-gray-400">{{ $file->original_name }}</p>
                    </div>
                    <span class="inline-flex rounded-full px-2 py-1 text-xs font-medium {{ $file->type_badge_class }}">
                        {{ str_replace('_', ' ', $file->type) }}
                    </span>
                </div>

                <div class="mt-4 flex items-center justify-between text-sm text-gray-600 dark:text-gray-400">
                    <span>{{ $file->formatted_size }}</span>
                    <span>{{ $file->created_at->format('M j, Y') }}</span>
                </div>

                <button @click="previewFile({{ $file->id }}, '{{ addslashes($file->display_name) }}', '{{ $file->type }}')"
                    class="mt-5 inline-flex w-full items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/70">
                    {{ in_array($file->type, ['WORD_DOCUMENT', 'POWERPOINT'], true) ? 'Download File' : 'Preview File' }}
                </button>
            </div>
        @endforeach
    </div>

    <div x-show="previewOpen" x-cloak
        class="fixed inset-0 z-50 flex flex-col bg-gray-950/95"
        @keydown.escape.window="previewOpen = false">

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

        <template x-if="previewType === 'HTML_PRESENTATION' || previewType === 'HTML_EMAIL'">
            <iframe :src="previewUrl"
                class="flex-1 w-full border-0"
                sandbox="allow-scripts allow-same-origin"
                referrerpolicy="no-referrer">
            </iframe>
        </template>

        <template x-if="previewType === 'PDF_DOCUMENT'">
            <iframe :src="previewUrl"
                class="flex-1 w-full border-0">
            </iframe>
        </template>

        <template x-if="previewType === 'MARKDOWN_RESEARCH'">
            <div class="flex-1 overflow-auto">
                <div class="max-w-4xl mx-auto px-8 py-10">
                    <div class="prose-dark" x-html="renderedMarkdown"></div>
                </div>
            </div>
        </template>

        <template x-if="previewType === 'JSON_DATA'">
            <div class="flex-1 overflow-auto p-6">
                <pre class="text-sm text-green-300 font-mono whitespace-pre-wrap bg-gray-900 rounded-lg p-6 border border-gray-700 leading-relaxed"
                    x-text="previewContent"></pre>
            </div>
        </template>

        <template x-if="previewType === 'WORD_DOCUMENT'">
            <div class="flex-1 overflow-auto">
                <div class="max-w-4xl mx-auto px-8 py-10">
                    <template x-if="wordLoading">
                        <p class="text-gray-400 text-center py-20">
                            <i class="fas fa-spinner fa-spin mr-2"></i>Converting document...
                        </p>
                    </template>
                    <template x-if="wordError">
                        <div class="text-center py-20 space-y-4">
                            <i class="fas fa-exclamation-triangle text-yellow-400 text-3xl"></i>
                            <p class="text-gray-300" x-text="wordError"></p>
                            <a :href="downloadUrl" class="inline-flex items-center justify-center rounded-md border border-gray-700 bg-gray-800 px-4 py-2 text-sm font-medium text-gray-100 hover:bg-gray-700">
                                <i class="fas fa-download mr-2"></i>Download to view
                            </a>
                        </div>
                    </template>
                    <div class="prose-dark" x-html="renderedWord"></div>
                </div>
            </div>
        </template>

        <template x-if="previewType === 'POWERPOINT'">
            <div class="flex-1 flex items-center justify-center">
                <div class="text-center space-y-4">
                    <i class="fas fa-file-powerpoint text-orange-400 text-5xl"></i>
                    <p class="text-gray-300 text-lg font-medium" x-text="previewTitle"></p>
                    <p class="text-gray-500 text-sm">PowerPoint files cannot be previewed in the browser.</p>
                    <a :href="downloadUrl" class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-indigo-500">
                        <i class="fas fa-download mr-2"></i>Download to view
                    </a>
                </div>
            </div>
        </template>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/mammoth@1.8.0/mammoth.browser.min.js"></script>
<script>
function clientPresentationDetail() {
    return {
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
            this.previewContent = '';
            this.renderedMarkdown = '';
            this.renderedWord = '';
            this.wordError = '';
            this.wordLoading = false;

            const baseUrl = `{{ route('client.presentations.show', $package) }}/files/${fileId}`;
            this.previewUrl = `${baseUrl}/preview`;
            this.downloadUrl = `${baseUrl}/preview`;

            if (type === 'MARKDOWN_RESEARCH' || type === 'JSON_DATA') {
                this.previewContent = 'Loading...';
                this.renderedMarkdown = '<p class="text-gray-400">Loading...</p>';

                try {
                    const response = await fetch(`${baseUrl}/content`, {
                        headers: {
                            'Accept': 'application/json',
                        },
                    });
                    const data = await response.json();
                    const rawContent = data.content || '';

                    this.previewContent = rawContent;

                    if (type === 'MARKDOWN_RESEARCH') {
                        this.renderedMarkdown = marked.parse(rawContent);
                    }
                } catch (error) {
                    this.previewContent = 'Error loading file.';
                    this.renderedMarkdown = '<p class="text-red-400">Error loading file.</p>';
                }

                return;
            }

            if (type === 'WORD_DOCUMENT') {
                this.wordLoading = true;

                try {
                    const response = await fetch(this.previewUrl);

                    if (! response.ok) {
                        throw new Error('Failed to fetch file');
                    }

                    const arrayBuffer = await response.arrayBuffer();
                    const result = await mammoth.convertToHtml({ arrayBuffer });
                    this.renderedWord = result.value || '<p class="text-gray-400">Document appears to be empty.</p>';
                } catch (error) {
                    this.wordError = 'Could not render document. Download it to view.';
                } finally {
                    this.wordLoading = false;
                }
            }
        },
    };
}
</script>
@endpush