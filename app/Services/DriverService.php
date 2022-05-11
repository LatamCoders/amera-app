<?php

namespace App\Services;

use App\Events\BookingNotification;
use App\Events\DriverTracking;
use App\Mail\RecoveryPassword;
use App\Models\Driver;
use App\utils\VerifyEmailService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DriverService
{
    protected $_SmsService;

    public function __construct(SmsService $SmsService)
    {
        $this->_SmsService = $SmsService;
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

    public function SendVerificationEmailCode($clientId)
    {
        $client = Driver::where('driver_id', $clientId)->first();

        VerifyEmailService::SendCode($client->email, RecoveryPassword::class, "VerifyEmail.$client->email");
    }
}
