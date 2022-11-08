<?php

namespace App\Services\Analysis\Analyser;

use App\Models\File;
use App\Services\Analysis\Analyser\Analysers\AnalyserContract;
use App\Services\Analysis\Analyser\Analysers\DummyAnalyser;
use App\Services\Analysis\Parser\Parser;
use Closure;

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
        $newPoints = [];
        foreach ($this->pointsFor($analysis) as $point) {
            $newPoints[] = $analyser->preparePoint($point);
        }
        $analysis->setPoints($newPoints);

        return $analyser->analyse($analysis);
    }

    private function getChain(?Closure $filter = null): AnalyserContract
    {
        $analysers = collect($this->analysers)
            ->map(fn (string $class) => app($class))
            ->filter($filter)
            ->values();

        if ($analysers->count() === 0) {
            return new DummyAnalyser();
        }

        $count = $analysers->count();
        for ($i = 0; $i < $count - 1; $i++) {
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
        foreach ($analysis->getPoints() as $point) {
            yield $point;
        }
    }
}
