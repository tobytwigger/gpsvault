<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
     */
    public function up()
    {
        Schema::dropIfExists('activity_points');
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down()
    {
        Schema::create('activity_points', function (Blueprint $table) {
            $table->id();
            $table->point('points')->nullable();
            $table->float('elevation')->nullable();
            $table->dateTime('time')->nullable();
            $table->float('cadence')->nullable();
            $table->float('temperature')->nullable();
            $table->float('heart_rate')->nullable();
            $table->float('speed')->nullable();
            $table->float('grade')->nullable();
            $table->float('battery')->nullable();
            $table->float('calories')->nullable();
            $table->float('cumulative_distance')->nullable();
            $table->unsignedBigInteger('order')->nullable();
            $table->unsignedBigInteger('stats_id');
            $table->timestamps();

            $table->foreign('stats_id')->references('id')->on('stats')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }
};
