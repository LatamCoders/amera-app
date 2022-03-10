<?php

use App\Models\Booking;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int)$user->id === (int)$id;
});

Broadcast::channel('testing.{bookingId}', function ($bookingId) {
    return true;
});

Broadcast::channel('booking.{bookingId}', function ($user, $bookingId) {
    $booking = Booking::where('id', $bookingId)->first();

    if ((int) $booking->selfpay_id == (int) $user->id || (int) $booking->driver_id == (int) $user->id) {
        return true;
    } else {
        return false;
    }
});
