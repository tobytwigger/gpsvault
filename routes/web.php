<?php

use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth:sanctum', 'verified'])->group(function() {
    Route::get('/dashboard', [\App\Http\Controllers\Pages\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('activity', \App\Http\Controllers\Pages\ActivityController::class);

    Route::post('sync/start', [\App\Http\Controllers\Pages\SyncController::class, 'sync'])->name('sync');
    Route::resource('sync', \App\Http\Controllers\Pages\SyncController::class);

    Route::prefix('activity/{activity}')->group(function() {
        Route::get('download', [\App\Http\Controllers\Pages\ActivityController::class, 'download']);
        Route::resource('file', \App\Http\Controllers\Pages\ActivityFileController::class, ['as' => 'activity'])->only(['destroy', 'update']);
        Route::get('file/{file}/download', [\App\Http\Controllers\Pages\ActivityFileController::class, 'download'])->name('activity.file.download');
    });

    Route::get('stats/{activity}', [\App\Http\Controllers\Pages\StatsController::class, 'index'])->name('stats');

    Route::get('/integration/{integration}/login', [\App\Http\Controllers\Pages\IntegrationLoginController::class, 'login'])->name('integration.login');
    Route::delete('/integration/{integration}', [\App\Http\Controllers\Pages\IntegrationController::class, 'destroy'])->name('integration.destroy');
    Route::get('/integration/{integration}/logs', [\App\Http\Controllers\Pages\ConnectionLogController::class, 'index'])->name('integration.logs');

});
