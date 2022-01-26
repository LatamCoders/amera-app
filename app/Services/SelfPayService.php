<?php

namespace App\Services;

use App\Models\SelfPay;
use App\utils\CustomHttpResponse;
use App\utils\UploadImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class SelfPayService
{
    public function SelfPaySignIn(Request $request, $phoneVerify)
    {
            $clienteExistente = SelfPay::where('phone_number', $request->phone_number)->exists();

            if ($clienteExistente) {
                return CustomHttpResponse::HttpResponse('Client exist', '', 200);
            }

            $selfpay = new SelfPay();

            $selfPayId = 'SP' . rand(100, 9999);

            $selfpay->client_id = $selfPayId;
            $selfpay->name = $request->name;
            $selfpay->lastname = $request->lastname;
            $selfpay->phone_number = $request->phone_number;
            $selfpay->email = $request->email;
            $selfpay->profile_picture = UploadImage::UploadProfileImage($request->file('profile_picture'), $selfPayId);
            $selfpay->ca_id = $request->ca_id;
            $selfpay->phone_number_verified_at = $phoneVerify;

            $selfpay->save();

            return 'Client register';
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
