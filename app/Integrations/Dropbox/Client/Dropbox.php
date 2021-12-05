<?php

namespace App\Integrations\Dropbox\Client;

use App\Integrations\Dropbox\Models\DropboxToken;
use App\Models\User;
use Kunnu\Dropbox\Authentication\DropboxAuthHelper;
use Kunnu\Dropbox\Dropbox as DropboxInstance;
use Kunnu\Dropbox\DropboxApp;

class Dropbox
{

    public static function client(User $user): DropboxInstance
    {
        $token = DropboxToken::where('user_id', $user->id)->orderBy('created_at', 'DESC')->first()
            ?? throw new \Exception('Your account is not connected to Dropbox');

        return static::getDropboxInstance($token->access_token);
    }

    public static function auth(): DropboxAuthHelper
    {
        return static::getDropboxInstance()->getAuthHelper();
    }

    private static function getDropboxInstance(?string $accessToken = null): DropboxInstance
    {
        return new DropboxInstance(
            new DropboxApp(
                config('dropbox.client_id'),
                config('dropbox.client_secret'),
                $accessToken
            ),
            ['persistent_data_store' => app()->make(PersistentDataStore::class)]
        );
    }

}
