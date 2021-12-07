<?php

namespace App\Services\Analysis\Analyser;

use App\Models\Activity;
use App\Services\Analysis\Analyser\Analysers\AnalyserContract;
use App\Services\Analysis\Parser\Parser;
use Illuminate\Support\Collection;

class AnalysisFactory implements AnalysisFactoryContract
{

    private array $analysers = [];

    public function analyse(Activity $activity): Analysis
    {
        return $this->runAnalysis(Parser::parse($activity));
    }

    public function runAnalysis(Analysis $analysis): Analysis
    {
        return $this->getChain()->analyse($analysis);
    }

    private function getChain(): AnalyserContract
    {
        if (count($this->analysers) === 0) {
            throw new \Exception('No analysers registered');
        }
        $analysers = array_map(fn(string $class) => app($class), $this->analysers);
        for ($i = 0; $i < (count($analysers) - 1); $i++) {
            $analysers[$i]->setNext($analysers[$i + 1]);
        }

        return $analysers[0];
    }

    public function registerAnalyser(string $class): AnalysisFactoryContract
    {
        $this->analysers[] = $class;
        return $this;
    }
}
