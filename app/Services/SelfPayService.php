<?php

namespace App\Services;

use App\Models\SelfPay;
use App\utils\CustomHttpResponse;
use App\utils\UploadImage;
use Illuminate\Http\Request;
use PHPUnit\Exception;

class SelfPayService
{
    public function SelfPaySignIn(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $selfpay = new SelfPay();

            $profile = UploadImage::UploadProfileImage($request->file('profile_picture'), $request->phone_number);

            $selfpay->client_id = 'SP' . rand(100, 999);
            $selfpay->name = $request->name;
            $selfpay->lastname = $request->lastname;
            $selfpay->phone_number = $request->phone_number;
            $selfpay->email = $request->email;
            $selfpay->profile_picture = $profile;

            $selfpay->save();

            return CustomHttpResponse::HttpReponse('Client register', '', 200);

        } catch (Exception $exception) {
            return CustomHttpResponse::HttpReponse('Error', $exception->getMessage(), 500);
        }
    }

}
