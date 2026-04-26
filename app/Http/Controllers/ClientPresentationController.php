<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Package;
use App\Models\PackageFile;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ClientPresentationController extends Controller
{
    public function index(): View
    {
        $client = $this->currentClient();

        $packages = Package::query()
            ->visibleInPortalForClient($client)
            ->withCount([
                'files as visible_deliverables_count' => function ($query): void {
                    $query->where('role', 'deliverable')
                        ->where('portal_promoted', true);
                },
            ])
            ->latest()
            ->paginate(12);

        return view('client.presentations.index', compact('packages'));
    }

    public function show(Package $package): View
    {
        $client = $this->currentClient();

        abort_if($package->client_id !== $client->id || ! $package->portal_enabled, 403);

        $visibleFiles = $package->promotedDeliverables()->get();

        abort_if($visibleFiles->isEmpty(), 403);

        return view('client.presentations.show', compact('package', 'visibleFiles'));
    }

    public function previewFile(Package $package, PackageFile $file): Response
    {
        $client = $this->currentClient();

        abort_if($package->client_id !== $client->id || ! $package->portal_enabled, 403);
        abort_if($file->package_id !== $package->id, 403);
        abort_if($file->role !== 'deliverable' || ! $file->portal_promoted, 403);

        $disk = $file->storage_disk ?: 'private';
        $contents = Storage::disk($disk)->get($file->storage_path);

        if ($contents === null) {
            abort(404, 'File not found in storage.');
        }

        $mimeMap = [
            'HTML_PRESENTATION' => 'text/html',
            'HTML_EMAIL' => 'text/html',
            'PDF_DOCUMENT' => 'application/pdf',
            'MARKDOWN_RESEARCH' => 'text/plain',
            'JSON_DATA' => 'application/json',
            'WORD_DOCUMENT' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'POWERPOINT' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ];
        $mime = $mimeMap[$file->type] ?? 'application/octet-stream';

        $disposition = in_array($file->type, ['WORD_DOCUMENT', 'POWERPOINT'], true)
            ? 'attachment'
            : 'inline';

        return response($contents, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', $disposition.'; filename="'.addslashes($file->original_name).'"');
    }

    private function currentClient(): Client
    {
        $client = Client::where('email', Auth::user()->email)->first();

        abort_if(! $client, 403);

        return $client;
    }
}
