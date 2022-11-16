<?php

namespace App\Services\Dashboard\Widgets\Traits;

use Carbon\Carbon;

trait WidgetDateConstraints
{
    private array $dateRanges = [];

    public function addDateRange(string $key, \Closure $from, \Closure $to, string $text)
    {
        $this->dateRanges[$key] = [$from, $to, $text];
    }

    private function initDateRanges()
    {
        $this->addDateRange('current-year', fn () => Carbon::createFromDate(now()->year, 1, 1)->setTime(0, 0, 0), fn () => now(), 'this year');
        $this->addDateRange('current-month', fn () => Carbon::createFromDate(now()->year, now()->month, 1)->setTime(0, 0, 0), fn () => now(), 'this month');
        $this->addDateRange('all-time', fn () => Carbon::createFromDate(1, 1, 1)->setTime(0, 0, 0), fn () => now(), 'ever');
    }

    private function getDateRangeSettings(): array
    {
        $this->initDateRanges();
        $dateRangeKey = $this->getSetting('period');
        if (!array_key_exists($dateRangeKey, $this->dateRanges)) {
            throw new \Exception('Could not find date range ' . $dateRangeKey);
        }

        return $this->dateRanges[$dateRangeKey];
    }

    public function getDurationText()
    {
        return $this->getDateRangeSettings()[2];
    }

    public function getLowerBound()
    {
        return $this->getDateRangeSettings()[0]();
    }


    public function getUpperBound()
    {
        return $this->getDateRangeSettings()[1]();
    }

    abstract public function getSetting(string $key): mixed;
}
