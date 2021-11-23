<?php

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Integrations\Integration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class IntegrationLoginController extends Controller
{

    public function login(Integration $integration)
    {
        abort_if(
            $integration->connected(Auth::user()),
            403,
            sprintf('You\'ve already connected your account to %s', $integration->name())
        );

        return redirect()->to($integration->loginUrl());
    }


}
