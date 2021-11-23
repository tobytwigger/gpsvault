<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConnectionLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('connection_logs', function (Blueprint $table) {
            $table->id();
            $table->enum('type', [
                \App\Models\ConnectionLog::SUCCESS,
                \App\Models\ConnectionLog::DEBUG,
                \App\Models\ConnectionLog::ERROR,
                \App\Models\ConnectionLog::INFO,
                \App\Models\ConnectionLog::WARNING
            ]);
            $table->text('log')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('integration');
            $table->uuid('client_uuid')->nullable();
            $table->uuid('request_uuid')->nullable();
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
        Schema::dropIfExists('connection_logs');
    }
}
