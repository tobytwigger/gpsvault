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
        Schema::table('routes', function (Blueprint $table) {
            $table->lineString('linestring')->nullable();
            $table->float('distance')->nullable();
            $table->float('elevation_gain')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn('linestring');
            $table->dropColumn('distance');
            $table->dropColumn('elevation_gain');
        });
        Schema::dropIfExists('routes');
    }
};
