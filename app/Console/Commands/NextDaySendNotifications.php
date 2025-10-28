<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\JobAction;
use App\Models\User;
use Illuminate\Console\Command;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;

class NextDaySendNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:next-day-send-notifications';

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
        $jobs = jobAction::where('action', 4)->get();
        foreach ($jobs as $job) {
            $completionDate = Carbon::parse($job->jobposting->job_date);
            $feedbackDate = $completionDate->addDay();
            $today = Carbon::now()->toDateString();
          if($today==$feedbackDate)
          {

          }

        }

    }
}
