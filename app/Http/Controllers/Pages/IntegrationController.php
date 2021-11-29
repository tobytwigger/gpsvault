<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Integrations\Integration;
use App\Integrations\Strava\Client\Log\ConnectionLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class IntegrationController extends Controller
{

    public function destroy(Integration $integration)
    {
        abort_if(
            !$integration->connected(Auth::user()),
            400,
            sprintf('You have not connected your account to %s', $integration->name())
        );

        $integration->disconnect(Auth::user());

        app(ConnectionLog::class)->setIntegration($integration->id())->setUserId(Auth::id())->success('Unlinked account from Strava');

        return redirect()->route('sync.index');
    }


}
