<?php

namespace App\Services\Analysis\Analyser;

use App\Models\File;
use App\Services\Analysis\Analyser\Analysers\AnalyserContract;
use App\Services\Analysis\Analyser\Analysers\DummyAnalyser;
use App\Services\Analysis\Analyser\Analysers\PointAnalyser;
use App\Services\Analysis\Parser\Parser;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AnalysisFactory implements AnalysisFactoryContract
{

    private array $analysers = [];

    public function analyse(File $file): Analysis
    {
        return $this->runAnalysis(Parser::parse($file));
    }

    public function runAnalysis(Analysis $analysis): Analysis
    {
        $analyser = $this->getChain();
        foreach($this->pointsFor($analysis) as $point) {
            $analyser->preparePoint($point);
        }
        return $analyser->analyse($analysis);
    }

    private function getChain(?\Closure $filter = null): AnalyserContract
    {
        $analysers = collect($this->analysers)
            ->map(fn(string $class) => app($class))
            ->filter($filter)
            ->values();

        if ($analysers->count() === 0) {
            return new DummyAnalyser();
        }

        for ($i = 0; $i < ($analysers->count() - 1); $i++) {
            $analysers[$i]->setNext($analysers[$i + 1]);
        }

        return $analysers->first();
    }

    public function registerAnalyser(string $class): AnalysisFactoryContract
    {
        $this->analysers[] = $class;
        return $this;
    }

    private function pointsFor(Analysis $analysis)
    {
        foreach($analysis->getPoints() as $point) {
            yield $point;
        }
    }
}
