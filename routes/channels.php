<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('sync.{syncId}', function (\App\Models\User $user, $syncId) {
    return (int) $user->id === (int) \App\Models\Sync::findOrFail($syncId)->user_id;
});

Broadcast::channel('task.{taskId}', function (\App\Models\User $user, $taskId) {
    return (int) $user->id === (int) \App\Models\SyncTask::findOrFail($taskId)->sync->user_id;
});
