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
        Schema::create('customer_memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_customer')->constrained('customers')->onDelete('cascade');
            $table->foreignId('id_membership')->constrained('membership_tiers')->onDelete('cascade');
            $table->date('start_periode');
            $table->date('end_periode');
            $table->string('status_tier');
            $table->string('status_benefit');
            $table->string('status_payment');
            $table->string('status_birthday_treat');
            $table->integer('kuota_weekly')->default(0);
            $table->integer('membership_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_memberships');
    }
};
