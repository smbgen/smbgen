<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminClientFileController extends Controller
{
    public function index(Client $client, Request $request)
    {
        $query = ClientFile::where('client_id', $client->id)->with('uploader');

        // Filter by visibility
        if ($request->has('visibility')) {
            if ($request->visibility === 'public') {
                $query->publicFiles();
            } elseif ($request->visibility === 'private') {
                $query->privateFiles();
            }
        }

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->ofCategory($request->category);
        }

        $files = $query->latest()->paginate(20)->withQueryString();

        return view('admin/client_files/index', compact('client', 'files'));
    }

    /**
     * Show an overview of clients and their file counts for quick navigation
     */
    public function all()
    {
        $clients = Client::withCount('files')->orderBy('created_at', 'desc')->paginate(20);

        // Calculate total storage used
        $totalStorageUsed = ClientFile::sum('file_size');

        // Check if using cloud storage (S3/Laravel Cloud)
        $usingCloudStorage = config('filesystems.disks.private.driver') === 's3';

        // Get disk space info (only meaningful for local storage)
        if (! $usingCloudStorage) {
            $storagePath = storage_path('app');
            $diskFreeSpace = disk_free_space($storagePath);
            $diskTotalSpace = disk_total_space($storagePath);
        } else {
            // Cloud storage doesn't have meaningful free/total space limits
            $diskFreeSpace = null;
            $diskTotalSpace = null;
        }

        return view('admin.client_files.all', compact('clients', 'totalStorageUsed', 'diskFreeSpace', 'diskTotalSpace', 'usingCloudStorage'));
    }

    /**
     * Show files uploaded by a specific user (not associated with clients)
     */
    public function userFiles(\App\Models\User $user, Request $request)
    {
        $query = ClientFile::where('user_id', $user->id)->whereNull('client_id');

        // Filter by visibility
        if ($request->has('visibility')) {
            if ($request->visibility === 'public') {
                $query->publicFiles();
            } elseif ($request->visibility === 'private') {
                $query->privateFiles();
            }
        }

        // Filter by category
        if ($request->has('category') && $request->category !== 'all') {
            $query->ofCategory($request->category);
        }

        $files = $query->latest()->paginate(20)->withQueryString();

        return view('admin.client_files.user_files', compact('user', 'files'));
    }

    /**
     * Store a file for a user (not associated with a client)
     */
    public function storeUserFile(\App\Models\User $user, Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:102400|mimes:pdf,doc,docx,txt,jpg,jpeg,png,gif,xls,xlsx,csv,zip', // 100MB, allowed file types
            'is_public' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $isPublic = $request->boolean('is_public', false);

        // Generate unique filename
        $filename = uniqid().'-'.$file->getClientOriginalName();

        // Store in public or private disk (automatically uses cloud storage if connected)
        $disk = $isPublic ? 'public_cloud' : 'private';
        $path = $file->storeAs("user_files/{$user->id}", $filename, $disk);

        // Get file metadata
        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();
        $extension = $file->getClientOriginalExtension();

        $clientFile = ClientFile::create([
            'client_id' => null, // No client association
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'uploaded_by' => 'admin',
            'user_id' => $user->id,
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'file_extension' => $extension,
            'is_public' => $isPublic,
            'description' => $request->description,
        ]);

        // Log activity
        \App\Services\ActivityLogger::logFileUpload($clientFile, [
            'user_name' => $user->name,
            'visibility' => $isPublic ? 'public' : 'private',
            'size' => $fileSize,
        ]);

        $visibility = $isPublic ? 'public' : 'private';

        return back()->with('success', "File uploaded successfully to {$visibility} storage.");
    }

    /**
     * Delete a user file
     */
    public function destroyUserFile(\App\Models\User $user, ClientFile $file)
    {
        if ($file->user_id !== $user->id || $file->client_id !== null) {
            return response()->view('errors.403', [], 403);
        }

        // Log activity before deletion
        \App\Services\ActivityLogger::logFileDelete($file);

        // Delete from appropriate disk
        $disk = $file->getStorageDisk();
        Storage::disk($disk)->delete($file->path);

        $file->delete();

        return back()->with('success', 'File deleted.');
    }

    /**
     * Download a user file
     */
    public function downloadUserFile(\App\Models\User $user, ClientFile $file)
    {
        if ($file->user_id !== $user->id || $file->client_id !== null) {
            return response()->view('errors.403', [], 403);
        }

        // Log activity
        \App\Services\ActivityLogger::logFileDownload($file);

        $disk = $file->getStorageDisk();

        return Storage::disk($disk)->download($file->path, $file->original_name);
    }

    public function store(Client $client, Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:102400|mimes:pdf,doc,docx,txt,jpg,jpeg,png,gif,xls,xlsx,csv,zip', // 100MB, allowed file types
            'is_public' => 'nullable|boolean',
            'description' => 'nullable|string|max:500',
        ]);

        $file = $request->file('file');
        $isPublic = $request->boolean('is_public', false);

        // Generate unique filename
        $filename = uniqid().'-'.$file->getClientOriginalName();

        // Store in public or private disk (automatically uses cloud storage if connected)
        $disk = $isPublic ? 'public_cloud' : 'private';
        $path = $file->storeAs("client_files/{$client->id}", $filename, $disk);

        // Get file metadata
        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();
        $extension = $file->getClientOriginalExtension();

        $clientFile = ClientFile::create([
            'client_id' => $client->id,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'uploaded_by' => 'admin',
            'user_id' => auth()->id(),
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'file_extension' => $extension,
            'is_public' => $isPublic,
            'description' => $request->description,
        ]);

        // Log activity
        \App\Services\ActivityLogger::logFileUpload($clientFile, [
            'client_name' => $client->name,
            'visibility' => $isPublic ? 'public' : 'private',
            'size' => $fileSize,
        ]);

        $visibility = $isPublic ? 'public' : 'private';

        return back()->with('success', "File uploaded successfully to {$visibility} storage.");
    }

    public function destroy(Client $client, ClientFile $file)
    {
        if ($file->client_id !== $client->id) {
            return response()->view('errors.403', [], 403);
        }

        // Log activity before deletion
        \App\Services\ActivityLogger::logFileDelete($file);

        // Delete from appropriate disk
        $disk = $file->getStorageDisk();
        Storage::disk($disk)->delete($file->path);

        $file->delete();

        return back()->with('success', 'File deleted.');
    }

    public function download(Client $client, ClientFile $file)
    {
        if ($file->client_id !== $client->id) {
            return response()->view('errors.403', [], 403);
        }

        // Log activity
        \App\Services\ActivityLogger::logFileDownload($file);

        $disk = $file->getStorageDisk();

        return Storage::disk($disk)->download($file->path, $file->original_name);
    }
}
