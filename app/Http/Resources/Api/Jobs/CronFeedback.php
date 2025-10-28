<?php

namespace App\Jobs;

use App\Helpers\JobMailHelper;
use App\Helpers\JobSmsHelper;
use App\Models\JobAction;
use App\Models\JobFeedback;
use App\Models\JobOnDay;
use App\Models\JobPost;
use App\Models\PrivateUserJobAction;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CronFeedback implements ShouldQueue
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
        $smsController = new JobSmsHelper();
        /* Check for attendance */
        $jobs_on_day = JobOnDay::whereDate("job_date", "<=", today()->subDay())->where("status", JobOnDay::STATUS_EMPLOYER_VERIFIED_ATTENDANCE)->get();
        $job_mail_helper = new JobMailHelper();

        if (sizeof($jobs_on_day) > 0) {
            foreach ($jobs_on_day as $job_on_day) {
                $job_id = $job_on_day['job_post_id'];
                $employer_id   = $job_on_day['employer_id'];
                $freelancer_id   = $job_on_day['freelancer_id'];
                $freelancer = User::find($freelancer_id);
                $employer = User::find($employer_id);

                $encrypted_job_id = encrypt($job_id);
                $encrypted_freelancer_id = encrypt($freelancer_id);
                $encrypted_employer_id = encrypt($employer_id);

                $encrypted_user_type_freelancer = encrypt("freelancer");
                $encrypted_user_type_employer = encrypt("employer");
                $feedback_url_freelancer = url("/feedback?job_id={$encrypted_job_id}&user_id={$encrypted_freelancer_id}&user_type={$encrypted_user_type_freelancer}");
                $feedback_url_employer = url("/feedback?job_id={$encrypted_job_id}&user_id={$encrypted_employer_id}&user_type={$encrypted_user_type_employer}");
                $block_url_locum = url("/block-user?employer_id={$encrypted_employer_id}&freelancer_id={$encrypted_freelancer_id}");
                $feedback_link_emp = '<a href="' . $feedback_url_employer . '" style="padding: 8px 30px; font-size: 16px; font-weight: 700; background: #00A9E0; color: #fff; ">Submit your feedback here</a>';
                $feedback_link_fre = '<a href="' . $feedback_url_freelancer . '" style="padding: 8px 30px; font-size: 16px; font-weight: 700; background: #00A9E0; color: #fff; ">Submit your feedback here</a>';
                $block_locum_link = '<p>Want to block this locum, please <a href="' . $block_url_locum . '">click here.</a></p>';

                JobPost::where("id", $job_id)->update([
                    "job_status" => JobPost::JOB_STATUS_DONE_COMPLETED
                ]);
                JobAction::where("job_post_id", $job_id)->where("freelancer_id", $freelancer_id)->update([
                    "action" => JobAction::ACTION_DONE
                ]);
                $job = JobPost::find($job_id);
                if ($job && $freelancer && $freelancer->can_freelancer_get_feedback()) {
                    $job_mail_helper->sendFeedbackNotification($job, $freelancer, $employer, $feedback_link_fre, $feedback_link_emp, $block_locum_link);
                    $job_on_day->status = JobOnDay::STATUS_FEEDBACK_NOTIFICATION_SEND;
                    $job_on_day->save();

                    $smsController->sendFeedbackNotificationFreSms($freelancer, $job_id, $feedback_url_freelancer);
                    $smsController->sendFeedbackNotificationEmpSms($employer, $job_id, $feedback_url_employer);
                }
            }
        }

        /* Check for job cancel */
        $jobs_canceled_on_day = JobOnDay::whereDate("job_date", today()->subDay())->where("status", JobOnDay::STATUS_NOT_ATTEND)->whereHas("job_post", function ($job_query) {
            $job_query->where("job_status", JobPost::JOB_STATUS_CANCELED)->whereHas("job_actions", function ($query) {
                $query->whereIn("action", [JobAction::ACTION_CANCEL_ACCEPTED_JOB_BY_EMPLOYER, JobAction::ACTION_CANCEL_JOB_BY_FREELANCER]);
            });
        })->get();
        if (sizeof($jobs_canceled_on_day) > 0) {
            foreach ($jobs_canceled_on_day as $job_on_day) {
                $job_id = $job_on_day['job_post_id'];
                $employer_id   = $job_on_day['employer_id'];
                $freelancer_id   = $job_on_day['freelancer_id'];
                $freelancer = User::find($freelancer_id);
                $employer = User::find($employer_id);

                $encrypted_job_id = encrypt($job_id);
                $encrypted_freelancer_id = encrypt($freelancer_id);
                $encrypted_employer_id = encrypt($employer_id);

                $encrypted_user_type_freelancer = encrypt("freelancer");
                $encrypted_user_type_employer = encrypt("employer");
                $feedback_url_freelancer = url("/feedback?job_id={$encrypted_job_id}&user_id={$encrypted_freelancer_id}&user_type={$encrypted_user_type_freelancer}");
                $feedback_url_employer = url("/feedback?job_id={$encrypted_job_id}&user_id={$encrypted_employer_id}&user_type={$encrypted_user_type_employer}");
                $block_url_locum = url("/block-user?employer_id={$encrypted_employer_id}&freelancer_id={$encrypted_freelancer_id}");
                $feedback_link_emp = '<a href="' . $feedback_url_employer . '" style="padding: 8px 30px; font-size: 16px; font-weight: 700; background: #00A9E0; color: #fff; ">Submit your feedback here</a>';
                $feedback_link_fre = '<a href="' . $feedback_url_freelancer . '" style="padding: 8px 30px; font-size: 16px; font-weight: 700; background: #00A9E0; color: #fff; ">Submit your feedback here</a>';
                $block_locum_link = '<p>Want to block this locum, please <a href="' . $block_url_locum . '">click here.</a></p>';

                $job = JobPost::find($job_id);
                if ($job && $freelancer && $freelancer->can_freelancer_get_feedback()) {
                    $job_mail_helper->sendFeedbackNotification($job, $freelancer, $employer, $feedback_link_fre, $feedback_link_emp, $block_locum_link);
                    $job_on_day->status = JobOnDay::STATUS_FEEDBACK_NOTIFICATION_SEND;
                    $job_on_day->save();

                    $smsController->sendFeedbackNotificationFreSms($freelancer, $job_id, $feedback_url_freelancer);
                    $smsController->sendFeedbackNotificationEmpSms($employer, $job_id, $feedback_url_employer);
                }
            }
        }

        /* Send alert of feedback after 1 week if user not submitted the feedback */
        $jobs_on_day = JobOnDay::whereDate("job_date", today()->subWeek())->where("status", JobOnDay::STATUS_FEEDBACK_NOTIFICATION_SEND)->get();
        if (sizeof($jobs_on_day) > 0) {
            foreach ($jobs_on_day as $job_on_day) {
                $job_id = $job_on_day['job_post_id'];
                $employer_id   = $job_on_day['employer_id'];
                $freelancer_id   = $job_on_day['freelancer_id'];
                $freelancer = User::find($freelancer_id);
                $employer = User::find($employer_id);

                $jobFeedbackStatusFre = JobFeedback::where("job_id", $job_id)->where("user_type", JobFeedback::FEEDBACK_BY_FREELANCER)->get();
                $jobFeedbackStatusEmp = JobFeedback::where("job_id", $job_id)->where("user_type", JobFeedback::FEEDBACK_BY_EMPLOYER)->get();
                $encrypted_job_id = encrypt($job_id);
                $encrypted_freelancer_id = encrypt($freelancer_id);
                $encrypted_employer_id = encrypt($employer_id);

                $encrypted_user_type_freelancer = encrypt("freelancer");
                $encrypted_user_type_employer = encrypt("employer");
                $feedback_url_freelancer = url("/feedback?job_id={$encrypted_job_id}&user_id={$encrypted_freelancer_id}&user_type={$encrypted_user_type_freelancer}");
                $feedback_url_employer = url("/feedback?job_id={$encrypted_job_id}&user_id={$encrypted_employer_id}&user_type={$encrypted_user_type_employer}");
                if (sizeof($jobFeedbackStatusFre) == 0  && $freelancer->can_freelancer_get_feedback()) {
                    $aLinkTag = '<a href="' . $feedback_url_freelancer . '" style="padding: 8px 30px; font-size: 16px; font-weight: 700; background: #00A9E0; color: #fff; ">Submit your feedback here</a>';
                    $job_mail_helper->sendFeedbackNotificationOneWeekAlert($job_on_day->job_post, $freelancer, $aLinkTag, 2);
                    $smsController->sendFeedbackNotificationFreSms($freelancer, $job_id, $feedback_url_freelancer);
                }
                if (sizeof($jobFeedbackStatusEmp) == 0  && $freelancer->can_freelancer_get_feedback()) {
                    $aLinkTag = '<a href="' . $feedback_url_employer . '" style="padding: 8px 30px; font-size: 16px; font-weight: 700; background: #00A9E0; color: #fff; ">Submit your feedback here</a>';
                    $job_mail_helper->sendFeedbackNotificationOneWeekAlert($job_on_day->job_post, $employer, $aLinkTag, 3);
                    $smsController->sendFeedbackNotificationFreSms($employer, $job_id, $feedback_url_employer);
                }

                $job_on_day->status = JobOnDay::STATUS_FEEDBACK_WEEK_NOTIFICATION_SEND;
                $job_on_day->save();
            }
        }

        /* Send canceledjob feedback alert after 1 week if user not submit the feedback */
        $jobs_canceled_on_day = JobOnDay::whereDate("job_date", today()->subWeek())->where("status", JobOnDay::STATUS_FEEDBACK_NOTIFICATION_SEND)->whereHas("job_post", function ($job_query) {
            $job_query->where("job_status", JobPost::JOB_STATUS_CANCELED)->whereHas("job_actions", function ($query) {
                $query->whereIn("action", [JobAction::ACTION_CANCEL_ACCEPTED_JOB_BY_EMPLOYER, JobAction::ACTION_CANCEL_JOB_BY_FREELANCER]);
            });
        })->get();
        if (sizeof($jobs_canceled_on_day) > 0) {
            foreach ($jobs_canceled_on_day as $job_on_day) {
                $job_id = $job_on_day['job_post_id'];
                $employer_id   = $job_on_day['employer_id'];
                $freelancer_id   = $job_on_day['freelancer_id'];
                $freelancer = User::find($freelancer_id);
                $employer = User::find($employer_id);

                $jobFeedbackStatusFre = JobFeedback::where("job_id", $job_id)->where("user_type", JobFeedback::FEEDBACK_BY_FREELANCER)->get();
                $jobFeedbackStatusEmp = JobFeedback::where("job_id", $job_id)->where("user_type", JobFeedback::FEEDBACK_BY_EMPLOYER)->get();
                $encrypted_job_id = encrypt($job_id);
                $encrypted_freelancer_id = encrypt($freelancer_id);
                $encrypted_employer_id = encrypt($employer_id);

                $encrypted_user_type_freelancer = encrypt("freelancer");
                $encrypted_user_type_employer = encrypt("employer");
                $feedback_url_freelancer = url("/feedback?job_id={$encrypted_job_id}&user_id={$encrypted_freelancer_id}&user_type={$encrypted_user_type_freelancer}");
                $feedback_url_employer = url("/feedback?job_id={$encrypted_job_id}&user_id={$encrypted_employer_id}&user_type={$encrypted_user_type_employer}");
                if (sizeof($jobFeedbackStatusFre) == 0  && $freelancer->can_freelancer_get_feedback()) {
                    $aLinkTag = '<a href="' . $feedback_url_freelancer . '" style="padding: 8px 30px; font-size: 16px; font-weight: 700; background: #00A9E0; color: #fff; ">Submit your feedback here</a>';
                    $job_mail_helper->sendFeedbackNotificationOneWeekAlert($job_on_day->job_post, $freelancer, $aLinkTag, 2);
                    $smsController->sendFeedbackNotificationFreSms($freelancer, $job_id, $feedback_url_freelancer);
                }
                if (sizeof($jobFeedbackStatusEmp) == 0  && $freelancer->can_freelancer_get_feedback()) {
                    $aLinkTag = '<a href="' . $feedback_url_employer . '" style="padding: 8px 30px; font-size: 16px; font-weight: 700; background: #00A9E0; color: #fff; ">Submit your feedback here</a>';
                    $job_mail_helper->sendFeedbackNotificationOneWeekAlert($job_on_day->job_post, $employer, $aLinkTag, 3);
                    $smsController->sendFeedbackNotificationFreSms($employer, $job_id, $feedback_url_employer);
                }

                $job_on_day->status = JobOnDay::STATUS_FEEDBACK_WEEK_NOTIFICATION_SEND;
                $job_on_day->save();
            }
        }

        /* If job accepted by private locum then update job status to done */
        $private_users_actions = PrivateUserJobAction::where("status", PrivateUserJobAction::ACTION_ACCEPT)->where("notify", 2)->whereHas("job", function ($query) {
            $query->whereDate("job_date", today()->subDay());
        })->select("id", "job_post_id")->get();
        $private_user_action_ids = [];
        $job_ids = [];
        foreach ($private_users_actions as $private_users_action) {
            $private_user_action_ids[] = $private_users_action->id;
            $job_ids[] = $private_users_action->job_post_id;
        }
        if (sizeof($job_ids) > 0) {
            JobPost::whereIn("id", $job_ids)->update([
                "job_status" => JobPost::JOB_STATUS_DONE_COMPLETED
            ]);
        }
        if (sizeof($private_user_action_ids) > 0) {
            PrivateUserJobAction::whereIn("id", $private_user_action_ids)->update([
                "status" => PrivateUserJobAction::ACTION_DONE
            ]);
        }
    }
}
