@extends('layouts.admin')

@section('content')
<div class="py-6 space-y-6" x-data="uploadForm()">

    {{-- Header --}}
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">New Package</h1>
            <p class="admin-page-subtitle">Upload files to create a client presentation package</p>
        </div>
        <a href="{{ route('admin.packages.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    {{-- Server-side validation errors --}}
    @if($errors->any())
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Client-side errors --}}
    <div x-show="clientError" x-cloak class="alert alert-error">
        <i class="fas fa-exclamation-circle"></i>
        <span x-text="clientError"></span>
    </div>

    <form method="POST" action="{{ route('admin.packages.review') }}" enctype="multipart/form-data"
          @submit="startUpload($event)">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Left: Client + upload mode --}}
            <div class="lg:col-span-1 space-y-5">

                {{-- Client selector --}}
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title"><i class="fas fa-user mr-2 text-blue-400"></i>Client</h3>
                    </div>
                    <div class="admin-card-body">
                        <select name="client_id" required
                            class="w-full px-3 py-2 bg-gray-700 border border-gray-600 rounded-lg text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select a client…</option>
                            @foreach($clients as $c)
                                <option value="{{ $c->id }}" @selected(old('client_id', $selectedClient?->id) == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                {{-- Upload mode selector --}}
                <div class="admin-card">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title"><i class="fas fa-upload mr-2 text-blue-400"></i>Upload Method</h3>
                    </div>
                    <div class="admin-card-body space-y-3">
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="radio" name="upload_type" value="zip" x-model="uploadType"
                                class="mt-1 text-blue-500 bg-gray-700 border-gray-500 focus:ring-blue-500">
                            <div>
                                <div class="text-gray-100 font-medium group-hover:text-white">ZIP Upload</div>
                                <div class="text-xs text-gray-400">Google Drive export or agent output ZIP</div>
                            </div>
                        </label>
                        <label class="flex items-start gap-3 cursor-pointer group">
                            <input type="radio" name="upload_type" value="multi" x-model="uploadType"
                                class="mt-1 text-blue-500 bg-gray-700 border-gray-500 focus:ring-blue-500">
                            <div>
                                <div class="text-gray-100 font-medium group-hover:text-white">Individual Files</div>
                                <div class="text-xs text-gray-400">Drag & drop or select multiple files</div>
                            </div>
                        </label>
                    </div>
                </div>

            </div>

            {{-- Right: Upload area --}}
            <div class="lg:col-span-2">

                {{-- ZIP upload --}}
                <div x-show="uploadType === 'zip'" class="admin-card h-full">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title"><i class="fas fa-file-archive mr-2 text-yellow-400"></i>ZIP File</h3>
                        <span class="text-xs text-gray-400">Max 50 MB</span>
                    </div>
                    <div class="admin-card-body">
                        <div class="border-2 border-dashed border-gray-600 rounded-xl p-10 text-center hover:border-blue-500 transition-colors"
                             @dragover.prevent @drop.prevent="handleZipDrop($event)">
                            <i class="fas fa-file-archive text-4xl text-yellow-500 mb-4"></i>
                            <p class="text-gray-300 mb-2" x-text="zipFileName || 'Drag your ZIP here or click to browse'"></p>
                            <p class="text-xs text-gray-500 mb-4">Folder structure is preserved as group labels</p>
                            <label class="btn-secondary cursor-pointer">
                                <i class="fas fa-folder-open mr-2"></i>Browse
                                <input type="file" name="zip_file" accept=".zip" class="hidden"
                                    @change="handleZipPick($event)">
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Multi-file upload --}}
                <div x-show="uploadType === 'multi'" class="admin-card h-full">
                    <div class="admin-card-header">
                        <h3 class="admin-card-title"><i class="fas fa-copy mr-2 text-green-400"></i>Individual Files</h3>
                        <span class="text-xs text-gray-400">Max 50 MB per file</span>
                    </div>
                    <div class="admin-card-body">
                        <div class="border-2 border-dashed border-gray-600 rounded-xl p-10 text-center hover:border-blue-500 transition-colors"
                             @dragover.prevent @drop.prevent="handleMultiDrop($event)">
                            <i class="fas fa-copy text-4xl text-green-500 mb-4"></i>
                            <template x-if="multiFileNames.length === 0">
                                <p class="text-gray-300 mb-2">Drag files here or click to browse</p>
                            </template>
                            <template x-if="multiFileNames.length > 0">
                                <div class="mb-4 text-left space-y-1">
                                    <template x-for="name in multiFileNames" :key="name">
                                        <div class="flex items-center gap-2 text-sm text-gray-300">
                                            <i class="fas fa-file text-gray-500"></i>
                                            <span x-text="name"></span>
                                        </div>
                                    </template>
                                </div>
                            </template>
                            <label class="btn-secondary cursor-pointer">
                                <i class="fas fa-folder-open mr-2"></i>Browse Files
                                <input type="file" name="files[]" multiple class="hidden" id="multi-file-input"
                                    @change="handleMultiPick($event)">
                            </label>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Submit --}}
        <div class="flex justify-end gap-3 mt-6">
            <a href="{{ route('admin.packages.index') }}" class="btn-secondary">Cancel</a>
            <button type="submit" class="btn-primary" :disabled="uploading">
                <template x-if="!uploading">
                    <span><i class="fas fa-magic mr-2"></i>Analyse & Review</span>
                </template>
                <template x-if="uploading">
                    <span><i class="fas fa-spinner fa-spin mr-2"></i>Processing…</span>
                </template>
            </button>
        </div>

    </form>
