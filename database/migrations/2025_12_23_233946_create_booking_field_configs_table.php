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
        Schema::create('booking_field_configs', function (Blueprint $table) {
            $table->id();
            $table->json('custom_fields')->nullable(); // Array of custom field definitions
            $table->boolean('show_phone')->default(true);
            $table->boolean('require_phone')->default(false);
            $table->boolean('show_property_address')->default(true);
            $table->boolean('require_property_address')->default(false);
            $table->boolean('show_notes')->default(true);
            $table->boolean('require_notes')->default(false);
            $table->timestamps();
        });

        // Insert default configuration
        DB::table('booking_field_configs')->insert([
            'show_phone' => true,
            'require_phone' => false,
            'show_property_address' => true,
            'require_property_address' => false,
            'show_notes' => true,
            'require_notes' => false,
            'custom_fields' => json_encode([]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_field_configs');
    }
};
