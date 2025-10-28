<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\JobPost;
use App\Models\JobPostTimeline;
use Carbon\Carbon;
use App\Jobs\CronJobTimeline;
use App\Models\LocumlogbookFollowupProcedure;
use App\Notifications\LocumlogbookNotification;

class Locumlogbook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:locumlogbook-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'updating the job rate which are not accpeted by the users';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today();
        $this->info($today->format('Y-m-d'));
        $jobs = LocumlogbookFollowupProcedure::
        whereDate('reminder_datetime', $today)->
        get();
        $this->info('Locumlogbook reminder job is working fine');
        foreach ($jobs as $job) {
            $this->info($job);
            //send notification to the user
            // LocumlogbookNotification
            $job->user->notify(new LocumlogbookNotification ($job));
        }
        
    }
}
