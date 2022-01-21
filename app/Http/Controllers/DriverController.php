<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\SelfPay;
use App\Models\Vehicle;
use App\Services\DriverService;
use App\Services\ExperienceService;
use App\Services\SmsService;
use App\utils\CustomHttpResponse;
use App\utils\UploadFiles;
use App\utils\UploadImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Util\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tymon\JWTAuth\JWTAuth;

class DriverController extends Controller
{
    protected $_ExperienceService;
    protected $_SmsService;
    protected $_DriverService;

    public function __construct(ExperienceService $experienceService, SmsService $SmsService, DriverService $DriverService)
    {
        $this->middleware('auth:driver', ['except' => ['DriverLogin', 'DriverSignUp', 'SendSmsCode']]);
        $this->_ExperienceService = $experienceService;
        $this->_SmsService = $SmsService;
        $this->_DriverService = $DriverService;
    }

    /*
     * Driver SingIn
     */
    public function DriverSignUp(Request $request): JsonResponse
    {
        try {
            $dirverExistente = Driver::where('phone_number', $request->phone_number)->exists();

            if ($dirverExistente) {
                return CustomHttpResponse::HttpResponse('Driver exist', '', 200);
            }

            $driver = new Driver();

            $driverId = 'DV' . rand(1000, 99999);

            $driver->driver_id = $driverId;
            $driver->name = $request->name;
            $driver->lastname = $request->lastname;
            $driver->phone_number = $request->phone_number;
            $driver->email = $request->email;
            $driver->profile_picture = UploadImage::UploadProfileImage($request->file('profile_picture'), $driverId);

            if ($driver->save()) {
                $this->RegisterVehicleAndDocuments($request, $driver->id, $driverId);

                UploadFiles::UploadDriverFile($request, $driverId, $driver->id);

                return CustomHttpResponse::HttpResponse('Driver register', '', 200);
            }

            return CustomHttpResponse::HttpResponse('Error', '', 500);

        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Registrar vehiculo y documentos
     */
    public function RegisterVehicleAndDocuments($request, $driverId, $number)
    {
        $vehicle = new Vehicle();

        $vehicle->model = $request->model;
        $vehicle->color = $request->color;
        $vehicle->year = $request->year;
        $vehicle->plate_number = $request->plate_number;
        $vehicle->vin_number = $request->vin_number;
        $vehicle->driver_id = $driverId;

        if ($vehicle->save()) {
            UploadFiles::UploadVehicleFile($request, $number, $vehicle->id);
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
            $driver = Driver::with('vehicle', 'driverdocuments', 'vehicle.vehicledocuments')->where('phone_number', $request->phone_number)->first();

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
            $driver = Driver::with('driverdocuments', 'vehicle', 'vehicle.vehicledocuments')->where('driver_id', $driverId)->first();

            return CustomHttpResponse::HttpResponse('OK', $driver, 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Puntuar Cliente
     */
    public function RateSelfPay(Request $request, $booking, $selfPayId, $driverId): JsonResponse
    {
        try {
            $rate = new SelfPay();

            $rate->rate = $request->rate;
            $rate->comments = $request->comments;
            $rate->driver_id = $driverId;
            $rate->selfpay_id = $selfPayId;
            $rate->booking_id = $booking;

            $rate->save();

            return CustomHttpResponse::HttpResponse('OK', '', 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    public function DriverRateAmeraExperience(Request $request, $bookingId, $driverId): \Illuminate\Http\JsonResponse
    {
        try {
            $this->_ExperienceService->RateAmera($request, $bookingId, $driverId, null);

            return CustomHttpResponse::HttpResponse('OK', '', 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Enviar codigo SMS
     */
    public function SendSmsCode(Request $request): JsonResponse
    {
        try {
            $resp = $this->_SmsService->SendSmsCode($request->number);

            return CustomHttpResponse::HttpResponse($resp['message'], $resp['data'], 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Verificar numero o email
     */
    public function VerifyEmailOrNumber(Request $request, $driverId): JsonResponse
    {
        try {
            $this->_DriverService->VerifyDriverNumberOrEmail($driverId, $request->query('type'));

            return CustomHttpResponse::HttpResponse('Ok', '', 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }
}
