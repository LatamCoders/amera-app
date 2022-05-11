<?php

namespace App\Http\Controllers;

use App\Services\AmeraUserService;
use App\Services\BookingService;
use App\Services\CorporateAccountService;
use App\utils\CustomHttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AmeraUserController extends Controller
{
    protected $_AmeraUserService;
    protected $_BookingService;
    protected $_CorporateAccountService;

    public function __construct(AmeraUserService $ameraUserService, BookingService $bookingService, CorporateAccountService $CorporateAccountService)
    {
        $this->middleware('auth:users', ['except' => ['Login', 'ValidEmailAndSendCode', 'RecoverPassword', 'ChangePassword']]);
        $this->_AmeraUserService = $ameraUserService;
        $this->_BookingService = $bookingService;
        $this->_CorporateAccountService = $CorporateAccountService;
    }

    public function Login(Request $request): JsonResponse
    {
        try {
            $response = $this->_AmeraUserService->AmeraUserLogin($request);

            return CustomHttpResponse::HttpResponse('Login successfully', $response, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function Logout(): JsonResponse
    {
        try {
            $response = $this->_AmeraUserService->AmeraUserLogOut();

            return CustomHttpResponse::HttpResponse('OK', $response, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Send recovery password code
     */
    public function ValidEmailAndSendCode(Request $request): JsonResponse
    {
        try {
            $this->_CorporateAccountService->ValidEmailAndSendCode($request);

            return CustomHttpResponse::HttpResponse('Code send successfully', [], 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Validate verification code
     */
    public function ValidateRecoveryCode(Request $request): JsonResponse
    {
        try {
           $res = $this->_CorporateAccountService->ValidateRecoveryCode($request);

            return CustomHttpResponse::HttpResponse($res, [], 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Change password
     */
    public function ChangePassword(Request $request): JsonResponse
    {
        try {
            $this->_CorporateAccountService->RecoverPassword($request);

            return CustomHttpResponse::HttpResponse('Password changed successfully', [], 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }
}
