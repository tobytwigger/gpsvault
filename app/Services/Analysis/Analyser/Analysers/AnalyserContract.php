<?php

namespace App\Services\Analysis\Analyser\Analysers;

use App\Services\Analysis\Analyser\Analysis;

abstract class AnalyserContract
{

    private ?AnalyserContract $nextAnalyser = null;

    public function setNext(AnalyserContract $nextAnalyser)
    {
        $this->nextAnalyser = $nextAnalyser;
    }

    public function analyse(Analysis $analysis): Analysis
    {
        if($this->canRun($analysis)) {
            $analysis = $this->run($analysis);
        }
        if($this->nextAnalyser !== null) {
            return $this->nextAnalyser->analyse($analysis);
        }
        return $analysis;
    }

    abstract protected function run(Analysis $analysis): Analysis;

    abstract public function canRun(Analysis $analysis): bool;

}
