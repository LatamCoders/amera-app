<?php

namespace App\Services;

use App\Models\ReservationCode;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ReservationCodeService
{
    public function GenerateReservationCode($user_id)
    {
        $client = ReservationCode::where('selfpay_id', $user_id)->first();

        if (!$client) {
            $code = rand(10000000, 99999999);
            $rc = new ReservationCode();

            $rc->code = $code;
            $rc->selfpay_id = $user_id;

            $rc->save();
        } else {
            throw new BadRequestException('This user already has an active code');
        }
    }
}
