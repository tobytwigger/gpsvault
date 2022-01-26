<?php

namespace App\Integrations\Strava\Client\Client\Resources;

use App\Integrations\Strava\Client\Client\Resource;
use App\Integrations\Strava\Client\Models\StravaClient;

class Webhook extends Resource
{

    public function webhookExists(StravaClient $clientModel): bool
    {
        $response = $this->request->unauthenticatedRequest('GET', 'push_subscriptions', [
            'json' => [
                'client_id' => $clientModel->client_id,
                'client_secret' => $clientModel->client_secret,
            ]
        ]);


        $content = $this->request->decodeResponse($response);

        $exists = true;
        if(count($content) === 0) {
            $exists = false;
        }

        return $exists;
    }

    public function createWebhook(StravaClient $clientModel)
    {
        $response = $this->request->unauthenticatedRequest('POST', 'push_subscriptions', [
            'json' => [
                'client_id' => $clientModel->client_id,
                'client_secret' => $clientModel->client_secret,
                'callback_url' => route('strava.webhook.verify', ['client' => $clientModel]),
                'verify_token' => $clientModel->webhook_verify_token
            ]
        ]);

        return $this->request->decodeResponse($response);
    }


}
