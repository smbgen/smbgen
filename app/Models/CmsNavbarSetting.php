<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsNavbarSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'logo_text',
        'logo_image_url',
        'use_business_colors',
        'custom_bg_color',
        'custom_text_color',
        'menu_items',
        'is_sticky',
    ];

    protected function casts(): array
    {
        return [
            'use_business_colors' => 'boolean',
            'is_sticky' => 'boolean',
            'menu_items' => 'array',
        ];
    }

    /**
     * Get the singleton navbar settings
     */
    public static function getSettings()
    {
        return static::firstOrCreate([], [
            'logo_text' => config('business.name'),
            'use_business_colors' => true,
            'menu_items' => [
                ['label' => 'Home', 'url' => '/', 'target' => '_self', 'order' => 1],
                ['label' => 'Contact', 'url' => '/contact', 'target' => '_self', 'order' => 2],
                ['label' => 'Book', 'url' => '/book', 'target' => '_self', 'order' => 3],
            ],
        ]);
    }

    /**
     * Get the background color for navbar
     */
    public function getBackgroundColor(): string
    {
        if ($this->use_business_colors) {
            $companyColors = \App\Models\CmsCompanyColors::getSettings();

            return $companyColors->getNavbarColor();
        }

        return $this->custom_bg_color ?? '#1f2937';
    }

    /**
     * Get the text color for navbar
     */
    public function getTextColor(): string
    {
        if ($this->use_business_colors) {
            $companyColors = \App\Models\CmsCompanyColors::getSettings();

            return $companyColors->getNavbarTextColor();
        }

        return $this->custom_text_color ?? '#ffffff';
    }

    /**
     * Get ordered menu items with normalized structure
     */
    public function getOrderedMenuItems(): array
    {
        $items = $this->menu_items ?? [];

        // Normalize items to ensure all have required fields
        $items = array_map(function ($item) {
            return array_merge([
                'label' => '',
                'url' => '',
                'target' => '_self',
                'style' => '',
                'order' => 0,
            ], $item);
        }, $items);

        usort($items, fn ($a, $b) => ($a['order'] ?? 0) <=> ($b['order'] ?? 0));

        return $items;
    }
}
