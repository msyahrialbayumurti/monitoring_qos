<?php

use App\Http\Controllers\AgentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/agent/enroll', [AgentController::class, 'enroll'])
    ->middleware('throttle:10,1');

Route::prefix('agent')
    ->middleware(['agent.token', 'throttle:120,1'])
    ->group(function () {
        Route::get('/config', [AgentController::class, 'config']);
        Route::post('/heartbeat', [AgentController::class, 'heartbeat']);
        Route::post('/measurements', [AgentController::class, 'storeMeasurement']);
    });
