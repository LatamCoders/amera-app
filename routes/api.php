<?php

use App\Http\Controllers\DriverController;
use App\Http\Controllers\SelfPayController;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['prefix' => 'v1', 'middleware' => 'onlyAjax'], function () {
    /*
     * Auth
     */
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
            Route::get('{clientId}/rate/get', [SelfPayController::class, 'GetClientRate']);

            /*
             * Method: Get
             * Booking
             */
            Route::get('{clientId}/booking/reserve/add', [SelfPayController::class, 'AddReserve']);

            /*
             * Method: Post
             * Profile
             */
            Route::post('{clientId}/profile/update', [SelfPayController::class, 'UpdateProfileData']);
            Route::post('{clientId}/profile/image/update', [SelfPayController::class, 'UpdateProfileImage']);
            Route::post('{clientId}/profile/payment/creditcard/add', [SelfPayController::class, 'AddCreditCard']);

            /*
             * Method: Post
             * Rate
             */
            Route::post('{clientId}/rate/driver/{driverId}/booking/{bookingId}', [SelfPayController::class, 'RateDriver']);
            Route::post('{clientId}/rate/amera/booking/{bookingId}', [SelfPayController::class, 'ClientRateAmeraExperience']);

            /*
             * Method: Post
             * Booking
             */
            Route::post('{clientId}/booking/add', [SelfPayController::class, 'AddReserve']);
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
             * Method: Post
             * Profile
             */
            Route::post('{driverId}/profile/update', [DriverController::class, 'UpdateProfileData']);
            Route::post('{driverId}/profile/image/update', [DriverController::class, 'UpdateProfileImage']);
            Route::get('{driverId}/profile/data', [DriverController::class, 'GetDriverData']);

            /*
             * Method: Post
             * Rate
             */
            Route::post('{driverId}/rate/client/{clientId}/booking/{bookingId}', [DriverController::class, 'RateSelfPay']);
            Route::post('{driverId}/rate/amera/booking/{bookingId}', [DriverController::class, 'DriverRateAmeraExperience']);
        });

    });
    Route::post('images', [DriverController::class, 'TestImages']);
});
