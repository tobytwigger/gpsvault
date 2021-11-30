<?php

namespace App\Services\Archive;

use App\Services\Archive\Parser\Parsers\ActivityParser;
use App\Services\Archive\Parser\Parsers\ConnectionLogParser;
use App\Services\Archive\Parser\Parsers\SyncParser;
use App\Services\Archive\Parser\Parsers\UserParser;
use App\Services\Archive\Parser\ResourceParser;
use Illuminate\Support\ServiceProvider;

class ArchiveServiceProvider extends ServiceProvider
{

    public function register()
    {
        $this->app->singleton(ResourceParser::class);
        $this->app->bind(\App\Services\Archive\Contracts\ZipCreator::class, TempZipCreator::class);
    }

    public function boot()
    {
        ResourceParser::withParser(ActivityParser::class);
        ResourceParser::withParser(SyncParser::class);
        ResourceParser::withParser(ConnectionLogParser::class);
        ResourceParser::withParser(UserParser::class);
    }

}
