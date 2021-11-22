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

    Route::get('stats/{activity}', [\App\Http\Controllers\Pages\StatsController::class, 'index'])->name('stats');

    Route::prefix('strava')->group(function() {
        Route::get('login', [\App\Http\Controllers\Pages\StravaController::class, 'login'])->name('strava.login');
        Route::get('callback', [\App\Http\Controllers\Pages\StravaController::class, 'callback'])->name('strava.callback');
        Route::get('logs', [\App\Http\Controllers\Pages\ConnectionLogController::class, 'index'])->name('strava.logs');

    });

});
