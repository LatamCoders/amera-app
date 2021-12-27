<?php

namespace App\Services;

use App\Models\Booking;
use App\utils\UniqueIdentifier;
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
        $booking->status = $request->status;

        $booking->save();

        return true;
    }

    public function Start($request, $bookingId)
    {
        $booking = Booking::where('booking_id', $bookingId)->first();

        if ($booking->booking_id === $bookingId) {
            throw new BadRequestException('Booking already exist');
        }

        $booking->trip_start = $request->trip_start;

        $booking->save();
    }
}
