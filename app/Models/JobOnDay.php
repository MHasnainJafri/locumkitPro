<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOnDay extends Model
{
    use HasFactory;

    const STATUS_NOT_ATTEND = 0;
    const STATUS_FREELANCER_ATTEND = 1;
    const STATUS_EMPLOYER_VERIFIED_ATTENDANCE = 2;
    const STATUS_FEEDBACK_NOTIFICATION_SEND = 3;
    const STATUS_FEEDBACK_WEEK_NOTIFICATION_SEND = 4;

    protected $fillable = [
        "job_post_id",
        "employer_id",
        "freelancer_id",
        "job_date",
        "status",
    ];

    public function job_post()
    {
        return $this->belongsTo(JobPost::class, "job_post_id", "id");
    }
    public function freelancer()
    {
        return $this->belongsTo(User::class, "freelancer_id", "id");
    }
}