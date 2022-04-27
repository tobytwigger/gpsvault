<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     */
    public function up()
    {
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('path', 1000);
            $table->string('filename', 1000);
            $table->string('extension');
            $table->string('type');
            $table->bigInteger('size');
            $table->string('title')->nullable();
            $table->text('caption')->nullable();
            $table->string('mimetype');
            $table->string('disk');
            $table->string('hash', 32);
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     */
    public function down()
    {
        Schema::dropIfExists('files');
    }
}
