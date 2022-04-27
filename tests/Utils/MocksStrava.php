<?php

namespace Tests\Utils;

use App\Integrations\Strava\Client\Client\Resource;
use App\Integrations\Strava\Client\Client\StravaClient;
use App\Integrations\Strava\Client\StravaClientFactory;
use App\Models\User;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

trait MocksStrava
{
    use ProphecyTrait;

    private $stravaProphecy;

    public function stravaClientProphecy()
    {
        if (!isset($this->stravaProphecy)) {
            $this->stravaProphecy = $this->prophesize(StravaClient::class);
        }

        return $this->stravaProphecy;
    }

    public function stravaFactory(User $user): StravaClientFactory
    {
        $factory = $this->prophesize(StravaClientFactory::class);
        $factory->client(Argument::that(fn ($arg) => $arg instanceof User && $arg->is($user)))
            ->willReturn($this->stravaClientProphecy()->reveal());

        return $factory->reveal();
    }

    public function mockResource(string $resourceName, Resource $mock)
    {
        $this->stravaClientProphecy()->{$resourceName}()->willReturn($mock);
    }
}
