<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationCode extends Mailable
{
    use Queueable, SerializesModels;

    public $NAME;
    public $CODE;
    public $DATE_TIME;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($name, $code, $datetime)
    {
        $this->NAME = $name;
        $this->CODE = $code;
        $this->DATE_TIME = $datetime;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('Mail.reservationCode')
            ->with([
                'name' => $this->NAME,
                'code' => $this->CODE,
                'datetime' => $this->DATE_TIME,
            ]);
    }
}
