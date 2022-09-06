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
            $table->addColumn('pointz', 'location', ['geomtype' => 'GEOGRAPHY', 'srid' => '4326']);
            $table->unsignedBigInteger('route_path_id');
            $table->timestamps();
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
