<?php

namespace App\Http\Controllers;

use App\Services\AmeraAdminService;
use App\utils\CustomHttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AmeraAdminController extends Controller
{
    protected $_AmeraAdminService;

    public function __construct(AmeraAdminService $AmeraAdminService)
    {
        $this->middleware('auth:users', ['except' => ['AdminLogin', 'AdminRegister']]);
        $this->_AmeraAdminService = $AmeraAdminService;
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
}
