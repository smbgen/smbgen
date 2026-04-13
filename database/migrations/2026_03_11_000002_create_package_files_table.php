<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('package_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained()->cascadeOnDelete();
            $table->string('original_name');
            $table->string('display_name');
            $table->enum('type', [
                'HTML_PRESENTATION',
                'HTML_EMAIL',
                'PDF_DOCUMENT',
                'MARKDOWN_RESEARCH',
                'JSON_DATA',
                'POWERPOINT',
                'WORD_DOCUMENT',
                'OTHER',
            ]);
            $table->enum('role', ['deliverable', 'research', 'data', 'email_template']);
            $table->string('group_label')->nullable();
            $table->string('storage_path');
            $table->string('storage_disk')->default('private');
            $table->unsignedBigInteger('size_bytes')->default(0);
            $table->boolean('portal_promoted')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('package_files');
    }
};
