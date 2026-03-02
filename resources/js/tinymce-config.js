/**
 * TinyMCE Configuration for Blog Editor
 */

import tinymce from 'tinymce/tinymce';

// Import theme and plugins
import 'tinymce/themes/silver';
import 'tinymce/icons/default';
import 'tinymce/plugins/link';
import 'tinymce/plugins/image';
import 'tinymce/plugins/code';
import 'tinymce/plugins/lists';
import 'tinymce/plugins/table';
import 'tinymce/plugins/media';
import 'tinymce/plugins/wordcount';
import 'tinymce/plugins/codesample';
import 'tinymce/plugins/fullscreen';
import 'tinymce/plugins/searchreplace';
import 'tinymce/plugins/autoresize';

/**
 * Initialize TinyMCE editor
 * @param {string} selector - CSS selector for textarea
 * @param {object} customOptions - Additional options to override defaults
 */
export function initTinyMCE(selector, customOptions = {}) {
    const defaultOptions = {
        selector: selector,
        skin: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'oxide-dark' : 'oxide',
        content_css: window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'default',
        height: 500,
        menubar: true,
        plugins: [
            'link', 'image', 'code', 'lists', 'table', 'media',
            'wordcount', 'codesample', 'fullscreen', 'searchreplace', 'autoresize'
        ],
        toolbar: 'undo redo | formatselect | bold italic underline strikethrough | ' +
                'forecolor backcolor | link image media | alignleft aligncenter alignright alignjustify | ' +
                'bullist numlist outdent indent | removeformat | codesample code | fullscreen',
        toolbar_mode: 'sliding',
        contextmenu: 'link image table',
        image_advtab: true,
        automatic_uploads: true,
        file_picker_types: 'image',
        content_style: 'body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif; font-size: 16px; }',
        
        // Image picker callback - integrates with CMS Image Library
        file_picker_callback: function(callback, value, meta) {
            if (meta.filetype === 'image') {
                // Open CMS image library modal
                openCmsImageLibrary(callback);
            }
        },
        
        // Setup callback for additional customization
        setup: function(editor) {
            // Add custom button for CMS images
            editor.ui.registry.addButton('cmsimages', {
                icon: 'image',
                tooltip: 'Insert from CMS Library',
                onAction: function() {
                    openCmsImageLibrary((url, meta) => {
                        editor.insertContent(`<img src="${url}" alt="${meta.alt || ''}" />`);
                    });
                }
            });
            
            // Handle dark mode changes
            const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            darkModeMediaQuery.addEventListener('change', (e) => {
                editor.getParam('skin', e.matches ? 'oxide-dark' : 'oxide');
            });
        },
        
        // Link configuration
        link_default_target: '_blank',
        link_title: false,
        link_context_toolbar: true,
        
        // Code sample configuration
        codesample_languages: [
            { text: 'HTML/XML', value: 'markup' },
            { text: 'JavaScript', value: 'javascript' },
            { text: 'CSS', value: 'css' },
            { text: 'PHP', value: 'php' },
            { text: 'Python', value: 'python' },
            { text: 'Java', value: 'java' },
            { text: 'C', value: 'c' },
            { text: 'C#', value: 'csharp' },
            { text: 'C++', value: 'cpp' },
            { text: 'SQL', value: 'sql' },
            { text: 'Bash', value: 'bash' }
        ],
        
        // Auto-resize
        autoresize_bottom_margin: 50,
        autoresize_overflow_padding: 50,
        max_height: 800,
        min_height: 300,
    };
    
    const options = { ...defaultOptions, ...customOptions };
    
    tinymce.init(options);
}

/**
 * Open CMS Image Library modal
 */
function openCmsImageLibrary(callback) {
    // Check if modal already exists
    let modal = document.getElementById('tinymce-cms-image-modal');
    
    if (!modal) {
        modal = document.createElement('div');
        modal.id = 'tinymce-cms-image-modal';
        modal.className = 'fixed inset-0 bg-black bg-opacity-75 z-[9999] flex items-center justify-center p-4';
        modal.innerHTML = `
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-6xl w-full max-h-[90vh] overflow-hidden flex flex-col">
                <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100">Select Image from CMS Library</h3>
                        <button type="button" class="text-gray-500 hover:text-gray-700 dark:hover:text-gray-300" id="close-cms-modal">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="flex-1 overflow-y-auto p-6">
                    <div id="cms-images-grid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                        <div class="col-span-full text-center py-8">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto"></div>
                            <p class="mt-4 text-gray-600 dark:text-gray-400">Loading images...</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        
        // Close modal on click outside or close button
        modal.addEventListener('click', (e) => {
            if (e.target === modal || e.target.closest('#close-cms-modal')) {
                modal.remove();
            }
        });
        
        // Fetch and display images
        fetch('/admin/cms/images/api')
            .then(response => response.json())
            .then(data => {
                const grid = document.getElementById('cms-images-grid');
                
                if (data.images && data.images.length > 0) {
                    grid.innerHTML = data.images.map(img => `
                        <div class="cms-image-item cursor-pointer group relative rounded-lg overflow-hidden border-2 border-gray-200 dark:border-gray-700 hover:border-blue-500 transition-all"
                             data-url="${img.url}"
                             data-alt="${img.alt_text || ''}"
                             data-width="${img.width || ''}"
                             data-height="${img.height || ''}">
                            <div class="aspect-square bg-gray-100 dark:bg-gray-900">
                                <img src="${img.url}" alt="${img.alt_text || ''}" class="w-full h-full object-cover">
                            </div>
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-50 transition-all flex items-center justify-center">
                                <span class="text-white opacity-0 group-hover:opacity-100 transition-opacity">
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </span>
                            </div>
                            <div class="p-2 bg-white dark:bg-gray-800">
                                <p class="text-xs text-gray-600 dark:text-gray-400 truncate">${img.original_name || 'Image'}</p>
                            </div>
                        </div>
                    `).join('');
                    
                    // Add click handlers
                    grid.querySelectorAll('.cms-image-item').forEach(item => {
                        item.addEventListener('click', function() {
                            const url = this.dataset.url;
                            const alt = this.dataset.alt;
                            const width = this.dataset.width;
                            const height = this.dataset.height;
                            
                            callback(url, {
                                alt: alt,
                                width: width,
                                height: height
                            });
                            
                            modal.remove();
                        });
                    });
                } else {
                    grid.innerHTML = `
                        <div class="col-span-full text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p class="mt-4 text-gray-600 dark:text-gray-400">No images found</p>
                            <a href="/admin/cms/images/create" target="_blank" class="mt-2 inline-block text-blue-500 hover:text-blue-600">
                                Upload images →
                            </a>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading images:', error);
                document.getElementById('cms-images-grid').innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <p class="text-red-600">Error loading images. Please try again.</p>
                    </div>
                `;
            });
    }
}

export default { initTinyMCE };
