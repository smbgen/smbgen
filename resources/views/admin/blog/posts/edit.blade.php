@extends('layouts.admin')

@section('content')
<div x-data="{}" class="py-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Edit Blog Post</h1>
            <p class="admin-page-subtitle">Update your blog post</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="btn-secondary">
                <i class="fas fa-eye mr-2"></i>Preview
            </a>
            <a href="{{ route('admin.blog.posts.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Posts
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-error mb-6">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.blog.posts.update', $post) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Title -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <label class="form-label">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $post->title) }}" required 
                           class="form-input" placeholder="Enter post title">
                </div>

                <!-- Slug -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <label class="form-label">Slug</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $post->slug) }}" 
                           class="form-input" placeholder="auto-generated-from-title">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">URL: {{ url('/blog/' . $post->slug) }}</p>
                </div>

                <!-- Excerpt -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <label class="form-label">Excerpt</label>
                    <textarea name="excerpt" id="excerpt" rows="4" class="form-input" placeholder="Brief summary...">{{ old('excerpt', $post->excerpt) }}</textarea>
                </div>

                <!-- Rich Text Editor -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <label class="form-label mb-0">Content (WYSIWYG Editor)</label>
                        @if(config('ai.enabled'))
                        <div class="flex gap-2">
                            <button type="button" 
                                    @click="$dispatch('open-ai-modal', { target: 'content', contentType: 'content_improvement' })"
                                    class="btn-secondary text-sm">
                                <i class="fas fa-robot mr-2"></i>Improve with AI
                            </button>
                            <button type="button" 
                                    @click="$dispatch('open-ai-modal', { target: 'content', contentType: 'blog_post' })"
                                    class="btn-secondary text-sm">
                                <i class="fas fa-sparkles mr-2"></i>Generate with AI
                            </button>
                        </div>
                        @endif
                    </div>
                    @if(config('ai.enabled'))
                    <p class="text-xs text-yellow-600 dark:text-yellow-400 mb-2">
                        <i class="fas fa-exclamation-triangle mr-1"></i>AI-generated content will replace existing content
                    </p>
                    @endif
                    <div id="content-editor" style="height: 400px;"></div>
                    <textarea name="content" id="content" class="hidden">{{ old('content', $post->content) }}</textarea>
                </div>

                <!-- Advanced: Content Blocks -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Advanced Content Blocks</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Build your content with flexible blocks</p>
                        </div>
                        <button type="button" id="add-block-btn" class="btn-primary">
                            <i class="fas fa-plus mr-2"></i>Add Block
                        </button>
                    </div>
                    <div id="content-blocks" class="space-y-4"></div>
                    <input type="hidden" name="content_blocks" id="content-blocks-input" value="{{ json_encode(old('content_blocks', $post->content_blocks ?: [])) }}">
                </div>
            </div>

            <!-- Sidebar Column -->
            <div class="space-y-6">
                <!-- Publish Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Publish</h3>
                    
                    <div class="mb-4">
                        <label class="form-label">Status</label>
                        <select name="status" class="form-select">
                            <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $post->status) === 'published' ? 'selected' : '' }}>Published</option>
                            <option value="scheduled" {{ old('status', $post->status) === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                            <option value="archived" {{ old('status', $post->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Publish Date</label>
                        <input type="datetime-local" name="published_at" 
                               value="{{ old('published_at', $post->published_at?->format('Y-m-d\TH:i')) }}" 
                               class="form-input">
                    </div>

                    <button type="submit" class="btn-primary w-full">
                        <i class="fas fa-save mr-2"></i>Update Post
                    </button>
                </div>

                <!-- Featured Image -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Featured Image</h3>
                    <div class="mb-4">
                        <input type="text" name="featured_image" id="featured_image" 
                               value="{{ old('featured_image', $post->featured_image) }}" 
                               class="form-input" placeholder="Image URL or ID">
                    </div>
                    <button type="button" id="select-media-btn" class="btn-secondary w-full">
                        <i class="fas fa-image mr-2"></i>Select from Library
                    </button>
                    <div id="featured-image-preview" class="mt-4 {{ $post->featured_image ? '' : 'hidden' }}">
                        <img src="{{ $post->featured_image }}" alt="Preview" class="w-full rounded">
                    </div>
                </div>

                <!-- Categories -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Categories</h3>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @forelse($categories as $category)
                            <label class="flex items-center">
                                <input type="checkbox" name="categories[]" value="{{ $category->id }}" 
                                       {{ in_array($category->id, old('categories', $post->categories->pluck('id')->toArray())) ? 'checked' : '' }}
                                       class="form-checkbox">
                                <span class="ml-2">{{ $category->name }}</span>
                            </label>
                        @empty
                            <p class="text-sm text-gray-500">No categories available.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Tags -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tags</h3>
                    <div class="space-y-2 max-h-48 overflow-y-auto">
                        @forelse($tags as $tag)
                            <label class="flex items-center">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}"
                                       {{ in_array($tag->id, old('tags', $post->tags->pluck('id')->toArray())) ? 'checked' : '' }}
                                       class="form-checkbox">
                                <span class="ml-2">{{ $tag->name }}</span>
                            </label>
                        @empty
                            <p class="text-sm text-gray-500">No tags available.</p>
                        @endforelse
                    </div>
                </div>

                <!-- SEO Settings -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">SEO</h3>
                        @if(config('ai.enabled'))
                        <button type="button" 
                                @click="$dispatch('open-seo-modal')"
                                class="btn-secondary text-sm">
                            <i class="fas fa-robot mr-2"></i>Generate with AI
                        </button>
                        @endif
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label">SEO Title</label>
                        <input type="text" name="seo_title" id="seo_title" value="{{ old('seo_title', $post->seo_title) }}" class="form-input" maxlength="60">
                        <p class="text-xs text-gray-500 mt-1">Leave blank to use post title</p>
                    </div>

                    <div class="mb-4">
                        <label class="form-label">SEO Description</label>
                        <textarea name="seo_description" id="seo_description" rows="3" class="form-input" maxlength="160">{{ old('seo_description', $post->seo_description) }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Max 160 characters</p>
                    </div>

                    <div>
                        <label class="form-label">SEO Keywords</label>
                        <input type="text" name="seo_keywords" id="seo_keywords" value="{{ old('seo_keywords', $post->seo_keywords) }}" class="form-input" placeholder="keyword1, keyword2">
                    </div>
                </div>

                <!-- Comments Section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Comments</h3>
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Total comments:</span>
                            <span class="font-semibold">{{ $post->comments()->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Approved:</span>
                            <span class="font-semibold text-green-600">{{ $post->comments()->approved()->count() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Pending:</span>
                            <span class="font-semibold text-yellow-600">{{ $post->comments()->pending()->count() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Block Type Modal -->
<div id="block-type-modal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 max-w-2xl w-full mx-4 max-h-screen overflow-y-auto">
        <h3 class="text-xl font-bold mb-4 text-gray-900 dark:text-white">Choose Block Type</h3>
        <div class="grid grid-cols-2 gap-4">
            <button type="button" class="block-type-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded hover:border-blue-500 dark:hover:border-blue-400 text-gray-900 dark:text-white" data-type="heading">
                <i class="fas fa-heading text-2xl mb-2"></i>
                <p class="font-semibold">Heading</p>
            </button>
            <button type="button" class="block-type-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded hover:border-blue-500 dark:hover:border-blue-400 text-gray-900 dark:text-white" data-type="text">
                <i class="fas fa-paragraph text-2xl mb-2"></i>
                <p class="font-semibold">Text/Rich Text</p>
            </button>
            <button type="button" class="block-type-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded hover:border-blue-500 dark:hover:border-blue-400 text-gray-900 dark:text-white" data-type="image">
                <i class="fas fa-image text-2xl mb-2"></i>
                <p class="font-semibold">Image</p>
            </button>
            <button type="button" class="block-type-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded hover:border-blue-500 dark:hover:border-blue-400 text-gray-900 dark:text-white" data-type="quote">
                <i class="fas fa-quote-left text-2xl mb-2"></i>
                <p class="font-semibold">Quote</p>
            </button>
            <button type="button" class="block-type-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded hover:border-blue-500 dark:hover:border-blue-400 text-gray-900 dark:text-white" data-type="code">
                <i class="fas fa-code text-2xl mb-2"></i>
                <p class="font-semibold">Code</p>
            </button>
            <button type="button" class="block-type-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded hover:border-blue-500 dark:hover:border-blue-400 text-gray-900 dark:text-white" data-type="video">
                <i class="fas fa-video text-2xl mb-2"></i>
                <p class="font-semibold">Video</p>
            </button>
            <button type="button" class="block-type-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded hover:border-blue-500 dark:hover:border-blue-400 text-gray-900 dark:text-white" data-type="callout">
                <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                <p class="font-semibold">Callout</p>
            </button>
            <button type="button" class="block-type-btn p-4 border-2 border-gray-300 dark:border-gray-600 rounded hover:border-blue-500 dark:hover:border-blue-400 text-gray-900 dark:text-white" data-type="gallery">
                <i class="fas fa-images text-2xl mb-2"></i>
                <p class="font-semibold">Gallery</p>
            </button>
        </div>
        <button type="button" id="close-modal-btn" class="mt-4 btn-secondary w-full">Cancel</button>
    </div>
</div>

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.snow.css" rel="stylesheet">
<style>
    /* Quill editor content styling */
    .ql-editor {
        color: #1f2937 !important;
        font-size: 16px;
        line-height: 1.6;
    }
    .dark .ql-editor {
        color: #f9fafb !important;
        background-color: #1f2937;
    }
    .ql-container {
        font-family: inherit;
    }
    .dark .ql-toolbar {
        background-color: #374151;
        border-color: #4b5563 !important;
    }
    .dark .ql-container {
        border-color: #4b5563 !important;
    }
    .dark .ql-stroke {
        stroke: #9ca3af !important;
    }
    .dark .ql-fill {
        fill: #9ca3af !important;
    }
    .dark .ql-picker-label {
        color: #9ca3af !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@2.0.2/dist/quill.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize Quill Editor with error handling
    let quill;
    try {
        quill = new Quill('#content-editor', {
        theme: 'snow',
        modules: {
            toolbar: [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike'],
                [{ 'color': [] }, { 'background': [] }],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['link', 'image', 'video'],
                ['blockquote', 'code-block'],
                ['clean']
            ]
        },
        placeholder: 'Write your content here...'
    });
    
        // Load existing content
        const existingContent = document.querySelector('textarea[name="content"]').value;
        if (existingContent) {
            quill.root.innerHTML = existingContent;
        }
        
        // Sync Quill content to textarea continuously
        quill.on('text-change', function() {
            document.querySelector('textarea[name="content"]').value = quill.root.innerHTML;
        });
        
        // Also sync on form submit as backup
        document.querySelector('form').addEventListener('submit', function(e) {
            document.querySelector('textarea[name="content"]').value = quill.root.innerHTML;
        });
    } catch (error) {
        console.error('Failed to initialize Quill editor:', error);
        alert('WYSIWYG editor failed to load. The page will reload. If the issue persists, please contact support.');
        setTimeout(() => window.location.reload(), 2000);
    }

    let blockCounter = 0;
    const blocksContainer = document.getElementById('content-blocks');
    const blocksInput = document.getElementById('content-blocks-input');
    const modal = document.getElementById('block-type-modal');
    const addBlockBtn = document.getElementById('add-block-btn');
    const closeModalBtn = document.getElementById('close-modal-btn');
    
    // Block builder modal controls
    if (modal && addBlockBtn && closeModalBtn) {
        addBlockBtn.addEventListener('click', () => modal.classList.remove('hidden'));
        closeModalBtn.addEventListener('click', () => modal.classList.add('hidden'));
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.add('hidden');
        });
        
        document.querySelectorAll('.block-type-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const type = this.dataset.type;
                addBlock(type);
                modal.classList.add('hidden');
            });
        });
    }
    
    // Auto-generate slug from title
    document.getElementById('title').addEventListener('input', function(e) {
        const slug = document.getElementById('slug');
        if (!slug.value || slug.dataset.autoGenerated) {
            slug.value = e.target.value.toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '');
            slug.dataset.autoGenerated = 'true';
        }
    });
    
    document.getElementById('slug').addEventListener('input', function() {
        delete this.dataset.autoGenerated;
    });
    
    // Featured image preview
    document.getElementById('featured_image').addEventListener('input', function(e) {
        const preview = document.getElementById('featured-image-preview');
        if (e.target.value) {
            preview.querySelector('img').src = e.target.value;
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
        }
    });
    
    function addBlock(type, existingData = {}) {
        const id = blockCounter++;
        const block = document.createElement('div');
        block.className = 'block-item border-2 border-gray-300 dark:border-gray-600 rounded-lg p-4';
        block.dataset.type = type;
        block.dataset.id = id;
        
        let content = `
            <div class="flex justify-between items-center mb-3 pb-3 border-b border-gray-200 dark:border-gray-600">
                <h4 class="font-semibold capitalize text-gray-900 dark:text-white"><i class="fas fa-grip-vertical mr-2"></i>${type}</h4>
                <div class="flex gap-2">
                    <button type="button" class="move-up px-3 py-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded text-sm font-medium transition-colors" title="Move Up">
                        ↑ Up
                    </button>
                    <button type="button" class="move-down px-3 py-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded text-sm font-medium transition-colors" title="Move Down">
                        ↓ Down
                    </button>
                    <button type="button" class="remove-block px-3 py-1 bg-red-100 hover:bg-red-200 dark:bg-red-900 dark:hover:bg-red-800 text-red-800 dark:text-red-200 rounded text-sm font-medium transition-colors" title="Delete Block">
                        🗑 Delete
                    </button>
                </div>
            </div>
        `;
        
        switch(type) {
            case 'heading':
                content += `
                    <select class="form-select mb-2" data-field="level">
                        <option value="h2" ${existingData.level === 'h2' ? 'selected' : ''}>Heading 2</option>
                        <option value="h3" ${existingData.level === 'h3' ? 'selected' : ''}>Heading 3</option>
                        <option value="h4" ${existingData.level === 'h4' ? 'selected' : ''}>Heading 4</option>
                    </select>
                    <input type="text" class="form-input" data-field="content" placeholder="Heading text" value="${existingData.content || ''}">
                `;
                break;
            case 'text':
                content += `<textarea class="form-input" rows="5" data-field="content" placeholder="Rich text content (HTML supported)">${existingData.content || ''}</textarea>`;
                break;
            case 'image':
                content += `
                    <input type="text" class="form-input mb-2" data-field="url" placeholder="Image URL" value="${existingData.url || ''}">
                    <input type="text" class="form-input mb-2" data-field="alt" placeholder="Alt text" value="${existingData.alt || ''}">
                    <input type="text" class="form-input" data-field="caption" placeholder="Caption (optional)" value="${existingData.caption || ''}">
                `;
                break;
            case 'quote':
                content += `
                    <textarea class="form-input mb-2" rows="3" data-field="content" placeholder="Quote text">${existingData.content || ''}</textarea>
                    <input type="text" class="form-input" data-field="author" placeholder="Author (optional)" value="${existingData.author || ''}">
                `;
                break;
            case 'code':
                content += `
                    <input type="text" class="form-input mb-2" data-field="language" placeholder="Language (e.g., php, javascript)" value="${existingData.language || ''}">
                    <textarea class="form-input font-mono text-sm" rows="8" data-field="content" placeholder="Code here">${existingData.content || ''}</textarea>
                `;
                break;
            case 'video':
                content += `
                    <input type="text" class="form-input mb-2" data-field="url" placeholder="Video URL (YouTube, Vimeo, etc.)" value="${existingData.url || ''}">
                    <select class="form-select" data-field="platform">
                        <option value="youtube" ${existingData.platform === 'youtube' ? 'selected' : ''}>YouTube</option>
                        <option value="vimeo" ${existingData.platform === 'vimeo' ? 'selected' : ''}>Vimeo</option>
                        <option value="direct" ${existingData.platform === 'direct' ? 'selected' : ''}>Direct Video</option>
                    </select>
                `;
                break;
            case 'callout':
                content += `
                    <select class="form-select mb-2" data-field="style">
                        <option value="info" ${existingData.style === 'info' ? 'selected' : ''}>Info (Blue)</option>
                        <option value="success" ${existingData.style === 'success' ? 'selected' : ''}>Success (Green)</option>
                        <option value="warning" ${existingData.style === 'warning' ? 'selected' : ''}>Warning (Yellow)</option>
                        <option value="danger" ${existingData.style === 'danger' ? 'selected' : ''}>Danger (Red)</option>
                    </select>
                    <textarea class="form-input" rows="3" data-field="content" placeholder="Callout text">${existingData.content || ''}</textarea>
                `;
                break;
            case 'gallery':
                content += `
                    <textarea class="form-input" rows="5" data-field="images" placeholder="Image URLs, one per line">${existingData.images || ''}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Enter one image URL per line</p>
                `;
                break;
        }
        
        block.innerHTML = content;
        blocksContainer.appendChild(block);
        
        // Event listeners
        block.querySelector('.remove-block').addEventListener('click', () => {
            block.remove();
            updateBlocksInput();
        });
        
        block.querySelector('.move-up').addEventListener('click', () => {
            if (block.previousElementSibling) {
                blocksContainer.insertBefore(block, block.previousElementSibling);
                updateBlocksInput();
            }
        });
        
        block.querySelector('.move-down').addEventListener('click', () => {
            if (block.nextElementSibling) {
                blocksContainer.insertBefore(block.nextElementSibling, block);
                updateBlocksInput();
            }
        });
        
        block.querySelectorAll('[data-field]').forEach(field => {
            field.addEventListener('input', updateBlocksInput);
        });
        
        updateBlocksInput();
    }
    
    // Load existing blocks
    try {
        const existingBlocks = JSON.parse(blocksInput.value || '[]');
        existingBlocks.forEach(blockData => {
            addBlock(blockData.type, blockData);
        });
    } catch (e) {
        console.error('Error loading existing blocks:', e);
    }
    
    function updateBlocksInput() {
        const blocks = [];
        document.querySelectorAll('.block-item').forEach(block => {
            const type = block.dataset.type;
            const data = { type };
            
            block.querySelectorAll('[data-field]').forEach(field => {
                const fieldName = field.dataset.field;
                data[fieldName] = field.value;
            });
            
            blocks.push(data);
        });
        
        blocksInput.value = blocks.length ? JSON.stringify(blocks) : '';
    }

    // SEO Generation Modal Handler
    window.addEventListener('open-seo-modal', () => {
        const modalHtml = `
            <div id="ai-seo-modal" class="fixed inset-0 z-50 overflow-y-auto" x-data="aiSEOGenerator()" x-show="open" x-cloak>
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-gray-500 dark:bg-gray-900 bg-opacity-75 dark:bg-opacity-75" @click="open = false"></div>
                    
                    <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                        <div class="absolute top-0 right-0 pt-4 pr-4">
                            <button type="button" @click="open = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        
                        <div class="sm:flex sm:items-start">
                            <div class="w-full">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                                    <i class="fas fa-robot mr-2 text-purple-500"></i>Generate SEO Metadata
                                </h3>
                                
                                <div class="space-y-4">
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        AI will analyze your title and content to generate optimized SEO metadata.
                                    </p>
                                    
                                    <!-- Loading Progress Bar -->
                                    <div x-show="loading" class="space-y-2">
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                            <div class="flex items-center space-x-3 mb-3">
                                                <i class="fas fa-circle-notch fa-spin text-blue-600 dark:text-blue-400 text-xl"></i>
                                                <span class="text-sm font-medium text-blue-900 dark:text-blue-200">Generating SEO metadata...</span>
                                            </div>
                                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2.5">
                                                <div class="bg-blue-600 h-2.5 rounded-full animate-pulse" style="width: 100%"></div>
                                            </div>
                                            <p class="text-xs text-blue-700 dark:text-blue-300 mt-2">This may take a few seconds...</p>
                                        </div>
                                    </div>
                                    
                                    <div x-show="error" class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-400 px-4 py-3 rounded">
                                        <p x-text="error"></p>
                                    </div>
                                    
                                    <div class="flex justify-between items-center pt-4">
                                        <button type="button" @click="open = false" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg font-medium transition-colors" :disabled="loading">
                                            Cancel
                                        </button>
                                        <button type="button" @click="generateSEO()" 
                                                class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center" 
                                                :disabled="loading">
                                            <template x-if="!loading">
                                                <span class="flex items-center">
                                                    <i class="fas fa-wand-magic-sparkles mr-2"></i>Generate SEO
                                                </span>
                                            </template>
                                            <template x-if="loading">
                                                <span class="flex items-center">
                                                    <i class="fas fa-spinner fa-spin mr-2"></i>Generating...
                                                </span>
                                            </template>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        if (!document.getElementById('ai-seo-modal')) {
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        }
    });

    function aiSEOGenerator() {
        return {
            open: true,
            loading: false,
            error: '',
            
            async generateSEO() {
                this.loading = true;
                this.error = '';
                
                try {
                    const title = document.getElementById('title').value;
                    const content = typeof quill !== 'undefined' ? quill.root.innerHTML : document.getElementById('content').value;
                    
                    if (!title) {
                        throw new Error('Please enter a post title first.');
                    }
                    
                    if (!content || content.trim() === '<p><br></p>' || content.trim() === '') {
                        throw new Error('Please add some content first.');
                    }
                    
                    const response = await fetch('{{ route('admin.ai.seo') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            prompt: title,
                            existing_content: content,
                            content_type: 'seo_metadata'
                        })
                    });
                    
                    const data = await response.json();
                    
                    if (!response.ok) {
                        throw new Error(data.error || 'Failed to generate SEO metadata');
                    }
                    
                    if (data.success && data.seo) {
                        // Populate SEO fields
                        if (data.seo.title) {
                            document.getElementById('seo_title').value = data.seo.title;
                        }
                        if (data.seo.description) {
                            document.getElementById('seo_description').value = data.seo.description;
                        }
                        if (data.seo.keywords) {
                            document.getElementById('seo_keywords').value = data.seo.keywords;
                        }
                        
                        // Close modal
                        this.open = false;
                        setTimeout(() => {
                            document.getElementById('ai-seo-modal')?.remove();
                        }, 300);
                    } else {
                        throw new Error('Invalid response from AI service');
                    }
                } catch (error) {
                    console.error('SEO generation error:', error);
                    this.error = error.message || 'An error occurred while generating SEO metadata';
                } finally {
                    this.loading = false;
                }
            }
        };
    }
});
</script>
@endpush

<x-ai-content-modal />

@endsection
