<?php

namespace App\Integrations\Dropbox;

use App\Integrations\Dropbox\Client\Dropbox;
use App\Integrations\Dropbox\Models\DropboxToken;
use App\Integrations\Integration;
use App\Models\User;

class DropboxIntegration extends Integration
{
    public function id(): string
    {
        return 'dropbox';
    }

    public function serviceUrl(): string
    {
        return 'https://dropbox.com';
    }

    public function name(): string
    {
        return 'Dropbox';
    }

    public function description(): string
    {
        return 'Dropbox is xyz';
    }

    public function functionality(): array
    {
        return [
            'Export backups of your data to Dropbox'
        ];
    }

    public function connected(User $user): bool
    {
        return DropboxToken::where('user_id', $user->id)->exists();
    }

    public function loginUrl(): string
    {
        return Dropbox::auth()->getAuthUrl(route('dropbox.callback'));
    }

    public function disconnect(User $user): void
    {
        DropboxToken::where('user_id', $user->id)->delete();
    }
}
