<?php

namespace App\Integrations\Dropbox\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Integrations\Dropbox\Client\Dropbox;
use App\Integrations\Dropbox\Models\DropboxToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DropboxController extends Controller
{

    public function callback(Request $request)
    {
        $request->validate([
            'code' => 'required',
            'state' => 'required'
        ]);
        $accessToken = Dropbox::auth()->getAccessToken($request->input('code'), $request->input('state'), route('dropbox.callback'));
        DropboxToken::create([
            'access_token' => $accessToken->getToken(),
            'user_id' => Auth::id()
        ]);
        return redirect()->route('sync.index');
    }

}
