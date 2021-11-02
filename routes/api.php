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
    Route::post('client/register', [\App\Http\Controllers\SelfPayController::class, 'SelfPaySignIn']);
    Route::get('/', [\App\Http\Controllers\SelfPayController::class, 'VerifyCode']);
    Route::post('client/login', [\App\Http\Controllers\SelfPayController::class, 'UserLogin']);
});
