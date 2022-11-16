<?php

namespace App\Integrations\Strava\Client\Client\Resources;

use App\Integrations\Strava\Client\Client\Resource;

class Authentication extends Resource
{
    public function currentUser()
    {
        $response = $this->request->request('GET', 'athlete');

        return $this->request->decodeResponse($response);
    }
}
