<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApproveMail extends Mailable
{
    public $name;
    public $chances;

    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @param $name
     * @param $chances
     */
    public function __construct($name, $chances)
    {
        $this->name = $name;
        $this->chances = $chances;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('mails/approve')->subject('Ваше участие одобрено');
    }
}
