<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\JobAction;
use Illuminate\Console\Command;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class PreviousDaySendNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:previous-day-send-notifications';

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
        $jobs = jobAction::where('action', 3)->get();

        foreach ($jobs as $job) {
            $completionDate = Carbon::parse($job->jobposting->job_date);
            $reminderDate = $completionDate->subDay();
            $today = Carbon::now()->toDateString();
            if ($today == $reminderDate) {
                NotificationService::sendNotification($job->jobposting->id, $job->freelancer_id, "Hey tomorrow you have work", $reminderDate);
                Log::info('Previous Info Notification is send');
            }
        }
    }
}
