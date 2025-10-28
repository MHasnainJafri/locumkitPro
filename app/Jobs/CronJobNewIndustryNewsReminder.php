<?php

namespace App\Jobs;

use App\Models\SubscribeUser;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewNewsLetter;
use Symfony\Component\Mime\Part\TextPart;
use Symfony\Component\Mime\Part\HtmlPart;
use Symfony\Component\Mime\Part\Multipart\AlternativePart;
use Illuminate\Support\Facades\Log;


class CronJobNewIndustryNewsReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $news;

    /**
     * Create a new job instance.
     */
    public function __construct($news)
    {
        $this->news = $news;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::channel('queue-worker')->info('CronJobNewIndustryNewsReminder is working');
        // Fetch all subscribed users
        $subscribedUsers = SubscribeUser::all();

            $news = $this->news;
        // Loop through each subscribed user and send an email
        foreach ($subscribedUsers as $user) {
            if (filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                try {
                    Mail::to($user->email)->send(new NewNewsLetter($news));
                } catch (\Exception $e) {
                    Log::channel('queue-worker')->error('Failed to send email to ' . $user->email . ': ' . $e->getMessage());
                }
            }
        }
    }

    /**
     * Send an email to the user with the latest news.
     *
     * @param string $email
     */


}

