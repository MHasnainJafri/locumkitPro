<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\JobPost;
use App\Models\JobPostTimeline;
use Carbon\Carbon;
use App\Jobs\CronJobTimeline;

class JobTimeLineUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:job-time-line-update';

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
        $jobs = JobPost::where('job_status', 1)->where('job_date', '>=', $today)->get();
        foreach($jobs as $key => $item)
        {
            $timeline = $item->job_post_timelines;
            if($timeline)
            {
                CronJobTimeline::dispatch();
                Log::info('$item->job_post_timelines');
            }
        }
        $post_time = JobPostTimeline::all();
    }
}
