<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Package;
use App\Models\PackageFile;
use App\Services\PackageIngestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PackageController extends Controller
{
    public function __construct(private PackageIngestService $ingest) {}

    /**
     * List all packages, optionally filtered by client.
     */
    public function index(Request $request)
    {
        $query = Package::with(['client', 'files'])->latest();

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $packages = $query->paginate(20)->withQueryString();
        $clients = Client::orderBy('name')->get(['id', 'name']);

        return view('admin.packages.index', compact('packages', 'clients'));
    }

    /**
     * Show the upload form.
     */
    public function create(Request $request)
    {
        $clients = Client::where('is_active', true)->orderBy('name')->get(['id', 'name']);
        $selectedClient = $request->filled('client_id')
            ? Client::find($request->client_id)
            : null;

        return view('admin.packages.create', compact('clients', 'selectedClient'));
    }

    /**
     * Parse uploaded files and return the review screen (no DB write yet).
     */
    public function review(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'upload_type' => 'required|in:zip,multi',
            'zip_file' => 'required_if:upload_type,zip|file|mimes:zip|max:51200',
            'files.*' => 'required_if:upload_type,multi|file|max:51200',
        ]);

        $client = Client::findOrFail($request->client_id);

        if ($request->upload_type === 'zip') {
            $draft = $this->ingest->parseZip($request->file('zip_file'));
        } else {
            $draft = $this->ingest->parseMultiFile($request->file('files'));
        }

        // Store uploaded files in session-keyed temp storage so we can
        // retrieve them at commit time without re-uploading.
        $sessionKey = 'pkg_review_'.uniqid();
        $tmpDir = $draft['tmp_dir'] ?? null;
        $multiFilePaths = [];

        if ($request->upload_type === 'multi') {
            foreach ($request->file('files') as $i => $file) {
                $tmpPath = $file->store('tmp/pkg_review', 'private');
                $multiFilePaths[$i] = $tmpPath;
            }
        }

        session([
            $sessionKey => [
                'client_id' => $client->id,
                'source' => $draft['source'],
                'original_filename' => $draft['original_filename'],
                'tmp_dir' => $tmpDir,
                'multi_file_paths' => $multiFilePaths,
                'upload_type' => $request->upload_type,
            ],
        ]);

        $fileTypes = [
            'HTML_PRESENTATION', 'HTML_EMAIL', 'PDF_DOCUMENT',
            'MARKDOWN_RESEARCH', 'JSON_DATA', 'POWERPOINT', 'WORD_DOCUMENT', 'OTHER',
        ];
        $roles = ['deliverable', 'research', 'data', 'email_template'];

        return view('admin.packages.review', [
            'client' => $client,
            'draft' => $draft,
            'sessionKey' => $sessionKey,
            'fileTypes' => $fileTypes,
            'roles' => $roles,
        ]);
    }

    /**
     * Commit the reviewed package to the database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'session_key' => 'required|string',
            'name' => 'required|string|max:255',
            'files' => 'required|array',
            'files.*.original_name' => 'required|string',
            'files.*.display_name' => 'required|string',
            'files.*.type' => 'required|string',
            'files.*.role' => 'required|in:deliverable,research,data,email_template',
            'files.*.tmp_relative_path' => 'nullable|string',
            'files.*.group_label' => 'nullable|string',
        ]);

        $sessionKey = $request->session_key;
        $sessionData = session($sessionKey);

        if (! $sessionData) {
            return back()->withErrors(['session_key' => 'Session expired. Please re-upload your files.']);
        }

        $client = Client::findOrFail($sessionData['client_id']);

        // Rebuild UploadedFile-like objects from stored temp paths for multi-file
        $uploadedFiles = [];
        if ($sessionData['upload_type'] === 'multi') {
            foreach ($sessionData['multi_file_paths'] as $i => $tmpPath) {
                $uploadedFiles[$i] = new \SplFileInfo(Storage::disk('private')->path($tmpPath));
            }
        }

        $reviewedData = [
            'name' => $request->name,
            'source' => $sessionData['source'],
            'original_filename' => $sessionData['original_filename'],
            'files' => $request->files_data ?? $request->input('files'),
        ];

        $package = $this->ingest->commit(
            $client,
            auth()->id(),
            $reviewedData,
            $sessionData['tmp_dir'],
            $uploadedFiles
        );

        // Clean up temp paths for multi-file
        foreach ($sessionData['multi_file_paths'] ?? [] as $tmpPath) {
            Storage::disk('private')->delete($tmpPath);
        }

        session()->forget($sessionKey);

        return redirect()->route('admin.packages.show', $package)
            ->with('success', "Package \"{$package->name}\" created with ".count($reviewedData['files']).' files.');
    }

    /**
     * Show package detail with three tabs.
     */
    public function show(Package $package)
    {
        $package->load(['files', 'client', 'createdBy']);

        $deliverables = $package->files->where('role', 'deliverable')->values();
        $researchFiles = $package->files->whereIn('role', ['research', 'data'])->values();
        $emailTemplates = $package->files->where('role', 'email_template')->values();

        // Pin index files to the top of research
        $researchFiles = $researchFiles->sortByDesc(fn ($f) => $f->isIndexFile())->values();

        return view('admin.packages.show', compact(
            'package',
            'deliverables',
            'researchFiles',
            'emailTemplates'
        ));
    }

    /**
     * Update package status.
     */
    public function updateStatus(Request $request, Package $package)
    {
        $request->validate(['status' => 'required|in:draft,ready,sent']);
        $package->update(['status' => $request->status]);

        return back()->with('success', 'Package status updated.');
    }

    /**
     * Toggle portal_enabled on a package.
     */
    public function togglePortal(Package $package)
    {
        $package->update(['portal_enabled' => ! $package->portal_enabled]);

        $msg = $package->portal_enabled ? 'Client portal enabled.' : 'Client portal disabled.';

        return back()->with('success', $msg);
    }

    /**
     * Toggle portal_promoted on a file.
     */
    public function togglePromote(Package $package, PackageFile $file)
    {
        abort_if($file->package_id !== $package->id, 403);
        $file->update(['portal_promoted' => ! $file->portal_promoted]);

        return response()->json(['portal_promoted' => $file->portal_promoted]);
    }

    /**
     * Serve a package file for admin preview (inline, no download).
     */
    public function previewFile(Package $package, PackageFile $file)
    {
        abort_if($file->package_id !== $package->id, 403);

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

        $disposition = in_array($file->type, ['WORD_DOCUMENT', 'POWERPOINT'])
            ? 'attachment'
            : 'inline';

        return response($contents, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', $disposition.'; filename="'.addslashes($file->original_name).'"');
    }

    /**
     * Return raw file content (for markdown/JSON preview in the research tab).
     */
    public function fileContent(Package $package, PackageFile $file)
    {
        abort_if($file->package_id !== $package->id, 403);

        $disk = $file->storage_disk ?: 'private';
        $contents = Storage::disk($disk)->get($file->storage_path);

        return response()->json(['content' => $contents ?? '']);
    }
}
