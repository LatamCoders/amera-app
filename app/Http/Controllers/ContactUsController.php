<?php

namespace App\Http\Controllers;

use App\Services\ContactUsService;
use App\utils\CustomHttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    protected $_ContactUsService;

    public function __construct(ContactUsService $ContactUsService)
    {
        $this->_ContactUsService = $ContactUsService;
    }

    public function ShowContactUs(): JsonResponse
    {
        try {
            $res = $this->_ContactUsService->GetContactUs();

            return CustomHttpResponse::HttpResponse('OK', $res, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }
}
