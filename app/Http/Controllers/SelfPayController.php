<?php

namespace App\Http\Controllers;

use App\Models\SelfPay;
use App\utils\UploadImage;
use Aws\S3\S3Client;
use http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Exception;

class SelfPayController extends Controller
{

    // Login para selfpay
    public function SelfPaySignIn(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $clienteExistente = SelfPay::where('phone_number', $request->phone_number)->exists();

            if ($clienteExistente) {
                $cliente = SelfPay::where('phone_number', $request->phone_number)->first();

                return \response()->json(['data' => $cliente], 200);
            }

            $selfpay = new SelfPay();

            $profile = UploadImage::UploadProfileImage($request->file('profile_picture'), $request->phone_number);

            $selfpay->name = $request->name;
            $selfpay->lastname = $request->lastname;
            $selfpay->phone_number = $request->phone_number;
            $selfpay->email = $request->email;
            $selfpay->profile_picture = $profile;

            $selfpay->save();

            return \response()->json(['message' => 'Cliente registrado'], 200);

        } catch (Exception $exception) {
            return \response()->json(['message' => $exception->getMessage()], 500);
        }
    }

    public function VerifyCode()
    {
        return \response()->json(['message' => 'Cliente registrado'], 200);
    }
}
