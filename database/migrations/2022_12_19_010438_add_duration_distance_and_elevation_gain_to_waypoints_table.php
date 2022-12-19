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
        Schema::table('waypoints', function (Blueprint $table) {
            $table->float('distance')->nullable();
            $table->float('duration')->nullable();
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
        Schema::table('waypoints', function (Blueprint $table) {
            $table->dropColumn('distance');
            $table->dropColumn('duration');
            $table->dropColumn('elevation_gain');
        });
    }
};
