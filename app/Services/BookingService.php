<?php

namespace App\Services;

use App\Models\Booking;
use App\utils\UniqueIdentifier;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class BookingService
{
    public function AddBooking($request, $clientId): bool
    {
        $booking = new Booking();

        $booking->booking_id = UniqueIdentifier::GenerateUid();
        $booking->selfpay_id = $clientId;
        $booking->booking_date = $request->booking_date;
        $booking->from = $request->from;
        $booking->to = $request->to;
        $booking->driver_id = $request->driver_id;
        $booking->status = 0;

        $booking->save();

        return true;
    }

    public function Start($bookingId)
    {
        $booking = Booking::where('booking_id', $bookingId)->first();

        if ($booking->driver_id == null) {
            throw new BadRequestException('Driver not assigned');
        } else if ($booking->status == 2) {
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
}