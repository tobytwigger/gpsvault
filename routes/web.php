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
    Route::resource('activity', \App\Http\Controllers\Pages\ActivityController::class)->except(['edit']);

    Route::resource('sync', \App\Http\Controllers\Pages\SyncController::class)->only(['index', 'store', 'destroy']);
    Route::get('file/{file}/download', [\App\Http\Controllers\Pages\FileController::class, 'download'])->name('file.download');
    Route::get('file/{file}/preview', [\App\Http\Controllers\Pages\FileController::class, 'preview'])->name('file.preview');
    Route::redirect('/upload', route('activity.create'))->name('upload');
    Route::get('/documentation', fn() => \Illuminate\Support\Facades\Redirect::away('https://tobytwigger.github.io/cycle-store/'))->name('documentation');

    Route::get('/activity/{activity}/stats', [\App\Http\Controllers\Pages\StatsController::class, 'index'])->name('activity.stats');

    Route::prefix('activity/{activity}')->group(function() {
        Route::get('download', [\App\Http\Controllers\Pages\DownloadController::class, 'downloadActivity'])->name('activity.download');
        Route::resource('file', \App\Http\Controllers\Pages\ActivityFileController::class, ['as' => 'activity'])->only(['destroy', 'update']);
    });

    Route::post('/data/download', [\App\Http\Controllers\Pages\DownloadController::class, 'all'])->name('data.download');

    Route::get('/integration/{integration}/login', [\App\Http\Controllers\Pages\IntegrationLoginController::class, 'login'])->name('integration.login');
    Route::delete('/integration/{integration}', [\App\Http\Controllers\Pages\IntegrationController::class, 'destroy'])->name('integration.destroy');
    Route::get('/integration/{integration}/logs', [\App\Http\Controllers\Pages\ConnectionLogController::class, 'index'])->name('integration.logs');

});
