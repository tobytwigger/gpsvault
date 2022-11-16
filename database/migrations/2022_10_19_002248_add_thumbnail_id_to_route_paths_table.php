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
        Schema::table('route_paths', function (Blueprint $table) {
            $table->unsignedBigInteger('thumbnail_id')->nullable();

            $table->foreign('thumbnail_id')->references('id')->on('files')->cascadeOnUpdate()->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('route_paths', function (Blueprint $table) {
            $table->dropColumn('thumbnail_id');
        });
    }
};
