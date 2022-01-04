<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddClientIdToStravaTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \App\Integrations\Strava\StravaToken::getQuery()->delete();
        Schema::table('strava_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('strava_client_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('strava_tokens', function (Blueprint $table) {
            $table->dropColumn('strava_client_id');
        });
    }
}
