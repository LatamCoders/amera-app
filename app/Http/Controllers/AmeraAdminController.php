<?php

namespace App\Http\Controllers;

use App\Services\AmeraAdminService;
use App\Services\AmeraUserService;
use App\Services\BookingService;
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

    public function __construct(AmeraAdminService $AmeraAdminService, BookingService $bookingService, CorporateAccountService $corporateAccountService, AmeraUserService $ameraUserService)
    {
        $this->middleware('auth:users', ['except' => ['AdminLogin', 'AdminRegister']]);
        $this->_AmeraAdminService = $AmeraAdminService;
        $this->_BookingService = $bookingService;
        $this->_CorporateAccountService = $corporateAccountService;
        $this->_AmeraUserService = $ameraUserService;
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

            return CustomHttpResponse::HttpResponse('User status change', [], 200);
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
}
