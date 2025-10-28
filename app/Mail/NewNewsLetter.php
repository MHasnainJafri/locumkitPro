<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewNewsLetter extends Mailable
{
    use Queueable, SerializesModels;

    protected $news;
    /**
     * Create a new message instance.
     */
    public function __construct($news)
    {
        $this->news= $news;
    }
    
    
       public function build()
    {
        return $this->subject('New Insdustry News')
            ->view('mail.news-letter')
            ->with(['news' => $this->news]);
    }

    
}
