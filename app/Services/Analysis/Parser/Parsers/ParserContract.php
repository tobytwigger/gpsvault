<?php

namespace App\Services\Analysis\Parser\Parsers;

use App\Models\Activity;
use App\Services\Analysis\Analyser\Analysis;
use Illuminate\Support\Collection;

interface ParserContract
{

    public function read(Activity $activity): Analysis;

}
