<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsFormSubmission extends Model
{
    protected $fillable = [
        'cms_page_id',
        'data',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'data' => 'array',
        ];
    }

    /**
     * Get the CMS page this submission belongs to
     */
    public function cmsPage()
    {
        return $this->belongsTo(CmsPage::class);
    }
}
