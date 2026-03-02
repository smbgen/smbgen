<?php

namespace App\Models;

use App\Helpers\HtmlSanitizer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CmsPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'head_content',
        'body_content',
        'footer_scripts',
        'cta_text',
        'cta_url',
        'background_color',
        'text_color',
        'is_published',
        'show_navbar',
        'show_footer',
        'has_form',
        'form_fields',
        'form_submit_button_text',
        'form_success_message',
        'form_redirect_url',
        'notification_email',
        'send_admin_notification',
        'send_client_notification',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'show_navbar' => 'boolean',
            'show_footer' => 'boolean',
            'has_form' => 'boolean',
            'form_fields' => 'array',
            'send_admin_notification' => 'boolean',
            'send_client_notification' => 'boolean',
        ];
    }

    /**
     * Scope to only get published pages
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Get a page by its slug
     */
    public static function findBySlug(string $slug)
    {
        return static::where('slug', $slug)->first();
    }

    public function sanitizedHeadContent(): string
    {
        return HtmlSanitizer::sanitizeHeadContent($this->head_content);
    }

    public function sanitizedBodyContent(): string
    {
        return HtmlSanitizer::sanitizeContent($this->body_content);
    }

    public function sanitizedFooterScripts(): string
    {
        return HtmlSanitizer::sanitizeHeadContent($this->footer_scripts);
    }

    public function safeBackgroundClass(): string
    {
        $background = trim((string) ($this->background_color ?? ''));

        if ($background === '') {
            return 'bg-white';
        }

        if (! preg_match('/^[#a-zA-Z0-9\-\s]+$/', $background)) {
            return 'bg-white';
        }

        return $background;
    }

    public function safeTextClass(): string
    {
        $text = trim((string) ($this->text_color ?? ''));

        if ($text === '') {
            return 'text-gray-900';
        }

        if (! preg_match('/^[#a-zA-Z0-9\-\s]+$/', $text)) {
            return 'text-gray-900';
        }

        return $text;
    }

    /**
     * Get form submissions for this page
     */
    public function formSubmissions(): HasMany
    {
        return $this->hasMany(CmsFormSubmission::class);
    }

    /**
     * Get leads submitted through this page
     */
    public function leads(): HasMany
    {
        return $this->hasMany(LeadForm::class);
    }
}
