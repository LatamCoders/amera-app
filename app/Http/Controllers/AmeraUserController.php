<?php

namespace App\Http\Controllers;

use App\Services\AmeraUserService;
use App\Services\BookingService;
use App\utils\CustomHttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AmeraUserController extends Controller
{
    protected $_AmeraUserService;
    protected $_BookingService;

    public function __construct(AmeraUserService $ameraUserService, BookingService $bookingService)
    {
        $this->middleware('auth:users', ['except' => ['Login']]);
        $this->_AmeraUserService = $ameraUserService;
        $this->_BookingService = $bookingService;
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
}
