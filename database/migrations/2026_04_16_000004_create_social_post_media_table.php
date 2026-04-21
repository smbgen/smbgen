<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Media files attached to a social post (no file duplication – references existing records)
        Schema::create('social_post_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('social_post_id')->constrained()->onDelete('cascade');

            // Polymorphic source: CmsImage, ClientFile, InspectionReport, or uploaded directly
            $table->string('mediable_type')->nullable(); // null when uploaded directly
            $table->unsignedBigInteger('mediable_id')->nullable();

            // If uploaded directly, store path on disk
            $table->string('disk')->nullable();
            $table->string('path')->nullable();
            $table->string('mime_type')->nullable();
            $table->string('original_name')->nullable();

            // Caption / alt text for this particular media item
            $table->string('caption')->nullable();

            // Display order
            $table->unsignedTinyInteger('sort_order')->default(0);

            $table->timestamps();

            $table->index(['social_post_id', 'sort_order']);
            $table->index(['mediable_type', 'mediable_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_post_media');
    }
};
