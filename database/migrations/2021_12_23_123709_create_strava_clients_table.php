<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStravaClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     */
    public function up()
    {
        Schema::create('strava_clients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->text('client_id');
            $table->text('client_secret');
            $table->boolean('enabled')->default(true);
            $table->boolean('public')->default(false);
            $table->text('webhook_verify_token');
            $table->uuid('invitation_link_uuid')->nullable();
            $table->unsignedBigInteger('used_15_min_calls')->default(0);
            $table->unsignedBigInteger('used_daily_calls')->default(0);
            $table->unsignedBigInteger('limit_15_min')->default(100);
            $table->unsignedBigInteger('limit_daily')->default(1000);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down()
    {
        Schema::dropIfExists('strava_clients');
    }
}
