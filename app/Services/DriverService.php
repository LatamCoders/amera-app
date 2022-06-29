<?php

namespace App\Services;

use App\Events\BookingNotification;
use App\Events\DriverTracking;
use App\Mail\RecoveryPassword;
use App\Mail\VerifyEmail;
use App\Models\Booking;
use App\Models\Driver;
use App\Models\SelfPay;
use App\utils\VerifyEmailService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Messaging\CloudMessage;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DriverService
{
    protected $_SmsService;
    protected $_Messaging;

    public function __construct(SmsService $SmsService, Messaging $messaging)
    {
        $this->_SmsService = $SmsService;
        $this->_Messaging = $messaging;
    }

    public function VerifyDriverNumberOrEmail($driverId, $verificationType, $request)
    {
        if ($verificationType == 'phone_number') {

            $data = Driver::where('driver_id', $driverId)->first();

            $data->phone_number_verified_at = Carbon::now();

            $data->save();
        } else if ($verificationType == 'email') {
            $data = Driver::where('driver_id', $driverId)->first();

            $code = Cache::get("VerifyEmail.$data->email");

            if ($code != (int)$request->code) {
                throw new BadRequestException("Invalid code");
            }

            $data->email_verified_at = Carbon::now();

            $data->save();

            Cache::forget("VerifyEmail.$data->email");
        } else {
            throw new BadRequestException('Invalid verification type');
        }
    }

    public function DriverRoute($bookingId, $lat, $long)
    {
        broadcast(new DriverTracking($bookingId, $lat, $long))->toOthers();
    }

    public function SelfPayNotifications($selfPayId, $message)
    {
        broadcast(new BookingNotification($selfPayId, $message));
    }

    public function UpdateDriverDocumentsImages($request, $driverId): string
    {
        switch ($request->query('fileType')) {
            case 'vehicleFrontImage':
                $vehicleFrontImage = "$driverId-vehicle-front-image.{$request->file('vehicle_front_image')->getClientOriginalExtension()}";
                Storage::disk('public')->delete("vehicle/$vehicleFrontImage");
                Storage::disk('public')->putFileAs('vehicle', $request->file('vehicle_front_image'), $vehicleFrontImage);

                return 'Vehicle front image updated';
            case 'vehicleRearImage':
                $vehicleRearImage = "$driverId-vehicle-rear-image.{$request->file('vehicle_rear_image')->getClientOriginalExtension()}";
                Storage::disk('public')->delete("vehicle/$vehicleRearImage");
                Storage::disk('public')->putFileAs('vehicle', $request->file('vehicle_rear_image'), $vehicleRearImage);

                return 'Vehicle rear image updated';
            case 'vehicleSideImage':
                $vehicleSideImage = "$driverId-vehicle-side-image.{$request->file('vehicle_side_image')->getClientOriginalExtension()}";
                Storage::disk('public')->delete("vehicle/$vehicleSideImage");
                Storage::disk('public')->putFileAs('vehicle', $request->file('vehicle_side_image'), $vehicleSideImage);

                return 'Vehicle side image updated';
            case 'vehicleInteriorImage':
                $vehicleInteriorImage = "$driverId-vehicle-interior-image.{$request->file('vehicle_interior_image')->getClientOriginalExtension()}";
                Storage::disk('public')->delete("vehicle/$vehicleInteriorImage");
                Storage::disk('public')->putFileAs('vehicle', $request->file('vehicle_interior_image'), $vehicleInteriorImage);

                return 'Vehicle interior image updated';
            case 'driverLicense':
                $DriverLicense = "$driverId-driver-license.{$request->file('driver_license')->getClientOriginalExtension()}";
                Storage::disk('public')->delete("vehicle/$DriverLicense");
                Storage::disk('public')->putFileAs('driver', $request->file('driver_license'), $DriverLicense);

                return 'driver licence updated';
            case 'proofOfInsurance':
                $ProofOfInsurance = "$driverId-proof-of-insurance.{$request->file('proof_of_insurance')->getClientOriginalExtension()}";
                Storage::disk('public')->delete("vehicle/$ProofOfInsurance");
                Storage::disk('public')->putFileAs('driver', $request->file('proof_of_insurance'), $ProofOfInsurance);

                return 'driver proof of insurance updated';
            default:
                throw new BadRequestException('Invalid option');
        }
    }

    public function SendVerificationEmailCode($clientId)
    {
        $client = Driver::where('driver_id', $clientId)->first();

        VerifyEmailService::SendCode($client->email, VerifyEmail::class, "VerifyEmail.$client->email");
    }

    public function SendDriverNotificationToClient($bookingId, $message, $title)
    {
        try {
            $client = Booking::with('SelfPay')->where('booking_id', $bookingId)->first();

            $message = CloudMessage::withTarget('token', $client->SelfPay->user_device_id)
                ->withNotification(['title' => $title, 'body' => $message])
                ->withData(['userId' => $client->id, 'notificationsId' => random_int(1000, 9999)]);

            $this->_Messaging->send($message);
        } catch (MessagingException|FirebaseException|\Exception $e) {
            throw new BadRequestException($e);
        }
    }
}
