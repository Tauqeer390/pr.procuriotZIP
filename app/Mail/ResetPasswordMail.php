<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResetPasswordMail extends Mailable
{
    use Queueable, SerializesModels;
        public $user;
        public $pin;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user,$pin)
    {
        $this->user = $user;
        $this->pin = $pin;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $pin = $this->pin;
        return $this->view('forgotPassword',compact('pin'));
    }
}
