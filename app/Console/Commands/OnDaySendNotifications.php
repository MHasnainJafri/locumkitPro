<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\JobAction;
use Illuminate\Console\Command;
use App\Models\SendNotification;
use Illuminate\Support\Facades\Log;
use App\Services\NotificationService;


class OnDaySendNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:on-day-send-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $jobs = JobAction::where('action', '3')->get();
        Log::info('On Day Notifications are workig');
        Log::info('No jobs', ['jobs' => $jobs]);
        foreach ($jobs as $job) {
            $completionDate = Carbon::parse($job->jobposting->job_date);

            NotificationService::sendNotification($job->jobposting->id, $job->freelancer_id, "Have you arrived at work...?", $completionDate);
            Log::info('here in foreach');
            $today = Carbon::now()->toDateString();
            if ($today == $completionDate) {
                SendNotification::create([
                    'job_post_id' => $job->jobposting->id,
                    'recipient_id' => $job->freelancer_id,
                    'message' => "Have you arrived at work...?",
                    'send_date' => $completionDate,
                ]);
            }
        }
        $next_job = SendNotification::where('status', '1')->get();
        Log::info($next_job . ' jobs to done is there');
        foreach ($next_job as $job) {
            Log::info('status 11111111111111');
            Log::info($job->freelancer_id. "recipt_id id");
            NotificationService::sendNotification($job->jobposting->id, $job->freelancer_id ?? '0', "Have you arrived at work...?", $completionDate);
            Log::info('On Day Notification is working.');
        }
    }
}
