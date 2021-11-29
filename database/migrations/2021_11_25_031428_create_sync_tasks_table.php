<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSyncTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_tasks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sync_id');
            $table->text('task_id');
            $table->text('config');
            $table->enum('status', ['queued', 'processing', 'succeeded', 'failed', 'cancelled'])->default('queued');
            $table->text('messages');
            $table->integer('percentage')->nullable();
            $table->dateTime('started_at')->nullable();
            $table->dateTime('finished_at')->nullable();
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
        Schema::dropIfExists('sync_tasks');
    }
}
