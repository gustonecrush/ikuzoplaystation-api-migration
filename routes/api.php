<?php

use App\Http\Controllers\Api\BenefitMembershipTierController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\ContentFacilityController;
use App\Http\Controllers\ContentGameController;
use App\Http\Controllers\ContentSectionController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerMembershipController;
use App\Http\Controllers\DateCloseController;
use App\Http\Controllers\MaintenanceFacilityController;
use App\Http\Controllers\MembershipTierController;
use App\Http\Controllers\OpenCloseTimeController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\ReservationSavingTimeController;
use App\Http\Controllers\SectionController;
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

Route::prefix('customer')->group(function () {
    Route::post('register', [CustomerController::class, 'register']);
    Route::post('login', [CustomerController::class, 'login']);

    Route::middleware('auth:customer')->group(function () {
        Route::get('profile', [CustomerController::class, 'profile']);
        Route::put('profile', [CustomerController::class, 'updateProfile']);
        Route::post('reset-password', [CustomerController::class, 'resetPassword']);
        Route::post('logout', [CustomerController::class, 'logout']);
    });
});


Route::apiResource('reservations', ReservationController::class);
Route::apiResource('catalogs', CatalogController::class);
Route::get('/statistics', [ReservationController::class, 'statistics']);
Route::get('/search', [CatalogController::class, 'search']);
Route::get('/export', [ReservationController::class, 'exportExcel']);
Route::delete('/reservations/order/{id}', [ReservationController::class, 'deleteByOrderId']);

Route::apiResource('content-games', ContentGameController::class);

Route::apiResource('content-facilities', ContentFacilityController::class);
Route::post('/content-facilities/{id}', [
    ContentFacilityController::class,
    'updateById',
]);

Route::apiResource('times', OpenCloseTimeController::class);
Route::post('/times/{id}', [OpenCloseTimeController::class, 'updateById']);

Route::apiResource('content-sections', ContentSectionController::class);
Route::post('/content-sections/{id}', [
    ContentSectionController::class,
    'updateById',
]);

Route::apiResource('sections', SectionController::class);
Route::post('/sections/{id}', [SectionController::class, 'updateById']);

Route::apiResource('dates', DateCloseController::class);
Route::post('/dates/{id}', [DateCloseController::class, 'updateById']);

Route::post('/midtrans-callback', [ReservationController::class, 'callback']);
Route::post('/reservations/{id}', [ReservationController::class, 'updateById']);
Route::post('/reservations/seat/{id}', [ReservationController::class, 'updateSeatUser']);

Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);


// MEMBERSHIP TIER
Route::apiResource('membership-tiers', MembershipTierController::class);

// CHECKING MEMBERSHIP
Route::post('/membership/check', [CustomerController::class, 'checkMembership']);
Route::apiResource('customer-memberships', CustomerMembershipController::class);
Route::get('/customers', [CustomerController::class, 'index']);

// Route::apiResource('/membership/benefits', BenefitMembershipTierController::class);

// MAINTENANCE
Route::apiResource('maintenances', MaintenanceFacilityController::class);


// BENEFITS 
Route::put('/customers/{id}/benefits', [CustomerController::class, 'addBenefits']);


Route::get('/reservation-saving-times', [ReservationSavingTimeController::class, 'index']);
Route::post('/reservation-saving-times', [ReservationSavingTimeController::class, 'store']);
Route::post('/reservation-saving-times/{id}', [ReservationSavingTimeController::class, 'update']);
Route::delete('/reservation-saving-times/{id}', [ReservationSavingTimeController::class, 'destroy']);
