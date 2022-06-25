<?php

namespace App\Http\Controllers;

use App\Services\AmeraAdminService;
use App\Services\AmeraUserService;
use App\Services\BookingService;
use App\Services\ContactUsService;
use App\Services\CorporateAccountService;
use App\utils\CustomHttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AmeraAdminController extends Controller
{
    protected $_AmeraAdminService;
    protected $_BookingService;
    protected $_CorporateAccountService;
    protected $_AmeraUserService;
    protected $_ContactUsService;

    public function __construct(AmeraAdminService $AmeraAdminService, BookingService $bookingService, CorporateAccountService $corporateAccountService, AmeraUserService $ameraUserService, ContactUsService $ContactUsService)
    {
        $this->middleware('auth:users', ['except' => ['AdminLogin', 'AdminRegister']]);
        $this->_AmeraAdminService = $AmeraAdminService;
        $this->_BookingService = $bookingService;
        $this->_CorporateAccountService = $corporateAccountService;
        $this->_AmeraUserService = $ameraUserService;
        $this->_ContactUsService = $ContactUsService;
    }

    public function AdminRegister(Request $request): JsonResponse
    {
        try {
            $response = $this->_AmeraAdminService->RegisterAdmin($request);

            return CustomHttpResponse::HttpResponse($response, '', 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function AdminLogin(Request $request): JsonResponse
    {
        try {
            $response = $this->_AmeraAdminService->AdminLogin($request);

            return CustomHttpResponse::HttpResponse('Login successfully', $response, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function AdminLogout(): JsonResponse
    {
        try {
            $response = $this->_AmeraAdminService->AdminLogOut();

            return CustomHttpResponse::HttpResponse('OK', $response, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function AdminProfile($adminId): JsonResponse
    {
        try {
            $response = $this->_AmeraAdminService->GetAdminData($adminId);

            return CustomHttpResponse::HttpResponse('OK', $response, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function CorporateAccountList(): JsonResponse
    {
        try {
            $response = $this->_AmeraAdminService->GetCorporateAccountList();

            return CustomHttpResponse::HttpResponse('OK', $response, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function UserStatus(Request $request): JsonResponse
    {
        try {
            $this->_AmeraAdminService->ChangeUserStatus($request->userId);

            return CustomHttpResponse::HttpResponse('Corporate account activated successfully', [], 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function BookingList(Request $request): JsonResponse
    {
        try {
            $list = $this->_BookingService->GetBookingList($request->query('status'));

            return CustomHttpResponse::HttpResponse('OK', $list, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function DriverList(): JsonResponse
    {
        try {
            $drivers = $this->_AmeraAdminService->GetDriverList();

            return CustomHttpResponse::HttpResponse('OK', $drivers, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function AssignDriver($bookingId, $driverId): JsonResponse
    {
        try {
            $this->_AmeraAdminService->AssignDriverToBooking($driverId, $bookingId);

            return CustomHttpResponse::HttpResponse('Driver assigned', '', 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function ApproveDocuments($driverId, Request $request): JsonResponse
    {
        try {
            $this->_AmeraAdminService->ApproveDriverDocuments($driverId, $request->query('document'));

            return CustomHttpResponse::HttpResponse('Document approved', '', 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function GetCorporateAccountInfo($caId): JsonResponse
    {
        try {
            $caData = $this->_CorporateAccountService->GetCorporateAccountData($caId);

            return CustomHttpResponse::HttpResponse('OK', $caData, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function GetBookingInfo($bookingId): JsonResponse
    {
        try {
            $bookingData = $this->_BookingService->GetBookingData($bookingId);

            return CustomHttpResponse::HttpResponse('OK', $bookingData, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function DriverInfo($driverId): JsonResponse
    {
        try {
            $bookingData = $this->_AmeraAdminService->GetDriverInfo($driverId);

            return CustomHttpResponse::HttpResponse('OK', $bookingData, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function GetUsersList(): JsonResponse
    {
        try {
            $userData = $this->_AmeraUserService->UserList();

            return CustomHttpResponse::HttpResponse('OK', $userData, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function ApproveTripCancellation($bookingId): JsonResponse
    {
        try {
            $response = $this->_AmeraAdminService->ApproveCancellationTrip($bookingId);

            return CustomHttpResponse::HttpResponse('OK', $response, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function ModifyUser(Request $request, $ameraUserId): JsonResponse
    {
        try {
            $userData = $this->_AmeraUserService->GetAndModifyUser($request->query('action'), $ameraUserId, $request);

            return CustomHttpResponse::HttpResponse('OK', $userData, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function MarkBookingAsPaid($bookingId): JsonResponse
    {
        try {
            $this->_AmeraAdminService->MarkAsDriverPaid($bookingId);

            return CustomHttpResponse::HttpResponse('OK', 'Marked as paid', 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function DeleteCorporateAccount($ameraUserId): JsonResponse
    {
        try {
            $res = $this->_AmeraAdminService->DeleteCaUser($ameraUserId);

            return CustomHttpResponse::HttpResponse($res, [], 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function GetCaPaymentMethod($caUserId): JsonResponse
    {
        try {
            $res = $this->_CorporateAccountService->GetCaCreditCard($caUserId);

            return CustomHttpResponse::HttpResponse('OK', $res, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function DeleteDriverUser($driverId): JsonResponse
    {
        try {
            $res = $this->_AmeraAdminService->DeleteDriverUser($driverId);

            return CustomHttpResponse::HttpResponse($res, [], 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function DeleteBooking($bookingId): JsonResponse
    {
        try {
            $res = $this->_AmeraAdminService->DeleteBooking($bookingId);

            return CustomHttpResponse::HttpResponse($res, [], 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function SaveContactUs(Request $request): JsonResponse
    {
        try {
            $this->_ContactUsService->SetContactUs($request);

            return CustomHttpResponse::HttpResponse('Contact Us saved', [], 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function ShowSelfpayList(Request $request, $clientId = null): JsonResponse
    {
        try {
            $res = $this->_AmeraAdminService->SelfpayList($request->query('type'), $clientId);

            return CustomHttpResponse::HttpResponse('OK', $res, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function ModifySelfPay(Request $request, $clientId): JsonResponse
    {
        try {
            $res = $this->_AmeraAdminService->ModifySelfPay($request, $clientId);

            return CustomHttpResponse::HttpResponse('OK', $res, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function DeleteSelfPay($clientId): JsonResponse
    {
        try {
            $res = $this->_AmeraAdminService->DeleteSelfPay($clientId);

            return CustomHttpResponse::HttpResponse('OK', $res, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function ShowAllCharges(): JsonResponse
    {
        try {
            $res = $this->_AmeraAdminService->ShowChargeList();

            return CustomHttpResponse::HttpResponse('OK', $res, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function ShowOneCharge($chargeId): JsonResponse
    {
        try {
            $res = $this->_AmeraAdminService->ShowOneCharge($chargeId);

            return CustomHttpResponse::HttpResponse('OK', $res, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function ChangeUserRole($ameraUserId, Request $request): JsonResponse
    {
        try {
            $res = $this->_AmeraAdminService->ChangeUserRole($ameraUserId, $request->role);

            return CustomHttpResponse::HttpResponse($res, [], 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function ChangeBookingReturnTime($bookingId, Request $request): JsonResponse
    {
        try {
            $this->_BookingService->UpdateBookingReturnTime($bookingId, $request->return_time);

            return CustomHttpResponse::HttpResponse('Return time changed successfully', [], 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }
}
