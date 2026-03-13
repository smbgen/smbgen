<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extreme_generations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('prompt');
            $table->string('status')->default('queued'); // queued | generating | complete | failed
            $table->json('config')->nullable(); // db, auth, billing, oauth, multi_tenant flags
            $table->unsignedInteger('file_count')->default(0);
            $table->unsignedInteger('test_count')->default(0);
            $table->string('zip_path')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('extreme_generations');
    }
};
