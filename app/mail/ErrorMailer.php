<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class ErrorMailer extends Mailable
{
    /**
     * Build the message.
     *
     * @return $this
     */

    public $gateway;
    public $error;

    public function __construct($gateway, $error)
    {
        $this->gateway = $gateway;
        $this->error = $error;
    }

    public function build()
    {
        return $this->view(
            'error_mailer',
            ['gateway' => $this->gateway, 'error' => $this->error]
        );
    }
}