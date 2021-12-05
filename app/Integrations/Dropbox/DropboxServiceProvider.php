<?php

namespace App\Integrations\Dropbox;

use App\Integrations\Dropbox\Client\PersistentDataStore;
use App\Integrations\Dropbox\Http\Controllers\DropboxController;
use App\Integrations\Dropbox\Tasks\ExportFullBackup;
use App\Integrations\Integration;
use App\Integrations\Dropbox\Models\DropboxToken;
use App\Services\Sync\Task;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;

class DropboxServiceProvider extends ServiceProvider
{

    public function boot() {
        Task::registerTask(ExportFullBackup::class);

        Integration::registerIntegration('dropbox', DropboxIntegration::class);

        Route::middleware(['web', 'auth:sanctum', 'verified'])->prefix('dropbox')->group(function() {
            Route::get('callback', [DropboxController::class, 'callback'])->name('dropbox.callback');
        });
    }

}
