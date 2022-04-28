<?php

namespace App\Http\Controllers\Pages\Backup;

class CancelBackupController
{
    public function cancel()
    {
        return redirect()->route('backup.index');
    }
}
