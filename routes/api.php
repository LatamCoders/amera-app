<?php

use App\Http\Controllers\DriverController;
use App\Http\Controllers\SelfPayController;
use App\Models\Driver;
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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['prefix' => 'v1', 'middleware' => 'onlyAjax'], function () {
    Route::group(['prefix' => 'auth'], function () {
        /*
         * Controller: SelfPay
         *
         * Method: Get
         */
        Route::middleware('auth:selfpay')->get('client/logout', [SelfPayController::class, 'LogOut']);

        /*
         * Controller: SelfPay
         *
         * Method: Post
         */
        Route::post('client/login', [SelfPayController::class, 'UserLogin'])->name('login');
        Route::post('client/register', [SelfPayController::class, 'SelfPaySignIn']);

        /*
         * Controller: Driver
         *
         * Method: Get
         */
        Route::middleware('auth:driver')->get('driver/logout', [DriverController::class, 'LogOut']);

        /*
         * Controller: Driver
         *
         * Method: Post
         */
        Route::post('driver/login', [DriverController::class, 'DriverLogin']);
        Route::post('driver/signup', [DriverController::class, 'DriverSignUp']);
    });

    /*
     * Controller: SelfPay
     */
    Route::group(['prefix' => 'client'], function () {
        /*
         * Auth required
         */
        Route::group(['middleware' => 'auth:selfpay'], function () {
            /*
             * Method: Get
             */
            Route::get('{clientId}/profile/data', [SelfPayController::class, 'getClientData']);
            Route::get('logout', [SelfPayController::class, 'LogOut']);

            /*
             * Method: Post
             */
            Route::post('{clientId}/profile/update', [SelfPayController::class, 'UpdateProfileData']);
            Route::post('{clientId}/profile/image/update', [SelfPayController::class, 'UpdateProfileImage']);
            Route::post('{clientId}/profile/payment/creditcard/add', [SelfPayController::class, 'AddCreditCard']);
        });

        Route::post('encrypt', [SelfPayController::class, 'TestEncipt']);
    });

    /*
     * Controller: Driver
     */
    Route::group(['prefix' => 'driver'], function () {
        /*
         * Auth required
         */
        Route::group(['middleware' => 'auth:driver'], function () {
            /*
             * Method: Get
             */

            Route::get('logout', [DriverController::class, 'LogOut']);

            /*
             * Method: Post
             */
            Route::post('{driverId}/profile/update', [DriverController::class, 'UpdateProfileData']);
            Route::post('{driverId}/profile/image/update', [DriverController::class, 'UpdateProfileImage']);
        });
    });

    Route::get('{driverId}/profile/data', [DriverController::class, 'GetDriverData']);
    Route::post('images', [DriverController::class, 'TestImages']);
});
