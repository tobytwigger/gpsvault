<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Webhooks\HandleDeletedActivity;
use App\Integrations\Strava\Webhooks\HandleIndexingActivity;
use App\Integrations\Strava\Webhooks\Payload;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncomingWebhookController extends Controller
{

    public function verify(Request $request)
    {
        $request->validate([
            'hub_mode' => 'required|string|in:subscribe',
            'hub_verify_token' => 'required|string|in:' . config('strava.verify_token'),
            'hub_challenge' => 'required|string'
        ]);

        return response()->json(['hub.challenge' => $request->input('hub_challenge')]);

    }

    public function incoming(Request $request)
    {
        $request->validate(Payload::rules());
        $payload = Payload::createFromRequest($request);
        if($payload->getObjectType() === 'activity') {
            if(in_array($payload->getAspectType(), ['update', 'create'])) {
                HandleIndexingActivity::dispatch($payload);
            } elseif($payload->getAspectType() === 'delete') {
                HandleDeletedActivity::dispatch($payload);
            }
        }
        return response('', 200);
    }

}
