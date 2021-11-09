<?php

namespace App\Http\Controllers;

use App\Models\SelfPay;
use App\utils\CustomHttpResponse;
use App\utils\UploadImage;
use Aws\S3\S3Client;
use http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Exception;
use Tymon\JWTAuth\JWTAuth;

class SelfPayController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:selfpay', ['except' => ['UserLogin', 'SelfPaySignIn']]);
    }

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
     * Devolver datos de usuario logeado
     */
    public function UserLogin(Request $request, JWTAuth $auth): \Illuminate\Http\JsonResponse
    {
        try {
            $cliente = SelfPay::where('phone_number', $request->phone_number)->first();

            $token = $auth->fromUser($cliente);

            if (!$cliente) {
                return CustomHttpResponse::HttpReponse('User not found', $cliente, 404);
            } else if (!$token) {
                return CustomHttpResponse::HttpReponse('Unauthorized', null, 401);
            }

            return CustomHttpResponse::HttpReponse('OK', $this->RespondWithToken($token, $cliente), 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpReponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Actualizar datos de perfil
     */
    public function UpdateProfileData(Request $request, $clientId): \Illuminate\Http\JsonResponse
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
    public function UpdateProfileImage(Request $request, $clientId): \Illuminate\Http\JsonResponse
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

    /*
     * Obtener datos de cliente selfpay
     */
    public function getClientData($clientId): \Illuminate\Http\JsonResponse
    {
        try {
            $cliente = SelfPay::where('client_id', $clientId)->first();

            return CustomHttpResponse::HttpReponse('OK', $cliente, 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpReponse('Error', $exception->getMessage(), 500);
        }
    }

    public function login()
    {

    }

    public function LogOut(): \Illuminate\Http\JsonResponse
    {
        try {
            Auth::guard('selfpay')->logout(true);

            return CustomHttpResponse::HttpReponse('Client logout successfully', '', 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpReponse('Error', $exception->getMessage(), 500);
        }
    }

    protected function RespondWithToken($token, $client): array
    {
        return [
            'client' => $client,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }
}
