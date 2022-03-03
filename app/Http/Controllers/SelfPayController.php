<?php

namespace App\Http\Controllers;

use App\Models\CreditCard;
use App\Models\DriverRate;
use App\Models\ReservationCode;
use App\Models\SelfPay;
use App\Models\SelfPayRate;
use App\Services\AdditionalServicesService;
use App\Services\BookingService;
use App\Services\ExperienceService;
use App\Services\SelfPayService;
use App\Services\SmsService;
use App\utils\CustomHttpResponse;
use App\utils\UploadImage;
use Aws\S3\S3Client;
use Carbon\Carbon;
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
    protected $_SelfPayService;
    protected $_ExperienceService;
    protected $_BookingService;
    protected $_AdditionalServicesService;
    protected $_SmsService;

    public function __construct(
        SelfPayService            $selfPayService,
        ExperienceService         $experienceService,
        BookingService            $bookingService,
        AdditionalServicesService $AdditionalServicesService,
        SmsService                $SmsService
    )
    {
        $this->middleware('auth:selfpay', ['except' => ['UserLogin', 'SelfPaySignIn', 'SendSmsCode', 'ActivateVerificationCode']]);
        $this->_SelfPayService = $selfPayService;
        $this->_ExperienceService = $experienceService;
        $this->_BookingService = $bookingService;
        $this->_AdditionalServicesService = $AdditionalServicesService;
        $this->_SmsService = $SmsService;
    }

    /*
     * Login para selfpay
     */
    public function SelfPaySignIn(Request $request): JsonResponse
    {
        try {

            $response = $this->_SelfPayService->SelfPaySignIn($request, Carbon::now());

            return CustomHttpResponse::HttpResponse($response, '', 200);

        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
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
                return CustomHttpResponse::HttpResponse('User not found', $cliente, 404);
            }

            $token = $auth->fromUser($cliente);

            return CustomHttpResponse::HttpResponse('OK', $this->RespondWithToken($token, $cliente), 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Reservation code Login
     */
    public function ReservationCodeLogin(Request $request): JsonResponse
    {
        try {
            $code = $this->_SelfPayService->ReservationCode($request);

            return CustomHttpResponse::HttpResponse('OK', $code, 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
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
            $cliente->gender = $request->gender;
            $cliente->birthday = $request->birthday;
            $cliente->email = $request->email;
            $cliente->address = $request->address;

            $cliente->save();

            return CustomHttpResponse::HttpResponse('Client update', null, 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Actualizar imagen de perfil
     */
    public function UpdateProfileImage(Request $request, $clientId): JsonResponse
    {
        try {
            $cliente = SelfPay::where('client_id', $clientId)->first();

            $profileImage = UploadImage::UploadProfileImage($request->file('profile_picture'), $cliente->phone_number);

            $cliente->profile_picture = $profileImage;

            $cliente->save();

            return CustomHttpResponse::HttpResponse('Profile image updated', null, 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Obtener datos de cliente selfpay
     */
    public function getClientData($clientId): JsonResponse
    {
        try {
            $cliente = SelfPay::where('client_id', $clientId)->first();

            return CustomHttpResponse::HttpResponse('OK', $cliente, 200);
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
            Auth::guard('selfpay')->logout(true);

            return CustomHttpResponse::HttpResponse('Client logout successfully', '', 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
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
    public function AddCreditCard(Request $request, $clientId): JsonResponse
    {
        try {
            $client = SelfPay::where('client_id', $clientId)->first();

            $credit_card = new CreditCard();

            $credit_card->name = $request->name;
            $credit_card->number = $request->number;
            $credit_card->ccv = $request->ccv;
            $credit_card->date = $request->date;
            $credit_card->selfpay_id = $client->id;

            return CustomHttpResponse::HttpResponse('Credit card add successfully', '', 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Puntuar Driver
     */
    public function RateDriver(Request $request, $booking, $selfPayId, $driverId): JsonResponse
    {
        try {
            $rate = new DriverRate();

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

    /*
     * Obtener puntuación
     */
    public function GetClientRate($selfpayId): JsonResponse
    {
        try {
            $data = SelfPay::with('selfpayrate.booking', 'selfpayrate.driver')->where('id', $selfpayId)->first();

            return CustomHttpResponse::HttpResponse('OK', $data, 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Calificar a Amera
     */
    public function ClientRateAmeraExperience(Request $request, $bookingId, $selfPayId): JsonResponse
    {
        try {
            $this->_ExperienceService->RateAmera($request, $bookingId, null, $selfPayId);

            return CustomHttpResponse::HttpResponse('OK', '', 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Agregar reserva
     */
    public function AddReserve(Request $request, $clientId): JsonResponse
    {
        try {
           $res = $this->_BookingService->AddBooking($request, $clientId);

            return CustomHttpResponse::HttpResponse('Booking add successfully', ['booking_id' => $res], 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Iniciar viaje
     */
    public function StartOrEndTrip($bookingId, $tripAction): JsonResponse
    {
        try {
            if ($tripAction == 'start') {
                $this->_BookingService->Start($bookingId);

                return CustomHttpResponse::HttpResponse('Trip start', '', 200);
            }

            $this->_BookingService->End($bookingId);

            return CustomHttpResponse::HttpResponse('Trip End', '', 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Agregar servicios adicionales
     */
    public function AddAdditionalService(Request $request, $bookingId): JsonResponse
    {
        try {
            $this->_AdditionalServicesService->Add($request, $bookingId);

            return CustomHttpResponse::HttpResponse('Service added', '', 200);
        } catch (\Exception $exception) {
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
    public function VerifyEmailOrNumber(Request $request, $selfpayId): JsonResponse
    {
        try {
            $this->_SelfPayService->VerifyClientNumberOrEmail($selfpayId, $request->query('type'));

            return CustomHttpResponse::HttpResponse('Ok', '', 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Activar el verification code
     */
    public function ActivateVerificationCode($selfpayId): JsonResponse
    {
        try {
            $this->_SelfPayService->ActivateReservationCodeSP($selfpayId);

            return CustomHttpResponse::HttpResponse('Ok', '', 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * My bookings
     */
    public function MyBookings(Request $request, $selfpayId): JsonResponse
    {
        try {
            $res = $this->_BookingService->BookingList($selfpayId, $request->query('type'));

            return CustomHttpResponse::HttpResponse('Ok', $res, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * One booking
     */
    public function GetOneBooking(Request $request, $bookingId, $selfpayId): JsonResponse
    {
        try {
            $res = $this->_BookingService->ShowOneBooking($selfpayId, $bookingId, $request->query('type'));

            return CustomHttpResponse::HttpResponse('Ok', $res, 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Eliminar un servicio
     */
    public function DeleteOneService($bookingId, $serviceId): JsonResponse
    {
        try {
            $this->_AdditionalServicesService->DeleteService($serviceId, $bookingId);

            return CustomHttpResponse::HttpResponse('Service deleted', '', 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }

    /*
     * Modificar un servicio
     */
    public function ModifyOneService(Request $request, $bookingId, $serviceId): JsonResponse
    {
        try {
            $this->_AdditionalServicesService->ModifyServices($request, $serviceId, $bookingId);

            return CustomHttpResponse::HttpResponse('Service modified', '', 200);
        } catch (\Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }


    /*
     * Test encrypt
     */
    public function TestEncipt()
    {
        try {
            return CustomHttpResponse::HttpResponse('Credit card add successfully', Crypt::encryptString('hola'), 200);
        } catch (Exception $exception) {
            return CustomHttpResponse::HttpResponse('Error', $exception->getMessage(), 500);
        }
    }
}
