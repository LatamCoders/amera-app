<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\utils\CustomHttpResponse;
use App\utils\UploadImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Util\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tymon\JWTAuth\JWTAuth;

class DriverController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:driver', ['except' => ['DriverLogin', 'DriverSignIn', 'TestImages']]);
    }

    /*
     * Driver SingIn
     */
    public function DriverSingIn(Request $request): JsonResponse
    {
        try {
            $dirverExistente = Driver::where('phone_number', $request->phone_number)->exists();

            if ($dirverExistente) {
                return CustomHttpResponse::HttpResponse('Driver exist', '', 200);
            }

            $selfpay = new Driver();

            $driverId = 'DV' . rand(100, 9999);

            $selfpay->dirver_id = $driverId;
            $selfpay->name = $request->name;
            $selfpay->lastname = $request->lastname;
            $selfpay->phone_number = $request->phone_number;
            $selfpay->email = $request->email;
            $selfpay->profile_picture = UploadImage::UploadProfileImage($request->file('profile_picture'), $driverId);

            $selfpay->save();

            return CustomHttpResponse::HttpResponse('Driver register', '', 200);

        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function TestImages(Request $request): JsonResponse
    {
        $files = $request->file('driver_files');
        try {
            return CustomHttpResponse::HttpResponse('Driver register', '', 200);

        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Actualizar datos de perfil
     */
    public function UpdateProfileData(Request $request, $driverId): JsonResponse
    {
        try {
            $driver = Driver::where('driver_id', $driverId)->first();

            $driver->name = $request->name;
            $driver->lastname = $request->lastname;
            $driver->gender = $request->gender;
            $driver->birthday = $request->birthday;
            $driver->email = $request->email;
            $driver->address = $request->address;

            $driver->save();

            return CustomHttpResponse::HttpResponse('Driver update', null, 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Actualizar imagen de perfil
     */
    public function UpdateProfileImage(Request $request, $driverId): JsonResponse
    {
        try {
            $driver = Driver::where('driver_id', $driverId)->first();

            $profileImage = UploadImage::UploadProfileImage($request->file('profile_picture'), $driverId);

            $driver->profile_picture = $profileImage;

            $driver->save();

            return CustomHttpResponse::HttpResponse('Profile image updated', null, 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Cerrar sesión
     */
    public function LogOut(): JsonResponse
    {
        try {
            Auth::guard('driver')->logout(true);

            return CustomHttpResponse::HttpResponse('Driver logout successfully', '', 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Devolver datos de usuario logeado
     */
    public function DriverLogin(Request $request, JWTAuth $auth): JsonResponse
    {
        try {
            $driver = Driver::with('vehicle')->where('phone_number', $request->phone_number)->first();

            if (!$driver) {
                return CustomHttpResponse::HttpResponse('Driver not found', $driver, 404);
            }

            $token = $auth->fromUser($driver);

            return CustomHttpResponse::HttpResponse('OK', $this->RespondWithToken($token, $driver), 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Retornar el token JWT
     */
    protected function RespondWithToken($token, $driver): array
    {
        return [
            'client' => $driver,
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ];
    }

    /*
     * Obtener datos del driver
     */
    public function GetDriverData($driverId): JsonResponse
    {
        try {
            $driver = Driver::with('vehicle')->where('driver_id', $driverId)->first();

            return CustomHttpResponse::HttpResponse('OK', $driver, 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }
}
