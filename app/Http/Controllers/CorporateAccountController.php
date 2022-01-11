<?php

namespace App\Http\Controllers;

use App\Models\CorporateAccountPersonalInfo;
use App\utils\CustomHttpResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPUnit\Util\Exception;

class CorporateAccountController extends Controller
{
    public function Register(Request $request): JsonResponse
    {
        try {
            $CA = new CorporateAccountPersonalInfo();

            $CA->telephone_number = $request->telephone_number;
            $CA->fax_number = $request->fax_number;
            $CA->email = $request->email;
            $CA->website = $request->website;
            $CA->contact_name = $request->contact_name;
            $CA->contact_number = $request->contact_number;

            $CA->save();

            return CustomHttpResponse::HttpResponse('Corporate account saved', '', 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function CaLogin(Request $request): JsonResponse
    {
        $ca = CorporateAccountPersonalInfo::where('email', $request->email)->exists();

        if ($ca) {
            if ($request->password == 'caamera') {
                return CustomHttpResponse::HttpResponse('Login successfully', true, 200);
            } else {
                return CustomHttpResponse::HttpResponse('Incorrect', false, 200);
            }
        } else {
            return CustomHttpResponse::HttpResponse('No user', '', 404);
        }
    }
}
