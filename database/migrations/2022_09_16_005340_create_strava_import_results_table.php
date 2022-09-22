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
        Schema::create('strava_import_results', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->text('message');
            $table->boolean('success');
            $table->text('data')->nullable();
            $table->unsignedBigInteger('strava_import_id');
            $table->timestamps();

            $table->foreign('strava_import_id')->references('id')->on('strava_imports')->cascadeOnUpdate()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down()
    {
        Schema::dropIfExists('strava_import_results');
    }
};
