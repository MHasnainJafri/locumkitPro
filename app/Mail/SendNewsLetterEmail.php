<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SendNewsLetterEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $subject, $message;
  
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject, $message) 
    {
        $this->subject = $subject;
        $this->message = $message;
    }  
    /**
     * Build the message.
     *
     * @return $this
     */

     public function build()
    {
        return $this->subject($this->subject)
                ->html($this->message)
                ->view('mail.NewsLetterEmail')->with(['message' => $this->message]);
    }
}
