<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStravaCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     */
    public function up()
    {
        Schema::create('strava_comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('strava_id');
            $table->string('first_name');
            $table->string('last_name');
            $table->text('text');
            $table->dateTime('posted_at');
            $table->unsignedBigInteger('activity_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down()
    {
        Schema::dropIfExists('strava_comments');
    }
}
