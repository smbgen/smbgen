<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageFile extends Model
{
    protected $fillable = [
        'package_id',
        'original_name',
        'display_name',
        'type',
        'role',
        'group_label',
        'storage_path',
        'storage_disk',
        'size_bytes',
        'portal_promoted',
        'sort_order',
    ];

    protected $casts = [
        'portal_promoted' => 'boolean',
        'size_bytes'      => 'integer',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size_bytes;
        if ($bytes === 0) {
            return '0 B';
        }
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 1).' '.$units[$i];
    }

    public function getTypeBadgeClassAttribute(): string
    {
        return match ($this->type) {
            'HTML_PRESENTATION'  => 'bg-purple-900/40 text-purple-300 border border-purple-700',
            'HTML_EMAIL'         => 'bg-blue-900/40 text-blue-300 border border-blue-700',
            'PDF_DOCUMENT'       => 'bg-red-900/40 text-red-300 border border-red-700',
            'MARKDOWN_RESEARCH'  => 'bg-green-900/40 text-green-300 border border-green-700',
            'JSON_DATA'          => 'bg-yellow-900/40 text-yellow-300 border border-yellow-700',
            default              => 'bg-gray-700 text-gray-300',
        };
    }

    public function getRoleBadgeClassAttribute(): string
    {
        return match ($this->role) {
            'deliverable'    => 'bg-blue-900/40 text-blue-300 border border-blue-700',
            'research'       => 'bg-green-900/40 text-green-300 border border-green-700',
            'data'           => 'bg-yellow-900/40 text-yellow-300 border border-yellow-700',
            'email_template' => 'bg-purple-900/40 text-purple-300 border border-purple-700',
            default          => 'bg-gray-700 text-gray-300',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'HTML_PRESENTATION'  => 'fa-desktop',
            'HTML_EMAIL'         => 'fa-envelope',
            'PDF_DOCUMENT'       => 'fa-file-pdf',
            'MARKDOWN_RESEARCH'  => 'fa-file-alt',
            'JSON_DATA'          => 'fa-code',
            default              => 'fa-file',
        };
    }

    public function isIndexFile(): bool
    {
        return str_contains(strtolower($this->original_name), '-index.')
            || str_contains(strtolower($this->display_name), 'index');
    }
}
