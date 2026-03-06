<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class CmsImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'filename',
        'original_name',
        'path',
        'mime_type',
        'size',
        'width',
        'height',
        'alt_text',
        'user_id',
    ];

    protected $casts = [
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    /**
     * Get the user who uploaded this image
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get human-readable file size
     */
    public function getFormattedSizeAttribute(): string
    {
        if (! $this->size) {
            return 'Unknown';
        }

        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    /**
     * Get the storage disk for CMS images
     */
    public function getStorageDisk(): string
    {
        return 'public_cloud';
    }

    /**
     * Get the full URL for the image
     * Always uses the app's /assets route to keep CMS HTML clean and stable.
     */
    public function getUrl(): string
    {
        return url('/assets/'.ltrim($this->path, '/'));
    }

    /**
     * Get HTML img tag for this image
     */
    public function getImgTag(array $attributes = []): string
    {
        $defaultAttributes = [
            'src' => $this->getUrl(),
            'alt' => $this->alt_text ?: $this->original_name,
        ];

        $attributes = array_merge($defaultAttributes, $attributes);

        $attrString = '';
        foreach ($attributes as $key => $value) {
            $attrString .= ' '.$key.'="'.htmlspecialchars($value).'"';
        }

        return '<img'.$attrString.'>';
    }

    /**
     * Get markdown image syntax
     */
    public function getMarkdown(): string
    {
        $alt = $this->alt_text ?: $this->original_name;

        return '!['.$alt.']('.$this->getUrl().')';
    }

    /**
     * Check if image is an actual image file
     */
    public function isImage(): bool
    {
        return $this->mime_type && str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Get image dimensions (if available)
     */
    public function getDimensions(): ?array
    {
        if (! $this->isImage()) {
            return null;
        }

        try {
            $disk = $this->getStorageDisk();
            $fullPath = Storage::disk($disk)->path($this->path);
            $size = getimagesize($fullPath);

            if ($size) {
                return [
                    'width' => $size[0],
                    'height' => $size[1],
                ];
            }
        } catch (\Exception $e) {
            // Ignore errors
        }

        return null;
    }
}
