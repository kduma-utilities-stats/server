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

Route::scopeBindings()->group(function () {
    Route::get('/status', function (Request $request) {
        return [
            'version' => config('app.version', 'develop'),
            'laravel' => app()->version(),
            'authenticated' => $request->user() !== null,
            'user' => $request->user() === null
                ? null
                : (new \App\Http\Resources\UserResource($request->user()))->toArray($request),
        ];
    });

    Route::post('/login', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'storeApi'])
        ->middleware('guest')
        ->name('login_api');

    Route::post('/logout', [\App\Http\Controllers\Auth\AuthenticatedSessionController::class, 'destroyApi'])
        ->middleware('auth:sanctum')
        ->name('logout_api');
});

Route::middleware(['auth:sanctum'])->scopeBindings()->group(function () {
//    Route::get('/user', function (Request $request) {
//        return $request->user();
//    });

    Route::apiResource('meter', \App\Http\Controllers\MeterController::class);
    Route::apiResource('meter.counter', \App\Http\Controllers\MeterCounterController::class)->only(['index', 'store']);
    Route::apiResource('counter', \App\Http\Controllers\CounterController::class)->except(['index', 'store']);
    Route::apiResource('reading', \App\Http\Controllers\ReadingController::class);
    Route::apiResource('reading.value', \App\Http\Controllers\ReadingValueController::class)->only(['index', 'store']);
    Route::apiResource('value', \App\Http\Controllers\ValueController::class)->except(['index', 'store']);
});



