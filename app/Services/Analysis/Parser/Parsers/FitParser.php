<?php

namespace App\Services\Analysis\Parser\Parsers;

use adriangibbons\phpFITFileAnalysis;
use App\Models\Activity;
use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use PhpUnitConversion\Unit\Length\KiloMeter;
use PhpUnitConversion\Unit\Length\Meter;
use PhpUnitConversion\Unit\Temperature\Celsius;
use PhpUnitConversion\Unit\Temperature\Fahrenheit;
use PhpUnitConversion\Unit\Velocity\KiloMeterPerHour;
use PhpUnitConversion\Unit\Velocity\MeterPerSecond;

class FitParser implements ParserContract
{

    public function read(Activity $activity): Analysis
    {
        $fit = new phpFITFileAnalysis(
            $activity->activityFile->getFileContents(),
            [
                'input_is_data' => true,
                'fix_data' => ['all'],
                'units' => 'metric'
            ]);
        $points = collect();

        $timestamps = collect([])
            ->merge($fit->data_mesgs['record']['timestamp'])
            ->merge(array_keys($fit->data_mesgs['record']['position_lat']))
            ->merge(array_keys($fit->data_mesgs['record']['position_long']))
            ->merge(array_keys($fit->data_mesgs['record']['altitude']))
            ->merge(array_keys($fit->data_mesgs['record']['distance']))
            ->merge(array_keys($fit->data_mesgs['record']['speed']))
            ->merge(array_keys($fit->data_mesgs['record']['grade']))
            ->merge(array_keys($fit->data_mesgs['record']['heart_rate']))
            ->merge(array_keys($fit->data_mesgs['record']['calories']))
            ->merge(array_keys($fit->data_mesgs['record']['cadence']))
            ->merge(array_keys($fit->data_mesgs['record']['battery_soc']))
            ->merge(array_keys($fit->data_mesgs['record']['temperature']))
            ->unique();

        $record = $fit->data_mesgs['record'] ?? [];
        foreach ($timestamps as $timestamp) {
            $get = function ($key, array $units = []) use ($timestamp, $record) {
                if(array_key_exists($key, $record) && array_key_exists($timestamp, $record[$key])) {
                    $data = $record[$key][$timestamp];
                    unset($record[$key][$timestamp]);
                    if(count($units) === 2) {
                        return (new $units[0]($data))->to($units[1])->getValue();
                    }
                    return $data;
                }
                return null;
            };

            $points->push(
                (new Point())
                    ->setCadence($get('cadence'))
                    ->setTemperature($get('temperature', [Celsius::class, Fahrenheit::class]))
                    ->setElevation($get('altitude'))
                    ->setHeartRate($get('heart_rate'))
                    ->setLatitude($get('position_lat'))
                    ->setLongitude($get('position_long'))
                    ->setSpeed($get('speed', [KiloMeterPerHour::class, MeterPerSecond::class]))
                    ->setGrade($get('grade'))
                    ->setCalories($get('calories'))
                    ->setBattery($get('battery_soc'))
                    ->setTime(Carbon::createFromTimestamp($timestamp))
            );
        }

        $getSessionData = function($key, array $units = []) use ($fit) {
            $data = data_get($fit->data_mesgs['session'], $key, null);
            if($data !== null && count($units) === 2) {
                return (new $units[0]($data))->to($units[1])->getValue();
            }
            return $data;
        };
        $analysis = new Analysis();
        $analysis->setPoints($points->all());
        $analysis->setMovingTime($getSessionData('total_timer_time'));
        $analysis->setDuration($getSessionData('total_elapsed_time'));
        $analysis->setAverageSpeed($getSessionData('avg_speed', [KiloMeterPerHour::class, MeterPerSecond::class]));
        $analysis->setMaxSpeed($getSessionData('max_speed', [KiloMeterPerHour::class, MeterPerSecond::class]));
        $analysis->setDistance($getSessionData('total_distance', [KiloMeter::class, Meter::class]));
        $analysis->setAverageHeartrate($getSessionData('avg_heart_rate'));
        $analysis->setAverageCadence($getSessionData('avg_cadence'));
        $analysis->setCumulativeElevationGain($getSessionData('total_ascent'));
        $analysis->setCumulativeElevationLoss($getSessionData('total_descent'));
        $analysis->setCalories($getSessionData('total_calories'));
        $analysis->setMaxHeartrate($getSessionData('max_heart_rate'));
        return $analysis;
    }
}
