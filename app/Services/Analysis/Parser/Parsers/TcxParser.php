<?php

namespace App\Services\Analysis\Parser\Parsers;

use App\Models\File;
use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Waddle\Parsers\TCXParser as BaseParser;

class TcxParser implements ParserContract
{
    public function read(File $file): Analysis
    {
        $parser = new BaseParser();
        $file = $parser->parse($file->fullPath());
        $points = collect();

        $averages = [
            'avg_heart_rate' => [],
            'max_heart_rate' => null,
            'avg_cadence' => []
        ];
        foreach ($file->getLaps() as $lap) {
            foreach ($lap->getTrackPoints() as $trackPoint) {
                $points->push(
                    (new Point())
                        ->setCadence($trackPoint->getCadence())
                        ->setElevation($trackPoint->getAltitude())
                        ->setHeartRate($trackPoint->getHeartRate())
                        ->setLatitude($trackPoint->getPosition('lat'))
                        ->setLongitude($trackPoint->getPosition('lon'))
                        ->setSpeed($trackPoint->getSpeed())
                        ->setCalories($trackPoint->getCalories())
                        ->setTime(Carbon::createFromTimestamp($trackPoint->getTime('U')))
                );
                $averages['avg_heart_rate'][] = $lap->getAvgHeartRate();
                if ($averages['max_heart_rate'] === null || $lap->getMaxHeartRate() > $averages['max_heart_rate']) {
                    $averages['max_heart_rate'] = $lap->getMaxHeartRate();
                }
                $averages['avg_cadence'][] = $lap->getCadence();
            }
        }

        $analysis = new Analysis();
        $analysis->setPoints($points->all());
        $analysis->setAverageHeartrate(count($averages['avg_heart_rate']) > 0 ? (float) collect($averages['avg_heart_rate'])->average() : null);
        $analysis->setAverageCadence(count($averages['avg_cadence']) > 0 ? (float) collect($averages['avg_cadence'])->average() : null);
        $analysis->setMaxHeartrate($averages['max_heart_rate']);
        $analysis->setAverageSpeed($file->getAverageSpeedInKPH() * 3.6);
        $analysis->setMaxSpeed($file->getMaxSpeedInKPH() * 3.6);
        $analysis->setCalories($file->getTotalCalories());
        $analysis->setDistance($file->getTotalDistance());
        $analysis->setDuration($file->getTotalDuration());
        $analysis->setCumulativeElevationGain($file->getTotalAscentDescent()['ascent']);
        $analysis->setCumulativeElevationLoss($file->getTotalAscentDescent()['descent']);
        $analysis->setAveragePace(CarbonInterval::createFromFormat('H:i:s', $file->getAveragePacePerKilometre())->totalMinutes * 0.06);

        return $analysis;
    }
}
