<?php

namespace App\Services\Analysis\Parser\Parsers;

use adriangibbons\phpFITFileAnalysis;
use App\Models\Activity;
use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class FitParser implements ParserContract
{

    public function read(Activity $activity): Analysis
    {
        $fit = new phpFITFileAnalysis($activity->activityFile->getFileContents(), ['input_is_data' => true]);
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
        dd($fit->data_mesgs);
        $record = $fit->data_mesgs['record'] ?? [];
        foreach ($timestamps as $timestamp) {
            $get = function ($key) use ($timestamp, $record) {
                if(array_key_exists($key, $record) && array_key_exists($timestamp, $record[$key])) {
                    $data = $record[$key][$timestamp];
                    unset($record[$key][$timestamp]);
                    return $data;
                }
                return null;
            };

            $points->push(
                (new Point())
                    ->setCadence($get('cadence'))
                    ->setTemperature($get('temperature'))
                    ->setElevation($get('altitude'))
                    ->setHeartRate($get('heart_rate'))
                    ->setLatitude($get('position_lat'))
                    ->setLongitude($get('position_long'))
                    ->setSpeed($get('speed') / 3.6)
                    ->setGrade($get('grade'))
                    ->setCalories($get('calories'))
                    ->setBattery($get('battery_soc'))
                    ->setTime(Carbon::createFromTimestamp($timestamp))
            );
        }

        $getSessionData = fn($key) => data_get($fit->data_mesgs['session'], $key, null);
        $analysis = new Analysis();
        $analysis->setPoints($points->all());
        $analysis->setMovingTime($getSessionData('total_timer_time'));
        $analysis->setDuration($getSessionData('total_elapsed_time'));
        $analysis->setAverageSpeed($getSessionData('avg_speed'));
        $analysis->setMaxSpeed($getSessionData('max_speed'));
        $analysis->setDistance($getSessionData('total_distance'));
        $analysis->setAverageHeartrate($getSessionData('avg_heart_rate'));
        $analysis->setAverageCadence($getSessionData('avg_cadence'));
        $analysis->setCumulativeElevationGain($getSessionData('total_ascent'));
        $analysis->setCumulativeElevationLoss($getSessionData('total_descent'));
        $analysis->setCalories($getSessionData('total_calories'));
        $analysis->setMaxHeartrate($getSessionData('max_heart_rate'));
        return $analysis;
    }
}
