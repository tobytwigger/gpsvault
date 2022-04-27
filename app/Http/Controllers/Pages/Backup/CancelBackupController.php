<?php

namespace App\Http\Controllers\Pages\Backup;

use App\Services\Sync\Sync;
use Illuminate\Support\Facades\Auth;

class CancelBackupController
{
    public function cancel(Sync $sync)
    {
        abort_if($sync->user_id !== Auth::id(), 403, 'You do not own this sync.');

        $sync->cancel();

        return redirect()->route('backup.index');
    }
}
