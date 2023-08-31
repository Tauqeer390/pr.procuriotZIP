<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ForgotPasswordController;
use App\Http\Controllers\Api\ManageProcuriotController;
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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('get/venues', [ApiController::class, 'getVenues']);
Route::get('get/venues2', [ApiController::class, 'getVenues2']);
Route::get('get/gateways/{venue_id}', [ApiController::class, 'getGateways']);
Route::get('get/beacons/{gateway_id}', [ApiController::class, 'getbeacons']);
Route::get('get/beacon/{beacon_id}', [ApiController::class, 'getbeacon']);

Route::post('stripe', [ApiController::class, 'stripePost']);
Route::get('getvenuesList/{id}', [ApiController::class, 'getvenuesList']);

Route::group([
    'prefix' => 'auth',
], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('social-login', [AuthController::class, 'socialLogin']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('forgot-password',[ForgotPasswordController::class, 'forgotPassword'] );
    Route::post('verify-pin',[ForgotPasswordController::class, 'verifyPin'] );
    Route::post('reset-password',[ForgotPasswordController::class, 'resetPassword'] );
});
Route::get('get-location',[ManageProcuriotController::class, 'getLocations'] );
Route::get('get-beacons/{gateWayId}',[ManageProcuriotController::class, 'getBeacons'] );
Route::get('get-beacon-products/{beaconId}',[ManageProcuriotController::class, 'getBeaconProducts'] );
Route::get('get-categories',[ManageProcuriotController::class, 'getCategories'] );
Route::get('search-product/{vendorId}',[ManageProcuriotController::class, 'searchProducts'] );
Route::get('get-gateway/{venueId}',[ManageProcuriotController::class, 'getGateway'] );
Route::post('payProduct/{productID}',[ManageProcuriotController::class, 'payProduct'] );
Route::group([
    'middleware' => 'auth:api',
], function () {
    Route::get('get-list',[ManageProcuriotController::class, 'getProcuriotListByName'] );
        // Route::get('logout', 'AuthController@logout');
        // Route::get('user', 'AuthController@user');
    });
