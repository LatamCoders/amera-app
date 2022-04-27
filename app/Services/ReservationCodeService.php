<?php

namespace App\Services;

use App\Models\ReservationCode;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ReservationCodeService
{
    public function GenerateReservationCode($user_id)
    {
        DB::beginTransaction();

        $client = ReservationCode::with('SelfPay.Booking')->where('selfpay_id', $user_id)->first();

        if (!$client) {
            $code = rand(10000000, 99999999);
            $rc = new ReservationCode();

            $rc->code = $code;
            $rc->selfpay_id = $user_id;

            $rc->save();

            Mail::to('jose.b1996m@gmail.com')->send(new \App\Mail\ReservationCode($client->self_pay->name, $code));

            DB::commit();
        } else {
            DB::rollBack();
            throw new BadRequestException('This user already has an active code');
        }
    }
}
