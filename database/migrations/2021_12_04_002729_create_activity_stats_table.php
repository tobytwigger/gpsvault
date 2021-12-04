<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_stats', function (Blueprint $table) {
            $table->id();
            $table->string('integration');
            $table->string('activity_id');
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
            $table->timestamps();

            $table->unique(['integration', 'activity_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_stats');
    }
}
