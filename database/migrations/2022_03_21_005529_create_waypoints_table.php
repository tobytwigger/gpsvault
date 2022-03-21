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
        Schema::create('waypoints', function (Blueprint $table) {
            $table->id();
//            'latitude' => $this->getLatitude(),
//            'longitude' => $this->getLongitude(),
//            'elevation' => $this->getElevation(),
//            'time' => $this->getTime()?->unix(),
//            'cadence' => $this->getCadence(),
//            'temperature' => $this->getTemperature(),
//            'heart_rate' => $this->getHeartRate(),
//            'speed' => $this->getSpeed(),
//            'grade' => $this->getGrade(),
//            'battery' => $this->getBattery(),
//            'calories' => $this->getCalories(),
//            'cumulative_distance' => $this->getCumulativeDistance()
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
        Schema::dropIfExists('waypoints');
    }
};
