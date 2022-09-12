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
        Schema::table('stats', function (Blueprint $table) {
            $table->point('start_point')->nullable();
            $table->point('end_point')->nullable();
            $table->dropColumn('start_latitude');
            $table->dropColumn('start_longitude');
            $table->dropColumn('end_latitude');
            $table->dropColumn('end_longitude');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stats', function (Blueprint $table) {
            $table->dropColumn('start_point');
            $table->dropColumn('end_point');
            $table->float('start_latitude')->nullable();
            $table->float('start_longitude')->nullable();
            $table->float('end_latitude')->nullable();
            $table->float('end_longitude')->nullable();
        });
    }
};
