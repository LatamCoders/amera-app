<?php

namespace App\Services;

use App\Models\SelfPay;
use App\utils\CustomHttpResponse;
use App\utils\UploadImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPUnit\Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class SelfPayService
{
    public function SelfPaySignIn(Request $request): JsonResponse
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

            return CustomHttpResponse::HttpResponse('Client register', '', 200);

        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function VerifyClientNumberOrEmail($selfpayId, $verificationType)
    {
        if ($verificationType == 'phone_number') {

            $data = SelfPay::where('selfpay_id', $selfpayId)->first();

            $data->phone_number_verified_at = Carbon::now();

            $data->save();
        } else if ($verificationType == 'email') {
            $data = SelfPay::where('selfpay_id', $selfpayId)->first();

            $data->email_verified_at = Carbon::now();

            $data->save();
        } else {
            throw new BadRequestException('Invalid verification type');
        }
    }

}
