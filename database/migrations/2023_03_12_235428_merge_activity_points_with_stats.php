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
            $table->longText('time_data')->nullable();
            $table->longText('cadence_data')->nullable();
            $table->longText('temperature_data')->nullable();
            $table->longText('heart_rate_data')->nullable();
            $table->longText('speed_data')->nullable();
            $table->longText('grade_data')->nullable();
            $table->longText('battery_data')->nullable();
            $table->longText('calories_data')->nullable();
            $table->longText('cumulative_distance_data')->nullable();
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
            $table->dropColumn('time_data');
            $table->dropColumn('cadence_data');
            $table->dropColumn('temperature_data');
            $table->dropColumn('heart_rate_data');
            $table->dropColumn('speed_data');
            $table->dropColumn('grade_data');
            $table->dropColumn('battery_data');
            $table->dropColumn('calories_data');
            $table->dropColumn('cumulative_distance_data');
        });
    }
};
