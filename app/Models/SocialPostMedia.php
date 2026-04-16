<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class SocialPostMedia extends Model
{
    protected $fillable = [
        'social_post_id',
        'mediable_type',
        'mediable_id',
        'disk',
        'path',
        'mime_type',
        'original_name',
        'caption',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function socialPost(): BelongsTo
    {
        return $this->belongsTo(SocialPost::class);
    }

    /** The originating CmsImage, ClientFile, or InspectionReport. */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    /** Resolve the public URL for this media item. */
    public function getUrl(): ?string
    {
        // If sourced from a CmsImage, delegate to that model
        if ($this->mediable_type === CmsImage::class && $this->mediable) {
            return $this->mediable->getUrl();
        }

        // Direct upload via a configured disk
        if ($this->disk && $this->path) {
            return Storage::disk($this->disk)->url($this->path);
        }

        return null;
    }

    /** Whether this is a video MIME type. */
    public function isVideo(): bool
    {
        return $this->mime_type && str_starts_with($this->mime_type, 'video/');
    }
}
