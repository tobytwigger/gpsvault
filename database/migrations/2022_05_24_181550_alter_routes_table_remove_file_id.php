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
        Schema::table('routes', function (Blueprint $table) {
            $table->dropColumn('file_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down()
    {
        Schema::table('routes', function (Blueprint $table) {
            $table->unsignedBigInteger('file_id')->nullable();
        });
    }
};
