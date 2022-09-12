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
        Schema::create('route_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order');
            $table->unsignedBigInteger('place_id')->nullable();
            $table->point('location')->nullable();
            $table->unsignedBigInteger('route_path_id');
            $table->timestamps();

            $table->foreign('route_path_id')->references('id')->on('route_paths')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('place_id')->references('id')->on('places')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down()
    {
        Schema::dropIfExists('route_points');
    }
};
