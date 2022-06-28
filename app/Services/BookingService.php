<?php

namespace App\Services;

use App\Events\BookingNotification;
use App\Mail\BookingClientDetail;
use App\Mail\RequestCancelBookingWithFee;
use App\Mail\RequestCancelBookingWithoutFee;
use App\Mail\ReturnTimeChanged;
use App\Models\Booking;
use App\Models\SelfPay;
use App\Notifications\StartTrip;
use App\utils\StatusCodes;
use App\utils\UniqueIdentifier;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Exception;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class BookingService
{
    public function AddBooking($request, $clientId)
    {
        try {
            DB::beginTransaction();

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
            $booking->facility_name = $request->facility_name;
            $booking->doctor_name = $request->doctor_name;
            $booking->facility_phone_number = $request->facility_phone_number;
            $booking->approximately_return_time = $request->approximately_return_time;
            $booking->price = $request->price;
            $booking->service_fee = $request->service_fee;
            $booking->driver_id = $request->driver_id;
            $booking->status = StatusCodes::TRIP_PENDING;

            $booking->save();

            if ($request->query('clientType') == null) {
                $client = SelfPay::where('id', $clientId)->first();

                Mail::to($client->email)->send(new BookingClientDetail($client->name, $client->lastname, $request->pickup_time, $request->surgery_type, $request->appoinment_datetime, $request->from, $request->to, $request->price));
            } else if ($request->query('clientType') == 'reservationCode') {
                $reservationCode = new ReservationCodeService();

                $reservationCode->GenerateReservationCode($clientId);
            }

            DB::commit();

            return $booking->id;
        } catch (\Exception $e) {
            DB::rollBack();

            throw new BadRequestException($e->getMessage());
        }
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
            return Booking::with('AdditionalService', 'Driver')->where('selfpay_id', $clientId)->orderByDesc('booking_date')->get();
        } else if ($type == 'driver') {
            return Booking::with('SelfPay', 'AdditionalService')->where('driver_id', $clientId)->orderByDesc('booking_date')->get();
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
        $booking = Booking::with('SelfPay')->where('id', $bookingId)->first();

        $hours = Carbon::create($booking->booking_date)->diffInHours(Carbon::now());

        if ($hours <= 23) {
            Mail::to($booking->SelfPay->email)->send(new RequestCancelBookingWithoutFee($booking->SelfPay->name));
        } else if ($hours >= 24) {
            Mail::to($booking->SelfPay->email)->send(new RequestCancelBookingWithFee($booking->SelfPay->name));
        }

        $booking->status = StatusCodes::CANCELLATION_PENDING;

        $booking->save();
    }

    public function UpdateBookingReturnTime($bookingId, $returnTime)
    {
        try {
            DB::beginTransaction();

            $booking = Booking::with('Driver', 'SelfPay')->where('id', $bookingId)->first();

            $booking->approximately_return_time = $returnTime;

            if ($booking->Driver != null) {
                Mail::to($booking->SelfPay->email)->send(new ReturnTimeChanged($booking->SelfPay->name, $booking->SelfPay->lastname, $booking->approximately_return_time));
                Mail::to($booking->Driver->email)->send(new ReturnTimeChanged($booking->SelfPay->name, $booking->SelfPay->lastname, $booking->approximately_return_time, $booking->Driver->name));
            } else {
                Mail::to($booking->SelfPay->email)->send(new ReturnTimeChanged($booking->SelfPay->name, $booking->SelfPay->lastname, $booking->approximately_return_time));
            }

            $booking->save();

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            return new BadRequestException($exception);
        }
    }
}
