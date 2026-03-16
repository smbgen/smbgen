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
            'WORD_DOCUMENT'      => 'fa-file-word',
            'POWERPOINT'         => 'fa-file-powerpoint',
            default              => 'fa-file',
        };
    }

    public function getTypeIconColorAttribute(): string
    {
        return match ($this->type) {
            'HTML_PRESENTATION'  => 'text-purple-400',
            'HTML_EMAIL'         => 'text-blue-400',
            'PDF_DOCUMENT'       => 'text-red-400',
            'MARKDOWN_RESEARCH'  => 'text-green-400',
            'JSON_DATA'          => 'text-yellow-400',
            'WORD_DOCUMENT'      => 'text-blue-300',
            'POWERPOINT'         => 'text-orange-400',
            default              => 'text-gray-400',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'HTML_PRESENTATION'  => 'Presentation',
            'HTML_EMAIL'         => 'Email HTML',
            'PDF_DOCUMENT'       => 'PDF',
            'MARKDOWN_RESEARCH'  => 'Markdown',
            'JSON_DATA'          => 'JSON',
            'WORD_DOCUMENT'      => 'Word',
            'POWERPOINT'         => 'PowerPoint',
            default              => 'File',
        };
    }

    public function getRoleLabelAttribute(): string
    {
        return match ($this->role) {
            'deliverable'    => 'Deliverable',
            'research'       => 'Research',
            'data'           => 'Data',
            'email_template' => 'Email Template',
            default          => ucfirst($this->role),
        };
    }

    public function isIndexFile(): bool
    {
        return str_contains(strtolower($this->original_name), '-index.')
            || str_contains(strtolower($this->display_name), 'index');
    }
}
