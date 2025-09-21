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
        Schema::create('benefit_membership_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_membership_tier')->constrained('membership_tiers')->onDelete('cascade');
            $table->string('name_benefit');
            $table->string('duration_benefit');
            $table->string('kuota_benefit');
            $table->string('syarat_benefit');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('benefit_membership_tiers');
    }
};
