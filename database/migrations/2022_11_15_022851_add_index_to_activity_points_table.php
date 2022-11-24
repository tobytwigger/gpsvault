<?php

use Illuminate\Database\Migrations\Migration;

return new class() extends Migration {
    /**
     * Run the migrations.
     *
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
     */
    public function down()
    {
        DB::connection()->statement(
            'DROP INDEX activity_points_time_sorting_index'
        );
    }
};
