<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('client_files', function (Blueprint $table) {
            $table->string('mime_type')->nullable()->after('path');
            $table->unsignedBigInteger('file_size')->nullable()->after('mime_type'); // in bytes
            $table->string('file_extension', 10)->nullable()->after('file_size');
            $table->boolean('is_public')->default(false)->after('file_extension');
            $table->text('description')->nullable()->after('is_public');

            // Add index for faster queries
            $table->index('is_public');
            $table->index('file_extension');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_files', function (Blueprint $table) {
            $table->dropIndex(['is_public']);
            $table->dropIndex(['file_extension']);
            $table->dropColumn(['mime_type', 'file_size', 'file_extension', 'is_public', 'description']);
        });
    }
};
