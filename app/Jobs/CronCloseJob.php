<?php

namespace App\Jobs;

use App\Helpers\JobMailHelper;
use App\Models\JobPost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;


class CronCloseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::channel('queue-worker')->info('CronCloseJob is working');
        $job_posts = JobPost::with("employer")->whereIn("job_status", [JobPost::JOB_STATUS_OPEN_WAITING, JobPost::JOB_STATUS_DISABLED])->whereDate("job_date", now())->get();
        $job_mail_helper = new JobMailHelper();
        $updateable_job_ids = array();
        foreach ($job_posts as $job_post) {
            $viewJobLink = '<a href="' . url('/employer/single-job?view=' . $job_post->id) . '" style="padding: 8px 30px; font-size: 16px; font-weight: 700; background: #00A9E0; color: #fff;text-decoration: none;">View Job</a>';
            $job_mail_helper->sendCloseJobNotification($job_post, $job_post->employer, $viewJobLink);
            $updateable_job_ids[] = $job_post->id;
        }
        if (sizeof($updateable_job_ids) > 0) {
            JobPost::whereIn("id", $updateable_job_ids)->update([
                "job_status" => JobPost::JOB_STATUS_CLOSE_EXPIRED
            ]);
        }
    }
}
