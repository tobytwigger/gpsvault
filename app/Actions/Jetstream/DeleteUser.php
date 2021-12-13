<?php

namespace App\Actions\Jetstream;

use App\Integrations\Dropbox\Models\DropboxToken;
use App\Models\File;
use App\Models\User;
use Laravel\Jetstream\Contracts\DeletesUsers;

class DeleteUser implements DeletesUsers
{
    /**
     * Delete the given user.
     *
     * @param  User  $user
     * @return void
     */
    public function delete($user)
    {
        $user->delete();
    }
}
