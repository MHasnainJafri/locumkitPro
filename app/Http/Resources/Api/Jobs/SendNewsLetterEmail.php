<?php

namespace App\Jobs;

use App\Mail\SendNewsLetterEmail as MailSendNewsLetterEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendNewsLetterEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emails, $subject, $message;
    
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($emails, $subject, $message) 
    {
        $this->emails = $emails; 
        $this->subject = $subject;
        $this->message = $message;
    }
  
    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->emails)->send(new MailSendNewsLetterEmail($this->subject, $this->message));
    }
    
}
