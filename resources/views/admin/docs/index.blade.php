@extends('layouts.admin')

@section('content')
<div>
    <!-- Page Header -->
    <div class="admin-page-header">
        <div>
            <h1 class="text-3xl font-bold text-white flex items-center gap-3">
                <i class="fas fa-book text-primary-400"></i>
                Internal Documentation
            </h1>
            <p class="text-gray-400 mt-1">Technical documentation and project guides</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-400">
                <i class="fas fa-file-alt"></i> {{ $files->count() }} {{ Str::plural('document', $files->count()) }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Sidebar - Document List -->
        <div class="lg:col-span-1">
            <div class="bg-gray-800/50 rounded-lg border border-gray-700/50 overflow-hidden sticky top-6">
                <!-- Search Bar -->
                <div class="p-4 border-b border-gray-700/50">
                    <div class="relative">
                        <input 
                            type="text" 
                            id="docSearch" 
                            placeholder="Search documents..." 
                            class="w-full bg-gray-900/50 border border-gray-600/50 rounded-lg px-4 py-2 pl-10 text-sm text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                        >
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm"></i>
                    </div>
                </div>

                <!-- Document List -->
                <div class="max-h-[calc(100vh-250px)] overflow-y-auto p-2">
                    @forelse($files as $file)
                        <a 
                            href="?file={{ urlencode($file['name']) }}" 
                            class="doc-item flex items-center gap-3 px-3 py-2.5 rounded-lg transition-all duration-200 mb-1 {{ $selected === $file['name'] ? 'bg-primary-600/20 text-primary-400 font-medium border-l-2 border-primary-500' : 'text-gray-300 hover:bg-gray-700/50 hover:text-white' }}"
                            data-filename="{{ strtolower($file['name']) }}"
                        >
                            <i class="fas fa-file-alt text-sm flex-shrink-0 {{ $selected === $file['name'] ? 'text-primary-400' : 'text-gray-500' }}"></i>
                            <span class="text-sm truncate">{{ Str::before($file['name'], '.md') }}</span>
                        </a>
                    @empty
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-folder-open text-3xl mb-3 opacity-50"></i>
                            <p class="text-sm">No documentation files found</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="lg:col-span-3">
            @if($error)
                <div class="bg-red-900/20 border border-red-500/50 rounded-lg p-6">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-exclamation-triangle text-red-400 text-xl"></i>
                        <div>
                            <h3 class="text-red-400 font-semibold mb-1">Error Loading Document</h3>
                            <p class="text-red-300/80 text-sm">{{ $error }}</p>
                        </div>
                    </div>
                </div>
            @elseif($html)
                <div class="admin-card">
                    <div class="admin-card-body">
                        <!-- Document Header -->
                        <div class="flex items-center justify-between pb-4 mb-6 border-b border-gray-700/50">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-primary-600/20 flex items-center justify-center">
                                    <i class="fas fa-file-alt text-primary-400"></i>
                                </div>
                                <div>
                                    <h2 class="text-xl font-semibold text-white">{{ Str::before($selected, '.md') }}</h2>
                                    <p class="text-sm text-gray-400">{{ $selected }}</p>
                                </div>
                            </div>
                            <div class="flex gap-2">
                                <button 
                                    onclick="copyToClipboard()" 
                                    class="px-3 py-1.5 bg-gray-700/50 hover:bg-gray-600/50 text-gray-300 rounded-lg text-sm transition-colors flex items-center gap-2"
                                    title="Copy content"
                                >
                                    <i class="fas fa-copy"></i>
                                    <span>Copy</span>
                                </button>
                            </div>
                        </div>

                        <!-- Document Content -->
                        <div class="prose prose-invert prose-sm md:prose-base max-w-none prose-headings:text-white prose-p:text-gray-300 prose-a:text-primary-400 prose-a:no-underline hover:prose-a:underline prose-strong:text-white prose-code:text-primary-300 prose-code:bg-gray-900/50 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded prose-pre:bg-gray-900/50 prose-pre:border prose-pre:border-gray-700/50 prose-ul:text-gray-300 prose-ol:text-gray-300 prose-blockquote:border-l-primary-500 prose-blockquote:text-gray-400 prose-hr:border-gray-700/50">
                            {!! $html !!}
                        </div>
                    </div>
                </div>
            @elseif($selected)
                <div class="bg-yellow-900/20 border border-yellow-500/50 rounded-lg p-6">
                    <div class="flex items-start gap-3">
                        <i class="fas fa-info-circle text-yellow-400 text-xl"></i>
                        <div>
                            <h3 class="text-yellow-400 font-semibold mb-1">Empty Document</h3>
                            <p class="text-yellow-300/80 text-sm">
                                @if($content === '')
                                    This file exists but contains no content.
                                @else
                                    The selected file could not be found or is empty.
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Welcome State -->
                <div class="admin-card">
                    <div class="admin-card-body text-center py-16">
                        <div class="max-w-md mx-auto">
                            <div class="w-20 h-20 rounded-full bg-primary-600/20 flex items-center justify-center mx-auto mb-6">
                                <i class="fas fa-book-open text-primary-400 text-3xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-white mb-3">Welcome to Documentation</h3>
                            <p class="text-gray-400 mb-6">
                                Select a document from the sidebar to view its contents. All documentation is stored in the <code class="text-primary-400 bg-gray-900/50 px-2 py-1 rounded text-sm">app/docs</code> directory.
                            </p>
                            <div class="flex items-center justify-center gap-6 text-sm text-gray-500">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-file-alt"></i>
                                    <span>Markdown Format</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-search"></i>
                                    <span>Searchable</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-clock"></i>
                                    <span>Real-time</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Search functionality
    const searchInput = document.getElementById('docSearch');
    const docItems = document.querySelectorAll('.doc-item');

    if (searchInput) {
        searchInput.addEventListener('input', (e) => {
            const query = e.target.value.toLowerCase();
            
            docItems.forEach(item => {
                const filename = item.getAttribute('data-filename');
                if (filename.includes(query)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Copy content to clipboard
    function copyToClipboard() {
        const content = document.querySelector('.prose').innerText;
        navigator.clipboard.writeText(content).then(() => {
            // Show success feedback
            const btn = event.target.closest('button');
            const icon = btn.querySelector('i');
            const text = btn.querySelector('span');
            
            icon.className = 'fas fa-check';
            text.textContent = 'Copied!';
            btn.classList.add('bg-green-600/50');
            
            setTimeout(() => {
                icon.className = 'fas fa-copy';
                text.textContent = 'Copy';
                btn.classList.remove('bg-green-600/50');
            }, 2000);
        }).catch(err => {
            console.error('Failed to copy:', err);
        });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('.prose a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
</script>
@endpush
@endsection
