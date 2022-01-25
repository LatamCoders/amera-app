<?php

namespace App\Http\Controllers;

use App\Models\CorporateAccountPersonalInfo;
use App\Services\BookingService;
use App\Services\CorporateAccountService;
use App\utils\CustomHttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPUnit\Util\Exception;

class CorporateAccountController extends Controller
{
    protected $_CorporateAccountService;
    protected $_BookingService;

    public function __construct(CorporateAccountService $CorporateAccountService, BookingService $bookingService)
    {
        $this->middleware('auth:users', ['except' => ['CaLogin', 'CaRegister']]);
        $this->_CorporateAccountService = $CorporateAccountService;
        $this->_BookingService = $bookingService;
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
            $this->_BookingService->AddBooking($request, $request->clientId);

            return CustomHttpResponse::HttpResponse('Booking saved', '', 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }
}
