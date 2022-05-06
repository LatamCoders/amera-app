<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CorporateAccountActivated extends Mailable
{
    use Queueable, SerializesModels;

    public $NAME;
    public $PASSWORD;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($NAME, $PASSWORD)
    {
        //
        $this->NAME = $NAME;
        $this->PASSWORD = $PASSWORD;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('Mail.CorporateAccountActivated');
    }
}
