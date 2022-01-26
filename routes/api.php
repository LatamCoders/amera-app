<?php

use App\Http\Controllers\AmeraAdminController;
use App\Http\Controllers\CorporateAccountController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\SelfPayController;
use App\Http\Controllers\UserController;
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
        Route::post('client/sendsmscode', [SelfPayController::class, 'SendSmsCode']);

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
        Route::post('driver/sendsmscode', [DriverController::class, 'SendSmsCode']);

        /*
         * Controller: Corporate Account
         *
         * Method: Post
         */
        Route::post('ca/register', [CorporateAccountController::class, 'CaRegister']);
        Route::post('ca/login', [CorporateAccountController::class, 'CaLogin']);
        Route::middleware('auth:users')->post('ca/logout', [CorporateAccountController::class, 'CaLogout']);

        /*
         * Controller: Corporate Account
         *
         * Method: Get
         */


        /*
         * Controller: Admins
         *
         * Method: Post
         */
        Route::post('admin/register', [AmeraAdminController::class, 'AdminRegister']);
        Route::post('admin/login', [AmeraAdminController::class, 'AdminLogin']);
        Route::middleware('auth:users')->post('admin/logout', [AmeraAdminController::class, 'AdminLogout']);

        /*
         * Controller: Admins
         *
         * Method: Get
         */

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
            Route::post('{clientId}/profile/verify', [SelfPayController::class, 'VerifyEmailOrNumber']);

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
            Route::post('booking/{bookingId}/trip/{tripAction}', [SelfPayController::class, 'StartOrEndTrip']);
            Route::post('booking/{bookingId}/services/add', [SelfPayController::class, 'AddAdditionalService']);
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
            Route::post('{driverId}/profile/verify', [DriverController::class, 'VerifyEmailOrNumber']);

            /*
             * Method: Post
             * Rate
             */
            Route::post('{driverId}/rate/client/{clientId}/booking/{bookingId}', [DriverController::class, 'RateSelfPay']);
            Route::post('{driverId}/rate/amera/booking/{bookingId}', [DriverController::class, 'DriverRateAmeraExperience']);
        });

    });

    /*
     * Controller: Corporate account
     */
    Route::group(['prefix' => 'ca'], function () {
        /*
         * Auth required
         */
        Route::group(['middleware' => 'auth:users'], function () {
            /*
             * Method: Get
             * Profile
             */
            Route::get('{CaId}/profile', [CorporateAccountController::class, 'CaProfile']);

            /*
             * Method: Post
             * Panel
             */
            Route::post('panel/booking/add', [CorporateAccountController::class, 'BookingRegister']);
            Route::post('panel/client/add', [CorporateAccountController::class, 'RegisterCaClient']);

            /*
             * Method: Get
             * Panel
             */
            Route::get('{CaId}/panel/client/search', [CorporateAccountController::class, 'CaClientList']);
        });

    });

    /*
     * Controller: Amera Admin
     */
    Route::group(['prefix' => 'admin'], function () {
        /*
         * Auth required
         */
        Route::group(['middleware' => 'auth:users'], function () {
            /*
             * Method: Get
             * Profile
             */
            Route::get('{adminId}/profile', [AmeraAdminController::class, 'AdminProfile']);
        });

        /*
         * Method: Post
         * Panel
         */
        Route::get('panel/ca/list', [AmeraAdminController::class, 'CorporateAccountList']);
        Route::post('panel/users/change-user-status', [AmeraAdminController::class, 'UserStatus']);
    });


    Route::post('images', [DriverController::class, 'TestImages']);
});
