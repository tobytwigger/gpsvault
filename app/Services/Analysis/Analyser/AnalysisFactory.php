<?php

namespace App\Services\Analysis\Analyser;

use App\Models\Activity;
use App\Services\Analysis\Analyser\Analysers\AnalyserContract;
use App\Services\Analysis\Analyser\Analysers\DummyAnalyser;
use App\Services\Analysis\Analyser\Analysers\PointAnalyser;
use App\Services\Analysis\Parser\Parser;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class AnalysisFactory implements AnalysisFactoryContract
{

    private array $analysers = [];

    public function analyse(Activity $activity): Analysis
    {
        \Log::info('Analysing at ' . Carbon::now());
        return $this->runAnalysis(Parser::parse($activity));
    }

    public function runAnalysis(Analysis $analysis): Analysis
    {
        \Log::info('Running analysis at ' . Carbon::now());
        $pointAnalyser = $this->getChain(fn(AnalyserContract $analyser) => $analyser instanceof PointAnalyser);
        foreach($this->pointsFor($analysis) as $point) {
            $pointAnalyser->processPoint($point);
        }
        $analysis = $pointAnalyser->analyse($analysis);
        \Log::info('Finished point analysis at ' . Carbon::now());
        $analysis = $this->getChain(fn(AnalyserContract $analyser) => !($analyser instanceof PointAnalyser))->analyse($analysis);
        \Log::info('Finished full analysis at ' . Carbon::now());
        return $analysis;
    }

    private function getChain(\Closure $filter): AnalyserContract
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
