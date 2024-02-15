<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->string('reserve_id')->unique();
            $table->string('reserve_name');
            $table->string('reserve_contact');
            $table->string('location');
            $table->string('position');
            $table->date('reserve_date');
            $table->string('reserve_start_time');
            $table->string('reserve_end_time');
            $table->string('status_reserve')->nullable();
            $table->string('price'); // Assuming the price is in decimal format
            $table->string('status_payment')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
