<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;

class CreateRouteStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_stats', function (Blueprint $table) {
            $table->id();
            $table->string('integration');
            $table->string('route_id');
            $table->float('distance')->nullable();
            $table->float('min_altitude')->nullable();
            $table->float('max_altitude')->nullable();
            $table->float('elevation_gain')->nullable();
            $table->float('elevation_loss')->nullable();
            $table->float('start_latitude')->nullable();
            $table->float('start_longitude')->nullable();
            $table->float('end_latitude')->nullable();
            $table->float('end_longitude')->nullable();
            $table->string('json_points_file_id')->nullable();
            $table->timestamps();

            $table->unique(['integration', 'route_id']);
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route_stats');
    }
}
