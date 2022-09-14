<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('route_path_waypoints', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('route_path_id');
            $table->unsignedBigInteger('waypoint_id');
            $table->unsignedInteger('order');
            $table->timestamps();

            $table->foreign('route_path_id')->references('id')->on('route_paths')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('waypoint_id')->references('id')->on('waypoints')->cascadeOnUpdate()->cascadeOnDelete();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('route_path_waypoints');
    }
};
