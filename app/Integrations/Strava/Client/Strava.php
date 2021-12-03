<?php

namespace App\Integrations\Strava\Client;

use App\Integrations\Strava\Client\Authentication\StravaToken;
use App\Integrations\Strava\Client\Client\StravaClient;
use App\Integrations\Strava\Client\Log\ConnectionLog;
use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Uuid;

class Strava
{

    protected string $clientUuid;

    protected ClientFactory $clientFactory;

    private ?int $userId = null;

    public function __construct(ClientFactory $clientFactory, ?int $userId = null)
    {
        $this->clientFactory = $clientFactory;
        $this->userId = $userId;
        $this->setUserId($this->userId ?? Auth::id());
    }

    public function redirectUrl(
        string $redirectUrl,
        string $state
    ): string
    {
        $params = [
            'client_id' => config('strava.client_id'),
            'redirect_uri' => $redirectUrl,
            'response_type' => 'code',
            'approval_prompt' => 'auto',
            'scope' => 'activity:read,read,read_all,profile:read_all,activity:read_all,activity:write',
            'state  ' => $state
        ];

        return sprintf('https://www.strava.com/oauth/authorize?%s', http_build_query($params));
    }

    public function setUserId(?int $userId = null): Strava
    {
        $this->userId = $userId;
        return $this;
    }

    public function client(): StravaClient
    {
        if(!$this->userId) {
            throw new \Exception('No user ID has been given to the Strava client');
        }
        return $this->clientFactory->create($this->userId);
    }

}
