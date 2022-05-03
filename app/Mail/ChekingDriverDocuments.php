<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ChekingDriverDocuments extends Mailable
{
    use Queueable, SerializesModels;

    public $NAME;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($NAME)
    {
        //
        $this->NAME = $NAME;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('Mail.ChekingDriverDocuments');
    }
}
