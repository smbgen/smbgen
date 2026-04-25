<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsFooterSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'footer_html',
        'use_default',
    ];

    protected function casts(): array
    {
        return [
            'use_default' => 'boolean',
        ];
    }

    /**
     * Get the singleton footer settings
     */
    public static function getSettings()
    {
        return static::firstOrCreate([], [
            'footer_html' => self::getDefaultFooterHtml(),
            'use_default' => true,
        ]);
    }

    /**
     * Get the default blog footer HTML template
     */
    public static function getDefaultFooterHtml(): string
    {
        return <<<'HTML'
<footer class="bg-white border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- About -->
            <div>
                <h3 class="text-lg font-semibold mb-4" style="color: #111827 !important;">
                    {{ $companyName }}
                </h3>
                <p class="text-sm" style="color: #4b5563 !important;">
                    @if(config('business.tagline'))
                        {{ config('business.tagline') }}
                    @else
                        Stay updated with our latest posts and insights.
                    @endif
                </p>
                @if($companyWebsite && $companyHost)
                    <a href="{{ $companyWebsite }}" class="text-sm hover:opacity-80 transition-opacity mt-3 inline-block" style="color: #2563eb !important;" rel="noopener noreferrer">
                        {{ $companyHost }}
                    </a>
                @endif
            </div>
            
            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-semibold mb-4" style="color: #111827 !important;">Quick Links</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="/" class="text-sm hover:opacity-80 transition-opacity" style="color: #4b5563 !important;">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="/contact" class="text-sm hover:opacity-80 transition-opacity" style="color: #4b5563 !important;">
                            Contact
                        </a>
                    </li>
                    @if(config('business.features.booking'))
                        <li>
                            <a href="/book" class="text-sm hover:opacity-80 transition-opacity" style="color: #4b5563 !important;">
                                Book Appointment
                            </a>
                        </li>
                    @endif
                    @if(config('business.features.blog'))
                        <li>
                            <a href="/blog" class="text-sm hover:opacity-80 transition-opacity" style="color: #4b5563 !important;">
                                Blog
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            
            <!-- Social/Contact -->
            <div>
                <h3 class="text-lg font-semibold mb-4" style="color: #111827 !important;">Connect</h3>
                <div class="flex space-x-4">
                    @if(config('business.social.twitter'))
                        <a href="{{ config('business.social.twitter') }}" target="_blank" class="hover:opacity-70 transition-opacity" style="color: #4b5563 !important;">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                    @endif
                    @if(config('business.social.facebook'))
                        <a href="{{ config('business.social.facebook') }}" target="_blank" class="hover:opacity-70 transition-opacity" style="color: #4b5563 !important;">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                    @endif
                    @if(config('business.social.linkedin'))
                        <a href="{{ config('business.social.linkedin') }}" target="_blank" class="hover:opacity-70 transition-opacity" style="color: #4b5563 !important;">
                            <i class="fab fa-linkedin text-xl"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-200 mt-8 pt-8 text-center">
            <p class="text-sm" style="color: #6b7280 !important;">
                &copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.
            </p>
        </div>
    </div>
</footer>
HTML;
    }
}
