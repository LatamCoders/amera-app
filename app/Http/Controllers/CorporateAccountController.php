<?php

namespace App\Http\Controllers;

use App\Services\BookingService;
use App\Services\CorporateAccountService;
use App\Services\ReservationCodeService;
use App\Services\SelfPayService;
use App\utils\CustomHttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPUnit\Exception;

class CorporateAccountController extends Controller
{
    protected $_CorporateAccountService;
    protected $_BookingService;
    protected $_SelfPayService;
    protected $_ReservationCodeService;

    public function __construct(CorporateAccountService $CorporateAccountService, BookingService $bookingService, SelfPayService $selfPay, ReservationCodeService $reservationCodeService)
    {
        $this->middleware('auth:users', ['except' => ['CaLogin', 'CaRegister']]);
        $this->_CorporateAccountService = $CorporateAccountService;
        $this->_BookingService = $bookingService;
        $this->_SelfPayService = $selfPay;
        $this->_ReservationCodeService = $reservationCodeService;
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
            $res = $this->_SelfPayService->SelfPaySignIn($request, null, false);

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

    public function GetCaBooking($caId): JsonResponse
    {
        try {
            $res = $this->_BookingService->GetBookingCaData($caId);

            return CustomHttpResponse::HttpResponse('OK', $res, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function GetMyPaymentMethod($caId): JsonResponse
    {
        try {
            $res = $this->_CorporateAccountService->GetCaCreditCard($caId);

            return CustomHttpResponse::HttpResponse('OK', $res, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Generate reservation code
     */
    public function ReservationCodeGenerate(Request $request): JsonResponse
    {
        try {
            $this->_ReservationCodeService->GenerateReservationCode($request->query('user_id'));

            return CustomHttpResponse::HttpResponse('OK', "Reservation code generated", 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function ModifyCorporateAccount(Request $request,$corporateId): JsonResponse
    {
        try {
            $this->_CorporateAccountService->ModifyCorporateAccount($request,$corporateId);

            return CustomHttpResponse::HttpResponse('OK', '', 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function ModifyCorporateAccountPersonalInfo(Request $request,$id): JsonResponse
    {
        try {
            $this->_CorporateAccountService->ModifyCorporateAccountPersonalInfo($request,$id);

            return CustomHttpResponse::HttpResponse('OK', '', 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }
}
