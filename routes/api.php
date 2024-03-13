<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ContentFacilityController;
use App\Http\Controllers\ContentGameController;
use App\Http\Controllers\ContentSectionController;
use App\Http\Controllers\ReservationController;
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

Route::apiResource('reservations', ReservationController::class);
Route::apiResource('content-games', ContentGameController::class);
Route::apiResource('content-facilities', ContentFacilityController::class);
Route::apiResource('content-sections', ContentSectionController::class);

Route::post('/midtrans-callback', [ReservationController::class, 'callback']);
Route::post('/reservations/{id}', [ReservationController::class, 'updateById']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);