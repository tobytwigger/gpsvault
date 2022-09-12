<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     */
    public function up()
    {
        Schema::create('stages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('stage_number');
            $table->string('name')->nullable();
            $table->text('description')->nullable();
            $table->date('date')->nullable();
            $table->boolean('is_rest_day')->default(false);
            $table->unsignedBigInteger('tour_id');
            $table->unsignedBigInteger('route_id')->nullable();
            $table->unsignedBigInteger('activity_id')->nullable();
            $table->timestamps();

            $table->foreign('tour_id')->references('id')->on('tours')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('route_id')->references('id')->on('routes')->cascadeOnUpdate()->nullOnDelete();
            $table->foreign('activity_id')->references('id')->on('activities')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down()
    {
        Schema::dropIfExists('stages');
    }
}
