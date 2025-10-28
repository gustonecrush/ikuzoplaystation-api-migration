<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reservation_saving_times', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_reservation');
            $table->date('date_saving');
            $table->string('start_time_saving');
            $table->string('end_time_saving');
            $table->string('is_active');
            $table->timestamps();

            $table->foreign('id_reservation')->references('id')->on('reservations')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('reservation_saving_times');
    }
};
