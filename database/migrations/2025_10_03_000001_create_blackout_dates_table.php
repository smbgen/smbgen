<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('blackout_dates', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('reason')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Which admin created it
            $table->timestamps();

            $table->unique('date'); // Can't have duplicate blackout dates
        });
    }

    public function down()
    {
        Schema::dropIfExists('blackout_dates');
    }
};
