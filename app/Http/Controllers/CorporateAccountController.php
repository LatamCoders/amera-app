<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use App\Services\CorporateAccountService;
use App\Services\SelfPayService;
use App\utils\CustomHttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CorporateAccountController extends Controller
{
    protected $_CorporateAccountService;
    protected $_BookingService;
    protected $_SelfPayService;

    public function __construct(CorporateAccountService $CorporateAccountService, BookingService $bookingService, SelfPayService $selfPay)
    {
        $this->middleware('auth:users', ['except' => ['CaLogin', 'CaRegister']]);
        $this->_CorporateAccountService = $CorporateAccountService;
        $this->_BookingService = $bookingService;
        $this->_SelfPayService = $selfPay;
    }

    public function CaRegister(Request $request): JsonResponse
    {
        try {
            $this->_CorporateAccountService->RegisterCA($request);

            return CustomHttpResponse::HttpResponse('OK', '', 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function CaLogin(Request $request): JsonResponse
    {
        try {
            $response = $this->_CorporateAccountService->CorporateAccountLogin($request);

            return CustomHttpResponse::HttpResponse('Login successfully', $response, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function CaLogout(): JsonResponse
    {
        try {
            $response = $this->_CorporateAccountService->CorporateAccountLogOut();

            return CustomHttpResponse::HttpResponse('OK', $response, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function CaProfile($CaId): JsonResponse
    {
        try {
            $response = $this->_CorporateAccountService->GetCorporateAccountData($CaId);

            return CustomHttpResponse::HttpResponse('OK', $response, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function BookingRegister(Request $request): JsonResponse
    {
        try {
            $this->_BookingService->AddBooking($request, $request->selfpay_id);

            return CustomHttpResponse::HttpResponse('Booking saved', '', 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function RegisterCaClient(Request $request): JsonResponse
    {
        try {
           $res = $this->_SelfPayService->SelfPaySignIn($request, null);

            return CustomHttpResponse::HttpResponse($res, '', 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function CaClientList($caId): JsonResponse
    {
        try {
            $res = $this->_CorporateAccountService->GetCaClientList($caId);

            return CustomHttpResponse::HttpResponse('OK', $res, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }
}
