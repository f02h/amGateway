<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class Mailer extends Mailable
{
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('mailer');
    }
}