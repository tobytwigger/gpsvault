<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStravaClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('strava_clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->text('client_id');
            $table->text('client_secret');
            $table->boolean('enabled')->default(true);
            $table->boolean('public')->default(false);
            $table->text('webhook_verify_token');
            $table->uuid('invitation_link_uuid')->nullable();
            $table->unsignedBigInteger('used_15_min_calls')->default(0);
            $table->unsignedBigInteger('used_daily_calls')->default(0);
            $table->unsignedBigInteger('pending_calls')->default(0);
            $table->dateTime('15_mins_resets_at')->nullable();
            $table->dateTime('daily_resets_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('strava_clients');
    }
}
