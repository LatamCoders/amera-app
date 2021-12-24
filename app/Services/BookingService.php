<?php

namespace App\Services;

use App\Models\Booking;

class BookingService
{
    public function AddBooking($request, $clientId): bool
    {
        $booking = new Booking();

        $booking->selfpay_id = $clientId;
        $booking->booking_date = $request->booking_date;
        $booking->from = $request->from;
        $booking->to = $request->to;
        $booking->trip_start = $request->trip_start;
        $booking->trip_end = $request->trip_end;
        $booking->driver_id = $request->driver_id;
        $booking->status = $request->status;

        $booking->save();

        return true;
    }
}
