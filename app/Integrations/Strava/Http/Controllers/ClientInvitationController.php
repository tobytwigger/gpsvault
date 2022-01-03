<?php

namespace App\Integrations\Strava\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Integrations\Strava\Models\StravaClient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Linkeys\UrlSigner\Models\Link;

class ClientInvitationController extends Controller
{

    public function invite(Request $request, StravaClient $client)
    {
        // Create the link
        $link = \Linkeys\UrlSigner\Facade\UrlSigner::generate(
            route('strava.client.accept', ['client' => $client->id]),
            ['client_id' => $client->id],
            '+24 hours'
        );

        $client->invitation_link_uuid = $link->uuid;
        $client->save();

        return redirect()->route('strava.client.index');
    }

    public function leave(Request $request, StravaClient $client)
    {
        abort_if(!$client->sharedUsers()->where('user_id', Auth::id())->exists(), 403, 'You are not a part of this client.');

        $client->sharedUsers()->detach(Auth::id());

        return redirect()->route('strava.client.index');
    }

    public function accept(Request $request, StravaClient $client)
    {
        $link = $request->get(Link::class);
        abort_if($client->invitation_link_uuid !== $link->uuid, 403, 'The invitation is not valid or has expired.');
        abort_if($client->user_id === Auth::id(), 403, 'You cannot accept your own invitation.');
        abort_if($client->sharedUsers()->where('user_id', Auth::id())->exists(), 403, 'You have already accepted this invitation');

        $client->sharedUsers()->attach(Auth::id());

        return redirect()->route('strava.client.index');
    }


}
