<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DeclineMail extends Mailable
{
    public $name;
    public $reason;

    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param $name
     * @param $reason
     */
    public function __construct($name, $reason)
    {
        $this->name = $name;
        $this->reason = $reason;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails/decline')->subject('Ваше участие отклонено');
    }
}
