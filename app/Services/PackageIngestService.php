<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Package;
use App\Models\PackageFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class PackageIngestService
{
    /**
     * Process a zip upload: extract, classify all files, return draft PackageRecord
     * for admin review before saving.
     *
     * @return array{name: string, source: string, original_filename: string, files: array}
     */
    public function parseZip(UploadedFile $zipFile): array
    {
        $zip = new ZipArchive;
        $tmpDir = sys_get_temp_dir().'/pkg_'.Str::random(12);
        mkdir($tmpDir, 0755, true);

        if ($zip->open($zipFile->getPathname()) !== true) {
            throw new \RuntimeException('Could not open zip file.');
        }

        $zip->extractTo($tmpDir);
        $zip->close();

        $files = $this->scanDirectory($tmpDir, $tmpDir);

        return [
            'name' => $this->guessPackageName($zipFile->getClientOriginalName()),
            'source' => 'zip_upload',
            'original_filename' => $zipFile->getClientOriginalName(),
            'tmp_dir' => $tmpDir,
            'files' => $files,
        ];
    }

    /**
     * Process multi-file upload: classify each file, return draft PackageRecord.
     *
     * @param  UploadedFile[]  $uploadedFiles
     * @return array{name: string, source: string, original_filename: null, files: array}
     */
    public function parseMultiFile(array $uploadedFiles): array
    {
        $files = [];
        foreach ($uploadedFiles as $file) {
            $files[] = $this->classifyUploadedFile($file, null);
        }

        return [
            'name' => 'New Package',
            'source' => 'multi_file',
            'original_filename' => null,
            'tmp_dir' => null,
            'files' => $files,
        ];
    }

    /**
     * Commit a reviewed/confirmed package to the database and storage.
     *
     * @param  array  $reviewedData  The POST data from the review form
     * @param  string|null  $tmpDir  Temp directory holding extracted files (for zip uploads)
     * @param  UploadedFile[]  $uploadedFiles  Original uploaded files (for multi-file)
     */
    public function commit(
        Client $client,
        int $createdByUserId,
        array $reviewedData,
        ?string $tmpDir,
        array $uploadedFiles = []
    ): Package {
        $package = Package::create([
            'name' => $reviewedData['name'],
            'client_id' => $client->id,
            'created_by_user_id' => $createdByUserId,
            'status' => 'ready',
            'source' => $reviewedData['source'],
            'original_filename' => $reviewedData['original_filename'] ?? null,
            'portal_enabled' => false,
        ]);

        $packageDir = "packages/{$client->id}/{$package->id}";

        foreach ($reviewedData['files'] as $index => $fileData) {
            $type = $fileData['type'];
            $role = $fileData['role'];
            $subDir = $this->subDirForRole($role);
            $originalName = $fileData['original_name'];

            if ($tmpDir) {
                // Zip upload — file lives in temp dir
                $tmpPath = $tmpDir.'/'.ltrim($fileData['tmp_relative_path'], '/');
                $storagePath = "{$packageDir}/{$subDir}/{$originalName}";
                $disk = 'private';
                Storage::disk($disk)->put($storagePath, file_get_contents($tmpPath));
                $sizeBytes = filesize($tmpPath);
            } else {
                // Multi-file upload — file is an UploadedFile
                $uploadedFile = $uploadedFiles[$index] ?? null;
                if (! $uploadedFile) {
                    continue;
                }
                $storagePath = "{$packageDir}/{$subDir}/{$originalName}";
                $disk = 'private';
                Storage::disk($disk)->put($storagePath, file_get_contents($uploadedFile->getPathname()));
                $sizeBytes = $uploadedFile->getSize();
            }

            PackageFile::create([
                'package_id' => $package->id,
                'original_name' => $originalName,
                'display_name' => $fileData['display_name'],
                'type' => $type,
                'role' => $role,
                'group_label' => $fileData['group_label'] ?? null,
                'storage_path' => $storagePath,
                'storage_disk' => $disk,
                'size_bytes' => $sizeBytes,
                'portal_promoted' => false,
                'sort_order' => $index,
            ]);
        }

        // Write manifest.json
        $this->writeManifest($package, $packageDir);

        // Cleanup temp dir
        if ($tmpDir && is_dir($tmpDir)) {
            $this->rrmdir($tmpDir);
        }

        return $package->fresh(['files', 'client']);
    }

    // -------------------------------------------------------------------------

    private function scanDirectory(string $baseDir, string $currentDir, string $relativeBase = ''): array
    {
        $results = [];
        $entries = scandir($currentDir);

        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $fullPath = $currentDir.'/'.$entry;
            $relativePath = $relativeBase ? $relativeBase.'/'.$entry : $entry;

            if (is_dir($fullPath)) {
                // Recurse — folder name becomes group_label
                $subResults = $this->scanDirectory($baseDir, $fullPath, $relativePath);
                foreach ($subResults as &$sub) {
                    if (! $sub['group_label']) {
                        $sub['group_label'] = $entry;
                    }
                }
                $results = array_merge($results, $subResults);
            } else {
                $classification = $this->classifyFromPath($fullPath, $entry);
                $results[] = array_merge($classification, [
                    'tmp_relative_path' => $relativePath,
                    'group_label' => null, // set by parent folder loop above
                ]);
            }
        }

        return $results;
    }

    private function classifyUploadedFile(UploadedFile $file, ?string $group): array
    {
        $classification = $this->classifyFromPath($file->getPathname(), $file->getClientOriginalName());

        return array_merge($classification, [
            'tmp_relative_path' => null,
            'group_label' => $group,
        ]);
    }

    private function classifyFromPath(string $fullPath, string $filename): array
    {
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $mimeType = mime_content_type($fullPath) ?: 'application/octet-stream';
        $type = $this->detectType($ext, $mimeType, $fullPath, $filename);
        $role = $this->assignRole($type);
        $displayName = $this->extractDisplayName($filename);

        return [
            'original_name' => $filename,
            'display_name' => $displayName,
            'type' => $type,
            'role' => $role,
            'mime_type' => $mimeType,
            'size_bytes' => filesize($fullPath),
        ];
    }

    private function detectType(string $ext, string $mimeType, string $fullPath, string $filename): string
    {
        if ($ext === 'html' || $ext === 'htm' || $mimeType === 'text/html') {
            return $this->disambiguateHtml($fullPath, $filename);
        }

        if ($ext === 'pdf' || $mimeType === 'application/pdf') {
            return 'PDF_DOCUMENT';
        }

        if ($ext === 'md' || $mimeType === 'text/markdown' || $mimeType === 'text/plain') {
            if ($ext === 'md') {
                return 'MARKDOWN_RESEARCH';
            }
        }

        if ($ext === 'json' || $mimeType === 'application/json') {
            return 'JSON_DATA';
        }

        if (in_array($ext, ['pptx', 'ppt'])) {
            return 'POWERPOINT';
        }

        if (in_array($ext, ['docx', 'doc'])) {
            return 'WORD_DOCUMENT';
        }

        return 'OTHER';
    }

    private function disambiguateHtml(string $fullPath, string $filename): string
    {
        // Rule 1: filename contains Email or email
        if (stripos($filename, 'email') !== false) {
            return 'HTML_EMAIL';
        }

        $content = @file_get_contents($fullPath) ?: '';

        // Rule 2: table-based layout (email pattern)
        if (substr_count(strtolower($content), '<table') >= 2) {
            return 'HTML_EMAIL';
        }

        // Rule 3: viewport/scroll styling (presentation pattern)
        if (preg_match('/overflow[\s]*:[\s]*(?:auto|scroll|hidden)/i', $content) ||
            preg_match('/height[\s]*:[\s]*100(?:vh|%)/i', $content)) {
            return 'HTML_PRESENTATION';
        }

        // Default
        return 'HTML_PRESENTATION';
    }

    private function assignRole(string $type): string
    {
        return match ($type) {
            'HTML_EMAIL' => 'email_template',
            'MARKDOWN_RESEARCH' => 'research',
            'JSON_DATA' => 'data',
            default => 'deliverable',
        };
    }

    private function extractDisplayName(string $filename): string
    {
        $name = pathinfo($filename, PATHINFO_FILENAME);

        // Strip leading client prefix pattern: "CLIENTNAME-"
        $name = preg_replace('/^[A-Z0-9]+[-_]/', '', $name);

        // Convert dashes/underscores to spaces
        $name = str_replace(['-', '_'], ' ', $name);

        return ucwords(strtolower(trim($name)));
    }

    private function subDirForRole(string $role): string
    {
        return match ($role) {
            'deliverable' => 'deliverables',
            'research' => 'research',
            'data' => 'research',
            'email_template' => 'email',
            default => 'other',
        };
    }

    private function guessPackageName(string $zipFilename): string
    {
        $name = pathinfo($zipFilename, PATHINFO_FILENAME);

        // Strip timestamp pattern: -20260309T143621Z-3-001 or similar
        $name = preg_replace('/-\d{8}T\d{6}Z.*$/', '', $name);
        $name = preg_replace('/_\d{8}.*$/', '', $name);

        // Strip leading client number prefix: "05_HAMCO" → "HAMCO"
        $name = preg_replace('/^\d+[_-]/', '', $name);

        $name = str_replace(['-', '_'], ' ', $name);

        return ucwords(strtolower(trim($name)));
    }

    private function writeManifest(Package $package, string $packageDir): void
    {
        $manifest = [
            'id' => $package->id,
            'name' => $package->name,
            'client_id' => $package->client_id,
            'created_by' => $package->created_by_user_id,
            'created_at' => $package->created_at->toIso8601String(),
            'status' => $package->status,
            'source' => $package->source,
            'original_filename' => $package->original_filename,
            'portal_enabled' => $package->portal_enabled,
            'files' => $package->files->map(fn ($f) => [
                'id' => $f->id,
                'original_name' => $f->original_name,
                'display_name' => $f->display_name,
                'type' => $f->type,
                'role' => $f->role,
                'group_label' => $f->group_label,
                'storage_path' => $f->storage_path,
                'size_bytes' => $f->size_bytes,
                'portal_promoted' => $f->portal_promoted,
            ])->values()->all(),
        ];

        Storage::disk('private')->put(
            "{$packageDir}/manifest.json",
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
        );
    }

    private function rrmdir(string $dir): void
    {
        foreach (scandir($dir) as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $path = $dir.'/'.$entry;
            is_dir($path) ? $this->rrmdir($path) : unlink($path);
        }
        rmdir($dir);
    }
}
