<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Client\Models\StravaClient;
use App\Integrations\Strava\Webhooks\HandleDeletedActivity;
use App\Integrations\Strava\Webhooks\HandleIndexingActivity;
use App\Integrations\Strava\Webhooks\Payload;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class IncomingWebhookController extends Controller
{
    public function verify(Request $request, StravaClient $client)
    {
        $request->validate([
            'hub_mode' => 'required|string|in:subscribe',
            'hub_verify_token' => 'required|string|in:' . $client->webhook_verify_token,
            'hub_challenge' => 'required|string'
        ]);

        return response()->json(['hub.challenge' => $request->input('hub_challenge')]);
    }

    public function incoming(Request $request, StravaClient $client)
    {
        \Log::info($request->all());

        try {
            $request->validate(Payload::rules());
        } catch (ValidationException $e) {
            \Log::info(json_encode($e->errors()));
        }
        $payload = Payload::createFromRequest($request);
        \Log::info($payload->toArray());

        if ($payload->getObjectType() === 'activity') {
            if (in_array($payload->getAspectType(), ['update', 'create'])) {
                HandleIndexingActivity::dispatch($payload);
            } elseif ($payload->getAspectType() === 'delete') {
                HandleDeletedActivity::dispatch($payload);
            }
        }

        return response('', 200);
    }
}
