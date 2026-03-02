<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClientFileController extends Controller
{
    public function index()
    {
        $client = Client::where('email', Auth::user()->email)->first();
        if (! $client) {
            return response()->view('errors.403', [], 403);
        }
        $files = ClientFile::where('client_id', $client->id)->latest()->paginate(10);

        return view('client/files/index', compact('files'));
    }

    public function store(Request $request)
    {
        $client = Client::where('email', Auth::user()->email)->first();
        if (! $client) {
            return response()->view('errors.403', [], 403);
        }

        // Security: Only allow provisioned users to upload files
        if (! $client->isProvisioned()) {
            return back()->with('error', 'You must activate your account before uploading files. Please check your email for the activation link.');
        }

        $request->validate([
            'file' => 'required|file|max:51200', // 50MB
        ]);

        $file = $request->file('file');
        $filename = uniqid().'-'.$file->getClientOriginalName();

        // Client uploads are always private - store on the 'private' disk (uses cloud if connected)
        $path = $file->storeAs("client_files/{$client->id}", $filename, 'private');

        // Get file metadata
        $mimeType = $file->getMimeType();
        $fileSize = $file->getSize();
        $extension = $file->getClientOriginalExtension();

        $clientFile = ClientFile::create([
            'client_id' => $client->id,
            'filename' => $filename,
            'original_name' => $file->getClientOriginalName(),
            'path' => $path,
            'uploaded_by' => 'client',
            'user_id' => auth()->id(),
            'mime_type' => $mimeType,
            'file_size' => $fileSize,
            'file_extension' => $extension,
            'is_public' => false, // Client uploads are always private
            'description' => null,
        ]);

        // Log activity
        \App\Services\ActivityLogger::logFileUpload($clientFile, [
            'size' => $fileSize,
            'visibility' => 'private',
        ]);

        return redirect()->route('client.files')->with('success', 'File uploaded successfully.');
    }

    public function download(ClientFile $file)
    {
        $client = Client::where('email', Auth::user()->email)->first();
        if (! $client || $file->client_id !== $client->id) {
            return response()->view('errors.403', [], 403);
        }

        // Log activity
        \App\Services\ActivityLogger::logFileDownload($file);

        $disk = $file->getStorageDisk();

        return Storage::disk($disk)->download($file->path, $file->original_name);
    }

    public function destroy(ClientFile $file)
    {
        $client = Client::where('email', Auth::user()->email)->first();
        if (! $client || $file->client_id !== $client->id) {
            return response()->view('errors.403', [], 403);
        }

        // Log activity before deletion
        \App\Services\ActivityLogger::logFileDelete($file);

        // Delete from appropriate disk
        $disk = $file->getStorageDisk();
        Storage::disk($disk)->delete($file->path);

        $file->delete();

        return redirect()->route('client.files')->with('success', 'File deleted successfully.');
    }
}
