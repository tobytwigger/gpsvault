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
        DB::connection()->statement(
            'CREATE INDEX activity_points_time_sorting_index ON activity_points(time ASC NULLS LAST)'
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::connection()->statement(
            'DROP INDEX activity_points_time_sorting_index'
        );
    }
};
