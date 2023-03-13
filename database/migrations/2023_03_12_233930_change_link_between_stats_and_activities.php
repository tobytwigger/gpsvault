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
            $table->dropUnique(['integration', 'stats_id', 'stats_type']);
            $table->dropColumn('stats_type');
            $table->renameColumn('stats_id', 'activity_id');
        });

        Schema::table('stats', function (Blueprint $table) {
            $table->unique(['integration', 'activity_id']);
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
            $table->dropUnique(['integration', 'activity_id']);
            $table->string('stats_type')->default('App\Models\Activity');
            $table->renameColumn('activity_id', 'stats_id');
        });

        Schema::table('stats', function (Blueprint $table) {
            $table->unique(['integration', 'stats_id', 'stats_type']);
        });
    }
};
