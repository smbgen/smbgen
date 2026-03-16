<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    protected $fillable = [
        'name',
        'client_id',
        'created_by_user_id',
        'status',
        'source',
        'original_filename',
        'portal_enabled',
    ];

    protected $casts = [
        'portal_enabled' => 'boolean',
    ];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(PackageFile::class)->orderBy('sort_order')->orderBy('id');
    }

    public function deliverables(): HasMany
    {
        return $this->hasMany(PackageFile::class)->where('role', 'deliverable')->orderBy('sort_order');
    }

    public function researchFiles(): HasMany
    {
        return $this->hasMany(PackageFile::class)->where('role', 'research')->orderBy('sort_order');
    }

    public function dataFiles(): HasMany
    {
        return $this->hasMany(PackageFile::class)->where('role', 'data')->orderBy('sort_order');
    }

    public function emailTemplates(): HasMany
    {
        return $this->hasMany(PackageFile::class)->where('role', 'email_template')->orderBy('sort_order');
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match ($this->status) {
            'draft'  => 'bg-yellow-900/40 text-yellow-300 border border-yellow-700',
            'ready'  => 'bg-blue-900/40 text-blue-300 border border-blue-700',
            'sent'   => 'bg-green-900/40 text-green-300 border border-green-700',
            default  => 'bg-gray-700 text-gray-300',
        };
    }

    public function getDeliverableCountAttribute(): int
    {
        return $this->files->where('role', 'deliverable')->count();
    }

    public function getResearchCountAttribute(): int
    {
        return $this->files->where('role', 'research')->count();
    }

    public function getResearchAndDataCountAttribute(): int
    {
        return $this->files->whereIn('role', ['research', 'data'])->count();
    }

    public function getEmailTemplateCountAttribute(): int
    {
        return $this->files->where('role', 'email_template')->count();
    }

    public function getTotalFileCountAttribute(): int
    {
        return $this->files->count();
    }
}
