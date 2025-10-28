<?php

namespace App\Jobs;

use App\Helpers\JobMailHelper;
use App\Models\JobFeedback;
use App\Models\JobPost;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CronJobSummary implements ShouldQueue
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
        //Send job summary after two days
        $financeInfoArray = JobPost::with(["job_finance_income", "job_finance_expense", "employer"])->where("job_date", today()->subDays(2))->whereHas("job_finance_income", function ($query) {
            $query->where("job_type", 1)->where("income_type", 1);
        })->get();

        foreach ($financeInfoArray as $job) {
            $job_income = $job->job_finance_income->first();
            if (is_null($job_income)) continue;
            $freelancer = $job_income->freelancer;
            if (is_null($freelancer)) continue;
            $employer = $job->employer;
            $income = $job_income->job_rate;
            $expense = 0;
            if ($job->job_finance_expense && sizeof($job->job_finance_expense) > 0) {
                $expense  = $job->job_finance_expense->sum("job_rate");
            }
            $empFeedback = JobFeedback::where("employer_id", $employer->id)->where("job_id", $job->id)->where("user_type", JobFeedback::FEEDBACK_BY_FREELANCER)->first();
            $empFeedback = $empFeedback ? $empFeedback->rating : 0;
            $freFeedback = JobFeedback::where("freelancer_id", $freelancer->id)->where("job_id", $job->id)->where("user_type", JobFeedback::FEEDBACK_BY_EMPLOYER)->first();
            $freFeedback = $freFeedback ? $freFeedback->rating : 0;

            if ($freelancer->can_freelancer_get_job_reminders()) {
                $mailController->sendFreJobSummaryNotification($freelancer, $job, $income, $expense, $freFeedback);
            }
            $mailController->sendEmpJobSummaryNotification($employer, $freelancer, $job, $income, $expense, $empFeedback);
        }
    }
}
