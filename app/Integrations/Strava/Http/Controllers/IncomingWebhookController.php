<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IncomingWebhookController extends Controller
{

    public function incoming(Request $request)
    {
        if($request->input('hub.mode') === 'subscribe') {
            if(config('strava.verify_token') !== $request->input('hub.verify_token')) {
                throw new \Exception('Strava verify token mismatch');
            }
            return response()->json(['hub.challenge' => $request->input('hub.challenge')]);
        }
        return response('Verification not complete', 403);
    }

}
