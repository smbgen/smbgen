@extends('layouts.client')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white dark:bg-gray-900 border border-gray-300 dark:border-gray-800 rounded-xl shadow p-6 overflow-hidden">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">📁 My Documents</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">Secure uploads • Max 50MB</p>
            </div>
            <div class="hidden sm:block text-sm text-gray-600 dark:text-gray-400">You can upload PDFs, images, and documents for our team.</div>
        </div>

        @if(session('success'))
            <div class="mb-4 rounded-md border border-emerald-300 dark:border-emerald-500/30 bg-emerald-50 dark:bg-emerald-500/10 px-4 py-2 text-emerald-800 dark:text-emerald-200">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="mb-4 rounded-md border border-red-300 dark:border-red-500/30 bg-red-50 dark:bg-red-500/10 px-4 py-2 text-red-800 dark:text-red-200">{{ session('error') }}</div>
        @endif

        <form action="{{ route('client.files.upload') }}" method="POST" enctype="multipart/form-data" class="mb-6">
            @csrf
            <div class="flex flex-col sm:flex-row gap-3 sm:items-center">
                <input type="file" name="file" class="block w-full rounded-md border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900/50 text-gray-900 dark:text-gray-200 file:mr-4 file:rounded-l-md file:border-0 file:bg-gray-200 dark:file:bg-gray-700 file:px-4 file:py-2 file:text-gray-900 dark:file:text-gray-100 hover:file:bg-gray-300 dark:hover:file:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-purple-500/50" required />
                <button class="inline-flex items-center justify-center rounded-md bg-purple-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-400/70">Upload</button>
            </div>
            <p class="text-gray-600 dark:text-gray-400 text-xs mt-2">Allowed: PDF, DOCX, XLSX, PNG, JPG • Max size 50MB</p>
        </form>

        <div class="overflow-hidden rounded-lg border border-gray-300 dark:border-gray-800">
            <table class="min-w-full">
                <thead class="bg-gray-100 dark:bg-gray-800">
                    <tr>
                        <th scope="col" class="px-4 py-3 text-left text-sm font-medium uppercase tracking-wider text-gray-700 dark:text-gray-300">File</th>
                        <th scope="col" class="px-4 py-3 text-left text-sm font-medium uppercase tracking-wider text-gray-700 dark:text-gray-300">Uploaded</th>
                        <th scope="col" class="px-4 py-3 text-right text-sm font-medium uppercase tracking-wider text-gray-700 dark:text-gray-300">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900/30 divide-y divide-gray-300 dark:divide-gray-800">
                    @forelse($files as $file)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40">
                            <td class="px-4 py-4 text-gray-900 dark:text-gray-100">{{ $file->original_name }}</td>
                            <td class="px-4 py-4 text-gray-700 dark:text-gray-300">{{ $file->created_at->format('Y-m-d') }}</td>
                            <td class="px-4 py-4 text-right">
                                <div class="inline-flex gap-2 items-center justify-end">
                                    <a class="inline-flex items-center rounded-md border border-gray-300 dark:border-gray-700 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700/60" href="{{ route('client.files.download', $file) }}">Download</a>

                                    <form action="{{ route('client.files.destroy', $file) }}" method="POST" onsubmit="return confirm('Delete this file? This action cannot be undone.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center rounded-md border border-red-400 dark:border-red-600 px-3 py-2 text-sm font-medium text-red-600 dark:text-red-300 hover:bg-red-50 dark:hover:bg-red-700/20">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-4 py-12 text-center text-gray-600 dark:text-gray-400">
                                <div class="mx-auto mb-2 h-10 w-10 rounded-full bg-gray-300 dark:bg-gray-700/40 flex items-center justify-center">📭</div>
                                No files yet. Upload your first document above.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $files->links() }}
        </div>
    </div>
</div>
@endsection


