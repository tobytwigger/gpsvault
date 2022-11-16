<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;
use App\Services\Analysis\Parser\Point;

abstract class AnalyserContract
{
    private ?AnalyserContract $nextAnalyser = null;

    public function setNext(AnalyserContract $nextAnalyser)
    {
        $this->nextAnalyser = $nextAnalyser;
    }

    public function analyse(Analysis $analysis): Analysis
    {
        if ($this->canRun($analysis)) {
            $analysis = $this->run($analysis);
        }
        if ($this->nextAnalyser !== null) {
            return $this->nextAnalyser->analyse($analysis);
        }

        return $analysis;
    }

    public function preparePoint(Point $point): Point
    {
        if ($this instanceof PointAnalyser) {
            $point = $this->processPoint($point);
        }
        if($this->nextAnalyser !== null) {
            $point = $this->nextAnalyser?->preparePoint($point);
        }
        return $point;
    }

    abstract protected function run(Analysis $analysis): Analysis;

    public function canRun(Analysis $analysis): bool
    {
        return true;
    }
}
