<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\ReservationCode;
use App\Models\SelfPay;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class ReservationCodeService
{
    public function GenerateReservationCode($user_id)
    {
        DB::beginTransaction();

        $client = ReservationCode::with('SelfPay')->where('selfpay_id', $user_id)->first();
        $bookingDate = Booking::where('selfpay_id', $user_id)->first();
        $selfPay = SelfPay::where('id', $user_id)->first();

        if (!$client) {
            $code = rand(10000000, 99999999);
            $rc = new ReservationCode();

            $rc->code = $code;
            $rc->selfpay_id = $user_id;

            $rc->save();

            Mail::to($selfPay->email)->send(new \App\Mail\ReservationCode($selfPay->name, $code, $bookingDate->appoiment_datetime));

            DB::commit();
        } else {
            DB::rollBack();
            throw new BadRequestException('This user already has an active code');
        }
    }
}
