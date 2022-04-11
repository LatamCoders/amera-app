<?php

namespace App\Services;

use App\Events\BookingNotification;
use App\Models\Booking;
use App\Models\SelfPay;
use App\Notifications\StartTrip;
use App\utils\StatusCodes;
use App\utils\UniqueIdentifier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class BookingService
{
    public function AddBooking($request, $clientId)
    {
        $booking = new Booking();

        $from = (object)['from' => $request->from, 'coordinate' => $request->from_coordinates];
        $to = (object)['from' => $request->to, 'coordinate' => $request->to_coordinates];

        $booking->booking_id = UniqueIdentifier::GenerateUid();
        $booking->selfpay_id = $clientId;
        $booking->booking_date = $request->booking_date;
        $booking->pickup_time = $request->pickup_time;
        $booking->city = $request->city;
        $booking->surgery_type = $request->surgery_type;
        $booking->appoinment_datetime = $request->appoinment_datetime;
        $booking->from = json_encode($from);
        $booking->to = json_encode($to);
        $booking->trip_distance = $request->trip_distance;
        $booking->price = $request->price;
        $booking->driver_id = $request->driver_id;
        $booking->status = StatusCodes::TRIP_PENDING;

        $booking->save();

        return $booking->id;
    }

    public function Start($bookingId, $message)
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
        $booking->status = StatusCodes::IN_PROGRESS;

        $booking->save();

        broadcast(new BookingNotification($booking->selfpay_id, $message))->toOthers();
    }

    public function End($bookingId, $message)
    {
        $booking = Booking::where('booking_id', $bookingId)->first();

        if ($booking->trip_start == null) {
            throw new BadRequestException('The trip has not started');
        }

        $booking->trip_end = Carbon::now();
        $booking->status = StatusCodes::COMPLETED;

        $booking->save();

        broadcast(new BookingNotification($booking->selfpay_id, $message))->toOthers();
    }

    public function GetBookingList($status)
    {
        return Booking::with('SelfPay', 'Driver', 'AdditionalService')->where('status', $status)->get();
    }

    /*
     * Obtener la información de una reserva
     */
    public function GetBookingData($bookingId)
    {
        return Booking::with('SelfPay', 'Driver', 'AdditionalService')->where('id', $bookingId)->first();
    }

    /*
     * Obtener reservas del CA
     */
    public function GetBookingCaData($caId)
    {
        $newBookingList = [];

        $booking = Booking::with('SelfPay', 'Driver', 'AdditionalService')->get();

        foreach ($booking as $items) {
            if ($items->selfpay->ca_id == $caId) {
                $newBookingList[] = $items;
            }
        }

        return $newBookingList;
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

    /*
     * obtener reservar especifica para selfpay o driver
     */
    public function ShowOneBooking($clientId, $bookingId, $type)
    {
        if ($type == 'selfpay') {
            return Booking::with('AdditionalService', 'Driver', 'Driver.vehicle')->where('selfpay_id', $clientId)
                ->where('id', $bookingId)->first();
        } else if ($type == 'driver') {
            return Booking::with('SelfPay', 'AdditionalService')->where('driver_id', $clientId)
                ->where('id', $bookingId)->first();
        } else {
            throw new BadRequestException('Invalid option');
        }
    }

    /*
     * Pedir cancelar un booking
     */
    public function RequestCancelBooking($bookingId)
    {
        $booking = Booking::were('booking_id', $bookingId)->first();

        $booking->status = StatusCodes::CANCELLATION_PENDING;

        $booking->save();
    }

    /*
     * Devolucion de dinero al Selfpay
     */
    public function RefundCard($refundId)
    {
        $stripe = new \Stripe\StripeClient(
            env('STRIPE_KEY')
        );
        $stripe->refunds->create([
            'charge' => $refundId,
        ]);
    }

    /*
     * cancelar un booking
     */
    public function ApproveCancellationBooking($bookingId)
    {
        $booking = Booking::were('booking_id', $bookingId)->first();

        if ($booking->status != StatusCodes::CANCELLATION_PENDING) {
            throw new BadRequestException('This booking is not pending for cancellation');
        }

       $response = Http::withToken(env('STRIPE_KEY'))->post("https://api.stripe.com/v1/charges/$booking->charge_id/refunds");

        if ($response->status() != 200) {
            throw new BadRequestException($response['error']['message']);
        }

        $booking->status = StatusCodes::CANCELLED;
        $booking->refaund = true;

        $booking->save();
    }
}
