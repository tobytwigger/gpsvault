<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     */
    public function up()
    {
        Schema::create('stats', function (Blueprint $table) {
            $table->id();
            $table->string('integration');
            $table->float('distance')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
            $table->float('duration')->nullable();
            $table->float('average_speed')->nullable();
            $table->float('average_pace')->nullable();
            $table->float('min_altitude')->nullable();
            $table->float('max_altitude')->nullable();
            $table->float('elevation_gain')->nullable();
            $table->float('elevation_loss')->nullable();
            $table->float('moving_time')->nullable();
            $table->float('max_speed')->nullable();
            $table->float('average_cadence')->nullable();
            $table->float('average_temp')->nullable();
            $table->float('average_watts')->nullable();
            $table->float('kilojoules')->nullable();
            $table->float('start_latitude')->nullable();
            $table->float('start_longitude')->nullable();
            $table->float('end_latitude')->nullable();
            $table->float('end_longitude')->nullable();
            $table->float('max_heartrate')->nullable();
            $table->float('average_heartrate')->nullable();
            $table->float('calories')->nullable();
            $table->unsignedBigInteger('stats_id');
            $table->string('stats_type');
            $table->timestamps();

            $table->unique(['integration', 'stats_id', 'stats_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down()
    {
        Schema::dropIfExists('stats');
    }
}
