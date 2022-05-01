<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class EnablePostgis extends Migration
{
    /**
     * Run the migrations.
     *
     */
    public function up()
    {
        Schema::enablePostgisIfNotExists();
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down()
    {
        Schema::disablePostgisIfExists();
    }
}
