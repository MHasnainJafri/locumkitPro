<?php

namespace App\Jobs;

use App\Helpers\JobMailHelper;
use App\Models\FreelancerPrivateJob;
use App\Models\JobPost;
use App\Models\JobReminder;
use App\Models\PrivateUserJobAction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CronJobReminder implements ShouldQueue
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
        $mailController = new JobMailHelper();
        $jobReminders = JobReminder::with("job_post")->where("job_reminder_date", today())->where("job_reminder_status", 0)->whereHas("job_post", function ($query) {
            $query->where("job_status", JobPost::JOB_STATUS_ACCEPTED);
        })->get();

        $job_reminder_update_list = [];
        foreach ($jobReminders as $jobReminder) {
            $job_reminder_update_list[] = $jobReminder->id;
            $notifyDay = today()->diffInDays($jobReminder->job_date, false);
            $mailController->sendReminder($jobReminder->job_post, $jobReminder->freelancer, $jobReminder->employer, $notifyDay);
        }
        JobReminder::whereIn("id", $job_reminder_update_list)->update([
            "job_reminder_status" => 1
        ]);

        $freelancer_private_jobs = FreelancerPrivateJob::with("freelancer")->whereDate("job_date", now()->addDay())->where("status", 0)->get();
        $update_list_private = [];
        foreach ($freelancer_private_jobs as $job) {
            $update_list_private[] = $job->id;
            $mailController->sendPrivateJobReminder($job->freelancer, $job);
        }
        FreelancerPrivateJob::whereIn("id", $update_list_private)->update([
            "status" => FreelancerPrivateJob::STATUS_NOTIFIED_BEFORE_DAY
        ]);

        $private_user_actions = PrivateUserJobAction::with(["job", "private_user", "employer"])->where("status", PrivateUserJobAction::ACTION_ACCEPT)->where("notify", 0)->whereHas("job", function ($query) {
            $query->whereDate("job_date", now()->addDay());
        })->get();

        $update_list_private_actions = [];
        foreach ($private_user_actions as $value) {
            if ($value->job->job_status == JobPost::JOB_STATUS_ACCEPTED) {
                $mailController->sendRemindertoprivateuser($value->job, $value->private_user, $value->employer);
                $update_list_private_actions[] = $value->id;
            }
        }
        PrivateUserJobAction::whereIn("id", $update_list_private_actions)->update([
            "notify" => 1
        ]);
    }
}
