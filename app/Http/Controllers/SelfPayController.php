<?php

namespace App\Http\Controllers;

use App\Models\CreditCard;
use App\Models\SelfPay;
use App\utils\CustomHttpResponse;
use App\utils\UploadImage;
use Aws\S3\S3Client;
use http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Exception;
use Tymon\JWTAuth\JWTAuth;

class SelfPayController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:selfpay', ['except' => ['UserLogin', 'SelfPaySignIn', 'TestEncipt']]);
    }

    /*
     * Login para selfpay
     */
    public function SelfPaySignIn(Request $request): JsonResponse
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
    public function UserLogin(Request $request, JWTAuth $auth): JsonResponse
    {
        try {
            $cliente = SelfPay::where('phone_number', $request->phone_number)->first();

            if (!$cliente) {
                return CustomHttpResponse::HttpReponse('User not found', $cliente, 404);
            }

            $token = $auth->fromUser($cliente);

            return CustomHttpResponse::HttpReponse('OK', $this->RespondWithToken($token, $cliente), 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpReponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Actualizar datos de perfil
     */
    public function UpdateProfileData(Request $request, $clientId): JsonResponse
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
    public function UpdateProfileImage(Request $request, $clientId): JsonResponse
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
    public function getClientData($clientId): JsonResponse
    {

        try {
            $cliente = SelfPay::where('client_id', $clientId)->first();

            return CustomHttpResponse::HttpReponse('OK', $cliente, 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpReponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Cerrar sesión
     */
    public function LogOut(): JsonResponse
    {
        try {
            Auth::guard('selfpay')->logout(true);

            return CustomHttpResponse::HttpReponse('Client logout successfully', '', 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpReponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Retornar el token JWT
     */
    protected function RespondWithToken($token, $client): array
    {
        return [
            'client' => $client,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }

    /*
     * Agregar tarjeta de credito
     */
    public function AddCreditCard(Request $request, $clientId)
    {
        try {
            $client = SelfPay::where('client_id', $clientId)->first();

            $credit_card = new CreditCard();

            $credit_card->name = $request->name;
            $credit_card->number = $request->number;
            $credit_card->ccv = $request->ccv;
            $credit_card->date = $request->date;
            $credit_card->selfpay_id = $client->id;

            return CustomHttpResponse::HttpReponse('Credit card add successfully', '', 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpReponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Test encrupt
     */
    public function TestEncipt()
    {
        try {
            return CustomHttpResponse::HttpReponse('Credit card add successfully', Crypt::encryptString('hola'), 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpReponse('Error', $exception->getMessage(), 500);
        }
    }
}
