<?php

namespace App\Jobs;

use App\Helpers\JobMailHelper;
use App\Models\FinanceEmployer;
use App\Models\FreelancerPrivateJob;
use App\Models\JobOnDay;
use App\Models\JobPost;
use App\Models\PrivateUserJobAction;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CronOnDay implements ShouldQueue
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
        $jobsOnDay = JobOnDay::with(["job_post", "freelancer"])->where("status", JobOnDay::STATUS_NOT_ATTEND)->whereDate("job_date", now())->where("is_notified", 0)->get();
        foreach ($jobsOnDay as $jobOnDay) {
            $job_id  = $jobOnDay['job_post_id'];
            $employer_id = $jobOnDay['employer_id'];
            $freelancer_id = $jobOnDay['freelancer_id'];
            $job_date = Carbon::parse($jobOnDay->job_date);

            $encrypted_job_id = encrypt($job_id);
            $encrypted_freelancer_id = encrypt($freelancer_id);
            $encrypted_yes = encrypt("yes");
            $encrypted_no = encrypt("no");
            $job_type_encrypted = encrypt("website");

            $yes_link = url("/attendance?job_id={$encrypted_job_id}&user_id={$encrypted_freelancer_id}&action={$encrypted_yes}&job_type={$job_type_encrypted}");
            $no_link = url("/cancel-job?job_id={$job_id}");

            $btns_links = '
                <a style="outline: none !important;float:left;font-size: 20px;background-color: #2dc9ff;padding: 7px 30px;color: #fff;text-transform: uppercase;text-decoration: none;border-radius: 25px; margin-right: 15px;" href="' . $yes_link . '">Yes</a>
                <a style="outline: none !important;position: relative;top: 3px;float:left;font-size: 20px;background-color: #2dc9ff;padding: 7px 30px;color: #fff;text-transform: uppercase;text-decoration: none;border-radius: 25px;" href="' . $no_link . '">No</a>
                <div style="clear:both;width:100%;"></div>
            ';

            $job = $jobOnDay->job_post;
            $freelancer = $jobOnDay->freelancer;

            if ($job['job_status'] == JobPost::JOB_STATUS_ACCEPTED) {
                FinanceEmployer::create([
                    "employer_id" => $employer_id,
                    "job_id" => $job_id,
                    "freelancer_id" => $freelancer_id,
                    "freelancer_type" => 1,
                    "job_date" => $job_date,
                    "job_rate" => $job->job_rate,
                    "bonus" => null,
                    "is_paid" => null,
                    "paid_date" => null
                ]);

                if ($freelancer->can_freelancer_get_job_invitation()) {
                    $job_mail_helper->sendOnDayNotificationToFreelancer($job, $freelancer, $btns_links);
                }
                $jobOnDay->is_notified = 1;
                $jobOnDay->save();
            }
        }

        /* Work on freelancer private jobs */
        $freelancer_private_jobs = FreelancerPrivateJob::with("freelancer")->where("job_date", today())->whereIn("status", [0, 1])->get();
        $updateable_private_job_ids = array();
        foreach ($freelancer_private_jobs as $private_job) {
            $jobPvid      = $private_job['id'];
            $jobFid       = $private_job['freelancer_id'];
            $pEmpName     = $private_job['emp_name'];
            $pEmpEmail    = $private_job['emp_email'];
            $pJobTitle    = $private_job['job_title'];
            $pJobRate     = $private_job['job_rate'];
            $pJobDate     = $private_job['job_date'];
            $pJobLocation = $private_job['job_location'];

            $job_id_encrypted = encrypt($jobPvid);
            $freelancer_id_encrypted = encrypt($jobFid);
            $action_encrypted = encrypt("yes");
            $job_type_encrypted = encrypt("private");

            $hrefYesLink = url("/attendance?job_id={$job_id_encrypted}&user_id={$freelancer_id_encrypted}&action={$action_encrypted}&job_type={$job_type_encrypted}");
            $hrefNoLink = url("/private-job-cancel?job_id={$job_id_encrypted}&freelancer_id={$freelancer_id_encrypted}");

            $yesBtnLink = '
                <a style="outline: none !important;" href="' . $hrefYesLink . '" style=" float:left;">
                    <img src="' . url("/frontend/images/yes.png") . '" style="width: 120px;"   alt="Yes" />
                </a>
                <a href="' . $hrefNoLink . '"><img src="' . url("/frontend/images/no.png") . '" style="width: 120px;"  alt="No"/></a>
            ';
            $job_mail_helper->sendPrivateJobOnDayReminder($jobPvid, $private_job->freelancer, $pEmpName, $pEmpEmail, $pJobTitle, $pJobRate, $pJobDate, $pJobLocation, $yesBtnLink);

            $updateable_private_job_ids[] = $jobPvid;
        }
        if (sizeof($updateable_private_job_ids) > 0) {
            FreelancerPrivateJob::whereIn("id", $updateable_private_job_ids)->update([
                "status" => FreelancerPrivateJob::STATUS_NOTIFIED_ON_JOB_DAY
            ]);
        }

        /* If job accepted by private locum then update employer finance */
        $private_freelancer_jobs = PrivateUserJobAction::with(["job", "private_user"])->where("status", PrivateUserJobAction::ACTION_ACCEPT)->where("notify", 1)->whereHas("job", function ($query) {
            $query->whereDate("job_date", now());
        })->get();

        $private_freelancer_job_action_ids = [];
        foreach ($private_freelancer_jobs as  $private_freelancer_job) {
            $employer_id = $private_freelancer_job['employer_id'];
            $private_user_id = $private_freelancer_job['private_user_id'];
            $job_id     = $private_freelancer_job['job_post_id'];
            $job_date = $private_freelancer_job->job->job_date;

            if ($private_freelancer_job->job->job_status == JobPost::JOB_STATUS_ACCEPTED) {
                $job_mail_helper->sendOnDayRemindertoprivateuser($private_freelancer_job->private_user, $private_freelancer_job->job);

                $private_freelancer_job_action_ids[] = $private_freelancer_job->id;
                FinanceEmployer::create([
                    "employer_id" => $employer_id,
                    "job_id" => $job_id,
                    "freelancer_id" => $private_user_id,
                    "freelancer_type" => 2,
                    "job_date" => $job_date,
                    "job_rate" => $private_freelancer_job->job->job_rate,
                    "bonus" => null,
                    "is_paid" => null,
                    "paid_date" => null
                ]);
            }
        }

        if (sizeof($private_freelancer_job_action_ids) > 0) {
            PrivateUserJobAction::whereIn("id", $private_freelancer_job_action_ids)->update([
                "notify" => 2
            ]);
        }
    }
}
