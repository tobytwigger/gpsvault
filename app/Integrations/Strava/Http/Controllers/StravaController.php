<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Integrations\Strava\StravaToken;
use App\Integrations\Strava\Client\Strava;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StravaController extends \Illuminate\Routing\Controller
{

    public function login(Request $request, Strava $strava)
    {
        $request->session()->put('state', $state = Str::random(40));

        return new RedirectResponse(
            $strava->redirectUrl(
                route('strava.callback'),
                $state
            )
        );
    }

    public function callback(Request $request, Strava $strava)
    {
        $token = $strava->client()->exchangeCode($request->input('code'));

        $savedToken = StravaToken::makeFromStravaToken($token);

        $savedToken->save();

        return redirect()->route('sync.index');
    }

}
