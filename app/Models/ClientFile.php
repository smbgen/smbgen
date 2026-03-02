<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClientFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'filename',
        'original_name',
        'path',
        'uploaded_by',
        'user_id',
        'mime_type',
        'file_size',
        'file_extension',
        'is_public',
        'description',
    ];

    protected $casts = [
        'is_public' => 'boolean',
        'file_size' => 'integer',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get human-readable file size
     */
    public function getFormattedSizeAttribute(): string
    {
        if (! $this->file_size) {
            return 'Unknown';
        }

        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    /**
     * Get Font Awesome icon class based on file type
     */
    public function getFileIcon(): string
    {
        if (! $this->mime_type && ! $this->file_extension) {
            return 'fa-file';
        }

        // Check by MIME type first
        if ($this->mime_type) {
            if (str_starts_with($this->mime_type, 'image/')) {
                return 'fa-file-image';
            }
            if (str_starts_with($this->mime_type, 'video/')) {
                return 'fa-file-video';
            }
            if (str_starts_with($this->mime_type, 'audio/')) {
                return 'fa-file-audio';
            }
            if ($this->mime_type === 'application/pdf') {
                return 'fa-file-pdf';
            }
            if (in_array($this->mime_type, [
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/msword',
            ])) {
                return 'fa-file-word';
            }
            if (in_array($this->mime_type, [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel',
            ])) {
                return 'fa-file-excel';
            }
            if (in_array($this->mime_type, [
                'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                'application/vnd.ms-powerpoint',
            ])) {
                return 'fa-file-powerpoint';
            }
            if (in_array($this->mime_type, ['application/zip', 'application/x-rar-compressed', 'application/x-7z-compressed'])) {
                return 'fa-file-archive';
            }
        }

        // Fallback to extension
        $ext = strtolower($this->file_extension ?? '');
        $iconMap = [
            'pdf' => 'fa-file-pdf',
            'doc' => 'fa-file-word',
            'docx' => 'fa-file-word',
            'xls' => 'fa-file-excel',
            'xlsx' => 'fa-file-excel',
            'ppt' => 'fa-file-powerpoint',
            'pptx' => 'fa-file-powerpoint',
            'jpg' => 'fa-file-image',
            'jpeg' => 'fa-file-image',
            'png' => 'fa-file-image',
            'gif' => 'fa-file-image',
            'svg' => 'fa-file-image',
            'zip' => 'fa-file-archive',
            'rar' => 'fa-file-archive',
            '7z' => 'fa-file-archive',
            'txt' => 'fa-file-alt',
            'csv' => 'fa-file-csv',
        ];

        return $iconMap[$ext] ?? 'fa-file';
    }

    /**
     * Get file type category
     */
    public function getFileCategory(): string
    {
        if (! $this->mime_type) {
            return 'other';
        }

        if (str_starts_with($this->mime_type, 'image/')) {
            return 'image';
        }
        if (str_starts_with($this->mime_type, 'video/')) {
            return 'video';
        }
        if ($this->mime_type === 'application/pdf') {
            return 'document';
        }
        if (in_array($this->mime_type, [
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/msword',
        ])) {
            return 'document';
        }
        if (in_array($this->mime_type, [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel',
        ])) {
            return 'spreadsheet';
        }

        return 'other';
    }

    /**
     * Scope to filter public files
     */
    public function scopePublicFiles($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to filter private files
     */
    public function scopePrivateFiles($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Scope to filter by file category
     */
    public function scopeOfCategory($query, string $category)
    {
        $mimeTypes = match ($category) {
            'image' => ['image/%'],
            'video' => ['video/%'],
            'document' => [
                'application/pdf',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/msword',
            ],
            'spreadsheet' => [
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'application/vnd.ms-excel',
            ],
            default => []
        };

        if (empty($mimeTypes)) {
            return $query;
        }

        return $query->where(function ($q) use ($mimeTypes) {
            foreach ($mimeTypes as $mimeType) {
                if (str_contains($mimeType, '%')) {
                    $q->orWhere('mime_type', 'like', str_replace('%', '', $mimeType).'%');
                } else {
                    $q->orWhere('mime_type', $mimeType);
                }
            }
        });
    }

    /**
     * Check if file is an image
     */
    public function isImage(): bool
    {
        return $this->mime_type && str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Get storage disk based on visibility
     * The disk configurations will automatically use cloud storage when available
     */
    public function getStorageDisk(): string
    {
        return $this->is_public ? 'public_cloud' : 'private';
    }

    /**
     * Get public URL for public files
     * Uses custom /storage route instead of symlink (Laravel Cloud compatible)
     * Returns null for private files
     */
    public function getPublicUrl(): ?string
    {
        if (! $this->is_public) {
            return null;
        }

        return url('/storage/'.$this->path);
    }

    /**
     * Scope to get all public files for CMS usage
     */
    public function scopeForCms($query)
    {
        return $query->where('is_public', true)
            ->orderBy('created_at', 'desc');
    }
}
