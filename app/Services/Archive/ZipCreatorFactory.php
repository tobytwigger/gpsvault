<?php

namespace App\Services\Archive;

use App\Models\File;
use App\Models\User;
use App\Services\Archive\Parser\ResourceParser;
use Illuminate\Support\Facades\Auth;

class ZipCreatorFactory
{
    private array $items = [];

    private ResourceParser $parser;

    private User $user;

    public function __construct(ResourceParser $parser)
    {
        $this->parser = $parser;
    }

    public function start(?User $user = null): ZipCreatorFactory
    {
        if ($user !== null) {
            $this->forUser($user);
        }

        return $this;
    }

    public function forUser(User $user)
    {
        $this->user = $user;
    }

    public function add($data): ZipCreatorFactory
    {
        $this->items[] = $data;

        return $this;
    }

    public function archive(): File
    {
        return $this->create()->archive();
    }

    public function create(): Contracts\ZipCreator
    {
        return app(\App\Services\Archive\Contracts\ZipCreator::class, ['results' => $this->parse(), 'user' => $this->user ?? Auth::user()]);
    }

    private function parse(): ParseResults
    {
        $results = new ParseResults();
        foreach ($this->items as $item) {
            $results->mergeResults($this->parser->parse($item));
        }

        return $results;
    }
}
