<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // The post content
            $table->text('caption');

            // Post lifecycle status
            $table->string('status')->default('draft'); // draft | scheduled | publishing | published | failed | cancelled

            // When to publish (null = immediate/manual)
            $table->timestamp('scheduled_at')->nullable();

            // When all platform targets were published
            $table->timestamp('published_at')->nullable();

            // Optional attribution to a source record (job photo context)
            $table->string('source_type')->nullable(); // App\Models\CmsImage | App\Models\ClientFile | App\Models\InspectionReport
            $table->unsignedBigInteger('source_id')->nullable();

            // Requires approval before going live
            $table->boolean('requires_approval')->default(false);
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('scheduled_at');
            $table->index(['source_type', 'source_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_posts');
    }
};
