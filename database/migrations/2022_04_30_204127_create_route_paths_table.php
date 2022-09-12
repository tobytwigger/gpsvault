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
        Schema::create('route_paths', function (Blueprint $table) {
            $table->id();
            $table->linestringZ('linestring');
            $table->float('distance');
            $table->float('elevation_gain');
            $table->float('duration');
            $table->unsignedBigInteger('route_id');
            $table->timestamps();

            $table->foreign('route_id')->references('id')->on('routes')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down()
    {
        Schema::dropIfExists('route_paths');
    }
};
