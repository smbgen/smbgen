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
        if (Schema::hasTable('email_sequence_steps')) {
            return;
        }

        Schema::create('email_sequence_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('email_sequence_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('position');
            $table->string('subject');
            $table->longText('body');
            $table->unsignedSmallInteger('delay_days')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('email_sequence_steps');
    }
};
