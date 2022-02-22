<?php

namespace App\Http\Controllers;

use App\Services\AmeraUserService;
use App\Services\CorporateAccountService;
use App\utils\CustomHttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Tymon\JWTAuth\JWTAuth;

class AmeraUserController extends Controller
{
    protected $_AmeraUserService;

    public function __construct(AmeraUserService $ameraUserService)
    {
        $this->middleware('auth:users', ['except' => ['Login']]);
        $this->_AmeraUserService = $ameraUserService;
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
