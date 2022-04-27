<?php

namespace App\Services\Archive\Contracts;

use App\Models\File;
use App\Models\User;
use App\Services\Archive\ParseResults;
use Illuminate\Support\Facades\Auth;

abstract class ZipCreator
{
    protected ParseResults $results;

    private User $user;

    public function __construct(ParseResults $results, ?User $user = null)
    {
        $this->results = $results;
        if (($user === null || !$user->exists) && Auth::check() === false) {
            throw new \Exception('Could not find a user to export a zip for');
        }
        $this->user = $user ?? Auth::user();
    }

    abstract public function archive(): File;

    public function user(): User
    {
        return $this->user;
    }
}
