<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
 */

Route::post('/token', [\App\Http\Controllers\Auth\SanctumAuthController::class, 'login']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:sanctum')->as('api.')->group(function () {
    Route::apiResource('activity', \App\Http\Controllers\Api\ActivityController::class)->only(['index', 'show']);
    Route::apiResource('place', \App\Http\Controllers\Api\PlaceController::class)->only(['index']);
    Route::apiResource('route', \App\Http\Controllers\Api\RouteController::class)->only(['index', 'show']);
    Route::apiResource('tour', \App\Http\Controllers\Api\TourController::class)->only(['index']);
    Route::apiResource('backup', \App\Http\Controllers\Api\BackupController::class)->only(['index']);
});
