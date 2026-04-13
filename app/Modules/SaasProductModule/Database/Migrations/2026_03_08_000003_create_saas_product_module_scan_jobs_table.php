<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('saasproductmodule_scan_jobs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('saasproductmodule_profiles')->cascadeOnDelete();
            $table->foreignId('data_broker_id')->constrained('saasproductmodule_data_brokers');
            $table->string('status')->default('pending');
            $table->json('result')->nullable();
            $table->unsignedInteger('listings_found')->default(0);
            $table->timestamp('scanned_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saasproductmodule_scan_jobs');
    }
};
