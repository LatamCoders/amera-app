<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BookingClientDetail extends Mailable
{
    use Queueable, SerializesModels;

    public $NAME;
    public $PICKUP_TIME;
    public $SURGERY_TYPE;
    public $APPOINMENT_DATETIME;
    public $FROM;
    public $TO;
    public $PRICE;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($NAME, $PICKUP_TIME, $SURGERY_TYPE, $APPOINMENT_DATETIME, $FROM, $TO, $PRICE)
    {
        //
        $this->NAME = $NAME;
        $this->PICKUP_TIME = $PICKUP_TIME;
        $this->SURGERY_TYPE = $SURGERY_TYPE;
        $this->APPOINMENT_DATETIME = $APPOINMENT_DATETIME;
        $this->FROM = $FROM;
        $this->TO = $TO;
        $this->PRICE = $PRICE;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('Mail.BookingClientDetail');
    }
}
