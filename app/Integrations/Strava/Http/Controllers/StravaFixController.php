<?php

namespace App\Integrations\Strava\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class StravaFixController extends Controller
{

    public function fix(Request $request)
    {
        $request->validate(['files' => 'array', 'files.*' => 'file|mimes:zip']);

        dd($request->allFiles());

    }

}
