<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Mail\SendEmailManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;



class SendMailJobManager implements ShouldQueue
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
        Log::channel('queue-worker')->info('sendmailjobmanager is working');
        Mail::to($this->emails)->send(new SendEmailManager($this->subject, $this->message));
    }
}
