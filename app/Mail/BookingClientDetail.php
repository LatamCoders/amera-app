<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingClientDetail extends Mailable
{
    use Queueable, SerializesModels;

    public $NAME;
    public $LAST_NAME;
    public $PICKUP_TIME;
    public $SURGERY_TYPE;
    public $APPOINMENT_DATETIME;
    public $FROM;
    public $TO;
    public $PRICE;
    public $PICKUP_TIME_SUBJECT;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($NAME, $LAST_NAME, $PICKUP_TIME, $SURGERY_TYPE, $APPOINMENT_DATETIME, $FROM, $TO, $PRICE)
    {
        //
        $this->NAME = $NAME;
        $this->PICKUP_TIME = $PICKUP_TIME;
        $this->SURGERY_TYPE = $SURGERY_TYPE;
        $this->APPOINMENT_DATETIME = $APPOINMENT_DATETIME;
        $this->FROM = $FROM;
        $this->TO = $TO;
        $this->PRICE = $PRICE;
        $this->PICKUP_TIME_SUBJECT = Carbon::parse($this->PICKUP_TIME)->format('d/m/y h:i A');
        $this->LAST_NAME = $LAST_NAME;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('Mail.BookingClientDetail')->subject("Booking confirmation - $this->NAME - Pickup $this->PICKUP_TIME_SUBJECT");
    }
}
