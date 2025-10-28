<?php

namespace App\Jobs;

use App\Helpers\JobMailHelper;
use App\Helpers\JobSmsHelper;
use App\Models\FreelancerPrivateJob;
use App\Models\JobAction;
use App\Models\JobOnDay;
use App\Models\JobPost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CronOnDayExpense implements ShouldQueue
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
        /* Get job reminder dates information */
        $job_mail_helper = new JobMailHelper();
        $job_sms_helper = new JobSmsHelper();
        $jobsOnDay = JobOnDay::with(["job_post", "freelancer"])->whereIn("status", [JobOnDay::STATUS_FREELANCER_ATTEND, JobOnDay::STATUS_EMPLOYER_VERIFIED_ATTENDANCE])->whereDate("job_date", now())->where("is_notified", 1)->get();

        $job_status_update_ids = [];
        foreach ($jobsOnDay as $jobOnDay) {
            if ($jobOnDay->job_post->job_status == JobPost::JOB_STATUS_ACCEPTED) {
                $job_id     = $jobOnDay['job_post_id'];
                $freelancer_id = $jobOnDay['freelancer_id'];
                $employer_id = $jobOnDay['employer_id'];

                $encrypted_job_id = encrypt($job_id);
                $encrypted_freelancer_id = encrypt($freelancer_id);
                $encrypted_employer_id = encrypt($employer_id);

                $hrefLink = url("/expense-cost-form?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&employer_id={$encrypted_employer_id}");
                $link = '<a style="outline: none !important;text-decoration: none;" href="' . $hrefLink . '">click here</a>';

                if ($jobOnDay->freelancer->can_freelancer_get_feedback()) {
                    $job_mail_helper->sendExpenseNotification($jobOnDay->job_post, $jobOnDay->freelancer, $link);
                }
                $job_status_update_ids[] = $job_id;
                JobAction::where("job_post_id", $job_id)->where("freelancer_id", $freelancer_id)->update([
                    "action" => JobAction::ACTION_DONE
                ]);
                $job_sms_helper->sendExpenseNotificationSms($jobOnDay->freelancer, $job_id, $hrefLink);
            }
        }

        if (sizeof($job_status_update_ids) > 0) {
            JobPost::whereIn("id", $job_status_update_ids)->update([
                "job_status" => JobPost::JOB_STATUS_DONE_COMPLETED
            ]);
        }

        /* Private job expense */
        $updateable_job_ids = [];
        $freelancer_private_jobs = FreelancerPrivateJob::with("freelancer")->whereDate("job_date", now())->where("status", FreelancerPrivateJob::STATUS_JOB_ATTENDED)->get();
        foreach ($freelancer_private_jobs as $freelancer_private_job) {
            $job_id      = $freelancer_private_job['id'];
            $freelancer_id       = $freelancer_private_job['freelancer_id'];

            $encrypted_job_id = encrypt($job_id);
            $encrypted_freelancer_id = encrypt($freelancer_id);
            $encrypted_job_type = encrypt("private");
            $hrefLink = url("/expense-cost-form?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&job_type={$encrypted_job_type}");
            $link = '<a href="' . $hrefLink . '" style="outline: none !important;text-decoration: none;">clicking here</a>';

            $job_mail_helper->sendExpenseNotification($freelancer_private_job, $freelancer_private_job->freelancer, $link, 'private');
            $updateable_job_ids[] = $job_id;
        }
        if (sizeof($updateable_job_ids) > 0) {
            FreelancerPrivateJob::whereIn("id", $updateable_job_ids)->update([
                "status" => FreelancerPrivateJob::STATUS_EXPENSE_NOTIFICATION_SEND
            ]);
        }
    }
}
