<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cleanslate_removal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained('cleanslate_profiles')->cascadeOnDelete();
            $table->foreignId('data_broker_id')->constrained('cleanslate_data_brokers');
            $table->string('status')->default('pending');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cleanslate_removal_requests');
    }
};
