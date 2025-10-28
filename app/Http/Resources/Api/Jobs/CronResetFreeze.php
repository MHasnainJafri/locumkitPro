<?php

namespace App\Jobs;

use App\Helpers\JobMailHelper;
use App\Models\JobAction;
use App\Models\JobPost;
use App\Models\PrivateUserJobAction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CronResetFreeze implements ShouldQueue
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

        $job_actions_10min = JobAction::with(["job", "freelancer"])
            ->select('id', 'job_post_id', 'freelancer_id', 'freelancer_id as private_user_id', DB::raw("'job_actions_10min' as action_type"))
            ->whereBetween("updated_at", [now()->subMinutes(8), now()->subMinutes(11)])
            ->whereIn("action", [JobAction::ACTION_FREEZE]);

        $job_actions = JobAction::with(["job", "freelancer"])
            ->select('id', 'job_post_id', 'freelancer_id', 'freelancer_id as private_user_id', DB::raw("'job_actions' as action_type"))
            ->where("updated_at", "<", now()->subMinutes(15))
            ->whereIn("action", [JobAction::ACTION_FREEZE, JobAction::ACTION_WAITING_FOR_UNFREEZE])
            ->whereHas("job", function ($query) {
                $query->where("job_status", JobPost::JOB_STATUS_FREEZED);
            });


        $private_user_actions = PrivateUserJobAction::with(["job", "private_user"])
            ->select('id', 'job_post_id', 'private_user_id', 'private_user_id as freelancer_id', DB::raw("'private_user_actions' as action_type"))
            ->whereHas("job", function ($query) {
                $query->whereHas("job_actions", function ($q) {
                    $q->where("updated_at", "<", now()->subMinutes(15))
                        ->whereIn("action", [JobAction::ACTION_FREEZE, JobAction::ACTION_WAITING_FOR_UNFREEZE]);
                });
            });

        /** @var Collection $merged_query */
        $merged_query = $job_actions_10min->union($job_actions)->union($private_user_actions)->get();
        if (sizeof($merged_query) > 0) {

            $job_actions_10min = $merged_query->where('action_type', '=', 'job_actions_10min');
            $job_actions = $merged_query->where('action_type', '=', 'job_actions');
            $private_user_actions = $merged_query->where('action_type', '=', 'private_user_actions');

            //Send notification to only user who freeze the job after 10mint of job freezing, that in 5 mint job will reset/open.
            foreach ($job_actions_10min as $job_action) {
                $encrypted_job_id = encrypt($job_action->job_post_id);
                $encrypted_freelancer_id    = encrypt($job_action->freelancer_id);
                $encrypted_freelancer_type = encrypt("live");
                $linkHref = url("/accept-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");
                $link     = '<a style="outline: none !important;border-radius: 25px;margin-bottom: 15px;font-size: 18px;color: #fff;background-color: #2dc9ff;padding: 10px 35px;text-decoration: none;text-transform: uppercase;font-weight: 500;" href="' . $linkHref . '">Accept</a>';

                $mailController->sendExpireFreezeNotification($job_action->job, $job_action->freelancer, 2, $link);
            }

            //After 15minutes of job freeze just reset/open the job and send notification to all connected locums
            $update_job_list_ids = [];
            $update_job_action_list_ids = [];

            foreach ($job_actions as $job_action) {
                $update_job_list_ids[] = $job_action->job_post_id;
                $update_job_action_list_ids[] = $job_action->id;
                $encrypted_job_id = encrypt($job_action->job_post_id);
                $encrypted_freelancer_id    = encrypt($job_action->freelancer_id);
                $encrypted_freelancer_type = encrypt("live");
                $linkHref = url("/accept-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");
                $link     = '<a style="outline: none !important;border-radius: 25px;margin-bottom: 15px;font-size: 18px;color: #fff;background-color: #2dc9ff;padding: 10px 35px;text-decoration: none;text-transform: uppercase;font-weight: 500;" href="' . $linkHref . '">Accept</a>';

                $mailController->sendExpireFreezeNotification($job_action->job, $job_action->freelancer, 1, $link);
            }
            if (sizeof($update_job_list_ids) > 0) {
                JobPost::whereIn("id", $update_job_list_ids)->update([
                    "job_status" => JobPost::JOB_STATUS_OPEN_WAITING
                ]);
            }
            if (sizeof($update_job_action_list_ids) > 0) {
                JobAction::whereIn("id", $update_job_action_list_ids)->update([
                    "action" => JobAction::ACTION_NONE
                ]);
            }

            //After 15minutes of job freeze send notification to all connected private locums that job has been unfreezed

            foreach ($private_user_actions as $private_user_action) {
                $encrypted_job_id = encrypt($private_user_action->job_post_id);
                $encrypted_freelancer_id    = encrypt($private_user_action->private_user_id);
                $encrypted_freelancer_type = encrypt("private");
                $linkHref = url("/accept-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");
                $link     = '<a style="outline: none !important;border-radius: 25px;margin-bottom: 15px;font-size: 18px;color: #fff;background-color: #2dc9ff;padding: 10px 35px;text-decoration: none;text-transform: uppercase;font-weight: 500;" href="' . $linkHref . '">Accept</a>';

                $mailController->sendExpireFreezeNotificationPrivateLocum($private_user_action->job, $private_user_action->private_user, $link);
            }
        }
    }
}
