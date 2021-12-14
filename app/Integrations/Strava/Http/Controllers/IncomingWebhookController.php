<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
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
        $request->validate([
            'object_type' => 'required|string|in:activity,athlete',
            'object_id' => 'required|integer',
            'aspect_type' => 'required|string|in:create,update,delete',
            'updates' => 'required|array',
            'owner_id' => ['required', 'integer', function ($attribute, $value, $fail) {
                if (!User::whereAdditionalData('strava_athlete_id', $value)->exists()) {
                    $fail('The '.$attribute.' has not requested webhooks.');
                }
            }],
            'subscription_id' => 'required|integer',
            'event_time' => 'required|timestamp'
        ]);
        \Log::info(json_encode($request->all()));
    }

}
