<?php

namespace App\Services;

use App\Models\Driver;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class DriverService
{
    protected $_SmsService;

    public function __construct(SmsService $SmsService)
    {
        $this->_SmsService = $SmsService;
    }

    public function VerifyDriverNumberOrEmail($driverId, $verificationType)
    {
        if ($verificationType == 'phone_number') {

            $data = Driver::where('driver_id', $driverId)->first();

            $data->phone_number_verified_at = Carbon::now();

            $data->save();
        } else if ($verificationType == 'email') {
            $data = Driver::where('driver_id', $driverId)->first();

            $data->email_verified_at = Carbon::now();

            $data->save();
        } else {
            throw new BadRequestException('Invalid verification type');
        }
    }
}
