<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPost extends Model
{
    use HasFactory;

    const JOB_STATUS_OPEN_WAITING = 1;
    const JOB_STATUS_CLOSE_EXPIRED = 2;
    const JOB_STATUS_DISABLED = 3;
    const JOB_STATUS_ACCEPTED = 4;
    const JOB_STATUS_DONE_COMPLETED = 5;
    const JOB_STATUS_FREEZED = 6;
    const JOB_STATUS_DELETED = 7;
    const JOB_STATUS_CANCELED = 8;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'job_date' => 'datetime',
    ];

    protected $fillable = [
        "employer_id",
        "user_acl_profession_id",
        "job_title",
        "job_date",
        "job_start_time",
        "job_post_desc",
        "job_rate",
        "job_type",
        "job_address",
        "job_region",
        "job_zip",
        "employer_store_list_id",
        "job_status",
    ];

    public function employer()
    {
        return $this->belongsTo(User::class, "employer_id", "id");
    }
    public function category()
    {
        return $this->belongsTo(UserAclProfession::class, "user_acl_profession_id", "id");
    }

    public function send_notification()
    {
        return $this->hasMany(SendNotification::class,'job_post_id');
    }

    public function job_store()
    {
        return $this->belongsTo(EmployerStoreList::class, "employer_store_list_id", "id");
    }

    public function get_store_start_time(): string
    {
        $start_time_value = "";
        if ($this->job_store->store_start_time && json_decode($this->job_store->store_start_time, true)) {
            $start_time = json_decode($this->job_store->store_start_time, true);
            $job_day = $this->job_date->format("l");
            $start_time_value = isset($start_time[$job_day]) ? $start_time[$job_day] : "";
        }
        return $start_time_value;
    }
    public function get_store_finish_time(): string
    {
        $end_time_value = "";
        if ($this->job_store->store_end_time && json_decode($this->job_store->store_end_time, true)) {
            $end_time = json_decode($this->job_store->store_end_time, true);
            $job_day = $this->job_date->format("l");
            $end_time_value = isset($end_time[$job_day]) ? $end_time[$job_day] : "";
        }
        return $end_time_value;
    }
    public function get_store_lunch_time(): string
    {
        $lunch_time_value = "";
        if ($this->job_store->store_lunch_time && json_decode($this->job_store->store_lunch_time, true)) {
            $lunch_time = json_decode($this->job_store->store_lunch_time, true);
            $job_day = $this->job_date->format("l");
            $lunch_time_value = isset($lunch_time[$job_day]) ? $lunch_time[$job_day] . " (Min)" : "";
        }
        return $lunch_time_value;
    }
    public function job_notification()
    {
        return $this->hasMany(Notification::class, "recipient_id");
    }

    public function job_actions()
    {

        return $this->hasMany(JobAction::class,'job_post_id');

    }
    public function private_user_job_actions()
    {
        return $this->hasMany(PrivateUserJobAction::class);
    }

    public function job_post_timelines()
    {
        return $this->hasMany(JobPostTimeline::class);
    }

    public function job_cancellation()
    {
        return $this->hasOne(JobCancelation::class, "job_id", "id");
    }

    public function job_finance_income()
    {
        return $this->hasMany(FinanceIncome::class, "job_id");
    }
    public function job_finance_expense()
    {
        return $this->hasMany(FinanceExpense::class, "job_id");
    }

    public function getAcceptedFreelancerData(): array
    {
        $job_action = $this->job_actions->filter(function ($job_action) {
            return in_array($job_action->action, [JobAction::ACTION_ACCEPT, JobAction::ACTION_DONE, JobAction::ACTION_CANCEL_JOB_BY_FREELANCER, JobAction::ACTION_CANCEL_ACCEPTED_JOB_BY_EMPLOYER]);
        })->sortByDesc("created_at")->first();
        if ($job_action) {
            $freelancer = $job_action->freelancer;
            if ($freelancer) {
                return [
                    "id" => $freelancer->id,
                    "name" => $freelancer->firstname . " " . $freelancer->lastname,
                    "email" => $freelancer->email,
                    "type" => "web"
                ];
            }
        } else {
            $private_job_action = $this->private_user_job_actions->filter(function ($job_action) {
                return in_array($job_action->status, [PrivateUserJobAction::ACTION_ACCEPT, PrivateUserJobAction::ACTION_CANCEL]);
            })->sortByDesc("created_at")->first();

            if ($private_job_action) {
                $freelancer = $private_job_action->private_user;
                if ($freelancer) {
                    return [
                        "id" => $freelancer->id . " (Private)",
                        "name" => $freelancer->name . " (Private)",
                        "email" => $freelancer->email,
                        "type" => "private"
                    ];
                }
            }
        }
        return [
            "id" => "N/A",
            "name" => "N/A",
            "email" => "N/A",
            "type" => "N/A"
        ];
    }

    public function getJobCancelByUserType(): string|null
    {
        if ($this->job_status == JobPost::JOB_STATUS_CANCELED) {
            if ($this->job_cancellation) {
                return $this->job_cancellation->cancel_by_user_type;
            }
        }
        return null;
    }
    public function getPrivateInviteJobs()
    {
        return $this->hasMany(JobInvitedUser::class, 'job_post_id', 'id');
    }
    public function getJobStatus()
    {
        return $this->hasone(JobAction::class, 'job_post_id', 'id');
    }
    public function getPrivateInviteUser()
    {
        return $this->hasOne(JobInvitedUser::class, 'job_post_id', 'id');
    }
}
