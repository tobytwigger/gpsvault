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
            $table->addColumn('linestringz', 'linestring', ['geomtype' => 'GEOGRAPHY', 'srid' => '4326']);
            $table->float('distance');
            $table->float('elevation_gain');
            $table->float('complete_in_seconds');
            $table->unsignedBigInteger('route_id');
            $table->timestamps();
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
