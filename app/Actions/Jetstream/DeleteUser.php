<?php

namespace App\Actions\Jetstream;

use App\Integrations\Dropbox\Models\DropboxToken;
use App\Models\File;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     *
     * @param  mixed  $user
     * @return void
     */
    public function delete($user)
    {
        $user->deleteProfilePhoto();
        $user->tokens->each->delete();
        $user->stravaTokens()->delete();
        DropboxToken::where('user_id', $user->id)->delete();
        $user->activities()->delete();
        $user->delete();
    }
}
