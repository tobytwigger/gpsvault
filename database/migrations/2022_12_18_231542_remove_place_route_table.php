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
        Schema::dropIfExists('place_route');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('place_route', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('route_id');
            $table->unsignedBigInteger('place_id');
            $table->timestamps();

            $table->foreign('route_id')->references('id')->on('routes')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('place_id')->references('id')->on('places')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }
};
