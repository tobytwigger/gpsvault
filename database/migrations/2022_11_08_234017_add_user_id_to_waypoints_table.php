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
        Schema::table('waypoints', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable();
        });

        $existingWaypoints = \App\Models\Waypoint::query()
            ->whereNull('user_id')
            ->whereHas('routePathWaypoints.routePath.route')
            ->with(['routePathWaypoints.routePath.route'])
            ->get();

        foreach ($existingWaypoints as $waypoint) {
            \Illuminate\Support\Facades\DB::table('waypoints')
                ->where('id', $waypoint->id)
                ->update(['user_id' => $waypoint->routePathWaypoints->first()?->routePath->route->user_id]);
        }

        \Illuminate\Support\Facades\DB::table('waypoints')
            ->whereNull('user_id')
            ->delete();
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down()
    {
        Schema::table('waypoints', function (Blueprint $table) {
            $table->dropColumn('user_id');
        });
    }
};
