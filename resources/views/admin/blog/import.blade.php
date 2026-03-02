@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto py-6">
    <h1 class="admin-page-title mb-6">Import from WordPress</h1>

    @if ($errors->any())
        <div class="alert alert-error mb-6">
            @foreach ($errors->all() as $error)
                <p>{{ $error }}</p>
            @endforeach
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <form method="POST" action="{{ route('admin.blog.import.process') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-6">
                <label class="form-label">WordPress XML Export File</label>
                <input type="file" name="wordpress_xml" accept=".xml" required class="form-input">
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    Export your WordPress content from Tools → Export in your WordPress admin.
                </p>
            </div>

            <div class="mb-4">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="import_categories" value="1" checked class="form-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Import Categories</span>
                </label>
            </div>

            <div class="mb-4">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="import_tags" value="1" checked class="form-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Import Tags</span>
                </label>
            </div>

            <div class="mb-6">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" name="set_as_published" value="1" class="form-checkbox rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                    <span class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-100">Set all imported posts as Published (otherwise they'll be drafts)</span>
                </label>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-upload mr-2"></i>Import
                </button>
                <a href="{{ route('admin.blog.posts.index') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
