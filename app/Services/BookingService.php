<?php

namespace App\Services;

use App\Models\Booking;
use App\utils\UniqueIdentifier;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class BookingService
{
    public function AddBooking($request, $clientId)
    {
        $booking = new Booking();

        $from = (object) ['from' => $request->from, 'coordinate' => $request->from_coordinates];
        $to = (object) ['from' => $request->to, 'coordinate' => $request->to_coordinates];

        $booking->booking_id = UniqueIdentifier::GenerateUid();
        $booking->selfpay_id = $clientId;
        $booking->booking_date = $request->booking_date;
        $booking->pickup_time = $request->pickup_time;
        $booking->city = $request->city;
        $booking->surgery_type = $request->surgery_type;
        $booking->appoinment_datetime = $request->appoinment_datetime;
        $booking->from = json_encode($from);
        $booking->to = json_encode($to);
        $booking->driver_id = $request->driver_id;
        $booking->status = 0;

        $booking->save();

        return $booking->id;
    }

    public function Start($bookingId)
    {
        $booking = Booking::where('booking_id', $bookingId)->first();

        if ($booking->driver_id == null) {
            throw new BadRequestException('Driver not assigned');
        } else if ($booking->status == 3) {
            throw new BadRequestException('Trip cancelled');
        } else if ($booking->trip_start != null) {
            throw new BadRequestException('Trip already start');
        }


        $booking->trip_start = Carbon::now();

        $booking->save();
    }

    public function End($bookingId)
    {
        $booking = Booking::where('booking_id', $bookingId)->first();

        if ($booking->trip_start == null) {
            throw new BadRequestException('The trip has not started');
        }

        $booking->trip_end = Carbon::now();
        $booking->status = 1;

        $booking->save();
    }

    public function GetBookingList($status)
    {
        return Booking::with('SelfPay', 'Driver', 'AdditionalService')->where('status', $status)->get();
    }

    /*
     * Listado de reservar para selfpay o driver
     */
    public function BookingList($clientId, $type)
    {
        if ($type == 'selfpay') {
            return Booking::with('AdditionalService', 'Driver')->where('selfpay_id', $clientId)->get();
        } else if ($type == 'driver') {
            return Booking::with('SelfPay', 'AdditionalService')->where('driver_id', $clientId)->get();
        } else {
            throw new BadRequestException('Invalid option');
        }
    }
}
