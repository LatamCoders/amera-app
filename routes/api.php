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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1'], function () {
    Route::group(['prefix' => 'auth'], function () {
        /*
         * Method: Get
         */
        Route::middleware('auth:selfpay')->get('client/logout', [\App\Http\Controllers\SelfPayController::class, 'LogOut']);

        /*
         * Method: Post
         */
        Route::post('client/login', [\App\Http\Controllers\SelfPayController::class, 'UserLogin'])->name('login');
        Route::post('client/register', [\App\Http\Controllers\SelfPayController::class, 'SelfPaySignIn']);
    });

    /*
     * Controller: SelfPay
     */
    Route::group(['prefix' => 'client'], function () {
        Route::group(['middleware' => 'auth:selfpay'], function () {
            /*
             * Method: Get
             */
            Route::get('{clientId}/profile/data', [\App\Http\Controllers\SelfPayController::class, 'getClientData']);
            Route::get('logout', [\App\Http\Controllers\SelfPayController::class, 'LogOut']);

            /*
             * Method: Post
             */
            Route::post('{clientId}/profile/update', [\App\Http\Controllers\SelfPayController::class, 'UpdateProfileData']);
            Route::post('{clientId}/profile/image/update', [\App\Http\Controllers\SelfPayController::class, 'UpdateProfileImage']);
        });
    });

});
