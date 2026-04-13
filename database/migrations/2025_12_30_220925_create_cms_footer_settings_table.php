<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cms_footer_settings', function (Blueprint $table) {
            $table->id();
            $table->text('footer_html')->nullable();
            $table->boolean('use_default')->default(true);
            $table->timestamps();
        });

        // Create default footer settings with blog footer HTML
        $defaultFooterHtml = $this->getDefaultFooterHtml();

        DB::table('cms_footer_settings')->insert([
            'footer_html' => $defaultFooterHtml,
            'use_default' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_footer_settings');
    }

    /**
     * Get the default blog footer HTML template
     */
    private function getDefaultFooterHtml(): string
    {
        return <<<'HTML'
<footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- About -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    {{ $companyName }}
                </h3>
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    @if(config('business.tagline'))
                        {{ config('business.tagline') }}
                    @else
                        Stay updated with our latest posts and insights.
                    @endif
                </p>
                @if($companyWebsite && $companyHost)
                    <a href="{{ $companyWebsite }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline mt-3 inline-block" rel="noopener noreferrer">
                        {{ $companyHost }}
                    </a>
                @endif
            </div>
            
            <!-- Quick Links -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Links</h3>
                <ul class="space-y-2">
                    <li>
                        <a href="/" class="text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                            Home
                        </a>
                    </li>
                    <li>
                        <a href="/contact" class="text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                            Contact
                        </a>
                    </li>
                    @if(config('business.features.booking'))
                        <li>
                            <a href="/book" class="text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                                Book Appointment
                            </a>
                        </li>
                    @endif
                    @if(config('business.features.blog'))
                        <li>
                            <a href="/blog" class="text-sm text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                                Blog
                            </a>
                        </li>
                    @endif
                </ul>
            </div>
            
            <!-- Social/Contact -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Connect</h3>
                <div class="flex space-x-4">
                    @if(config('business.social.twitter'))
                        <a href="{{ config('business.social.twitter') }}" target="_blank" class="text-gray-600 dark:text-gray-400 hover:text-blue-500">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                    @endif
                    @if(config('business.social.facebook'))
                        <a href="{{ config('business.social.facebook') }}" target="_blank" class="text-gray-600 dark:text-gray-400 hover:text-blue-600">
                            <i class="fab fa-facebook text-xl"></i>
                        </a>
                    @endif
                    @if(config('business.social.linkedin'))
                        <a href="{{ config('business.social.linkedin') }}" target="_blank" class="text-gray-600 dark:text-gray-400 hover:text-blue-700">
                            <i class="fab fa-linkedin text-xl"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="border-t border-gray-200 dark:border-gray-700 mt-8 pt-8 text-center">
            <p class="text-sm text-gray-600 dark:text-gray-400">
                &copy; {{ date('Y') }} {{ $companyName }}. All rights reserved.
            </p>
        </div>
    </div>
</footer>
HTML;
    }
};