</div>

@push('scripts')
<script>
const UPLOAD_MAX_BYTES = 50 * 1024 * 1024;

function fmtMb(bytes) {
    return (bytes / 1024 / 1024).toFixed(1) + ' MB';
}

function uploadForm() {
    return {
        uploadType: 'zip',
        zipFileName: null,
        multiFileNames: [],
        uploading: false,
        clientError: null,

        startUpload(e) {
            // Block submit if no file selected (everything else the server validates)
            if (this.uploadType === 'zip') {
                const input = document.querySelector('input[name="zip_file"]');
                if (!input?.files?.length) {
                    e.preventDefault();
                    this.clientError = 'Please select a ZIP file to upload.';
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }
            } else {
                const input = document.getElementById('multi-file-input');
                if (!input?.files?.length) {
                    e.preventDefault();
                    this.clientError = 'Please select at least one file to upload.';
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                    return;
                }
            }

            this.clientError = null;
            this.uploading = true;

            // Safety net: reset button if server never responds
            setTimeout(() => {
                if (this.uploading) {
                    this.uploading = false;
                    this.clientError = 'Upload is taking longer than expected. Please check your connection and try again.';
                }
            }, 5 * 60 * 1000);
        },

        handleZipDrop(e) {
            const file = e.dataTransfer.files[0];
            if (!file) return;
            if (!file.name.toLowerCase().endsWith('.zip')) {
                this.clientError = 'Only .zip files are accepted here.';
                return;
            }
            if (file.size > UPLOAD_MAX_BYTES) {
                this.clientError = `"${file.name}" is ${fmtMb(file.size)} — exceeds the 50 MB limit.`;
                return;
            }
            this.clientError = null;
            this.zipFileName = file.name;
            const dt = new DataTransfer();
            dt.items.add(file);
            document.querySelector('input[name="zip_file"]').files = dt.files;
        },

        handleZipPick(e) {
            const file = e.target.files[0];
            if (!file) return;
            if (file.size > UPLOAD_MAX_BYTES) {
                this.clientError = `"${file.name}" is ${fmtMb(file.size)} — exceeds the 50 MB limit.`;
                e.target.value = '';
                this.zipFileName = null;
                return;
            }
            this.clientError = null;
            this.zipFileName = file.name;
        },

        handleMultiDrop(e) {
            const files = Array.from(e.dataTransfer.files);
            const oversized = files.find(f => f.size > UPLOAD_MAX_BYTES);
            if (oversized) {
                this.clientError = `"${oversized.name}" is ${fmtMb(oversized.size)} — exceeds the 50 MB limit.`;
                return;
            }
            this.clientError = null;
            this.multiFileNames = files.map(f => f.name);
            const dt = new DataTransfer();
            files.forEach(f => dt.items.add(f));
            document.getElementById('multi-file-input').files = dt.files;
        },

        handleMultiPick(e) {
            const files = Array.from(e.target.files);
            const oversized = files.find(f => f.size > UPLOAD_MAX_BYTES);
            if (oversized) {
                this.clientError = `"${oversized.name}" is ${fmtMb(oversized.size)} — exceeds the 50 MB limit.`;
                e.target.value = '';
                this.multiFileNames = [];
                return;
            }
            this.clientError = null;
            this.multiFileNames = files.map(f => f.name);
        },
    };
}
</script>
@endpush
@endsection
