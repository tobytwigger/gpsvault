<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
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
        \Log::info(json_encode($request->all()));
    }

}
