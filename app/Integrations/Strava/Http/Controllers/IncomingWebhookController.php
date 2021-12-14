<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IncomingWebhookController extends Controller
{

    public function incoming(Request $request)
    {
        if($request->input('hub_mode') === 'subscribe') {
            if(config('strava.verify_token') !== $request->input('hub_verify_token')) {
                throw new \Exception('Strava verify token mismatch');
            }
            return response()->json(['hub_challenge' => $request->input('hub_challenge')]);
        }
        return response('Verification not complete', 403);
    }

}
