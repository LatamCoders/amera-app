<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReturnTimeChanged extends Mailable
{
    use Queueable, SerializesModels;

    public $NAME;
    public $RETURN_TIME;
    public $LAST_NAME;
    public $DRIVER_NAME;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($NAME, $LAST_NAME, $RETURN_TIME, $DRIVER_NAME = null)
    {
        $this->NAME = $NAME;
        $this->LAST_NAME = $LAST_NAME;
        $this->RETURN_TIME = $RETURN_TIME;
        $this->DRIVER_NAME = $DRIVER_NAME;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('Mail.ReturnTimeChanged')->subject("{$this->NAME} {$this->LAST_NAME} booking - Return time changed");
    }
}
