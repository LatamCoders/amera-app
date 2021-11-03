<?php

namespace App\Http\Controllers;

use App\Models\SelfPay;
use App\utils\CustomHttpResponse;
use App\utils\UploadImage;
use Aws\S3\S3Client;
use http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Exception;

class SelfPayController extends Controller
{

    /*
     * Login para selfpay
     */
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

    /*
     * TEst
     */
    public function VerifyCode()
    {
        return \response()->json(['message' => 'Cliente registrado'], 200);
    }

    /*
     * Devolver datos de usuario logeado
     */
    public function UserLogin(Request $request)
    {
        try {
            $cliente = SelfPay::where('phone_number', $request->phone_number)->first();

            if (!$cliente) {
                return CustomHttpResponse::HttpReponse('User not found', $cliente, 404);
            }

            return CustomHttpResponse::HttpReponse('OK', $cliente, 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpReponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Actualizar datos de perfil
     */
    public function UpdateProfileData(Request $request, $clientId)
    {
        try {
            $cliente = SelfPay::where('client_id', $clientId)->first();

            $cliente->name = $request->name;
            $cliente->lastname = $request->lastname;
            $cliente->email = $request->email;
            $cliente->address = $request->address;

            $cliente->save();

            return CustomHttpResponse::HttpReponse('Client update', null, 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpReponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Actualizar imagen de perfil
     */
    public function UpdateProfileImage(Request $request, $clientId)
    {
        try {
            $cliente = SelfPay::where('client_id', $clientId)->first();

            $profileImage = UploadImage::UploadProfileImage($request->file('profile_picture'), $request->phone_number);

            $cliente->profile_picture = $profileImage;

            $cliente->save();

            return CustomHttpResponse::HttpReponse('Profile image update', null, 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpReponse('Error', $exception->getMessage(), 500);
        }
    }
}
