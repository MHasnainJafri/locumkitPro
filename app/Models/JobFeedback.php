<?php

namespace App\Models;

use App\Models\JobFeedbackDispute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobFeedback extends Model
{
    use HasFactory;
    const FEEDBACK_BY_EMPLOYER = "employer";
    const FEEDBACK_BY_FREELANCER = "freelancer";

    protected $fillable = [
        "employer_id",
        "freelancer_id",
        "job_id",
        "rating",
        "feedback",
        "comments",
        "user_type",
        "cat_id",
        'status'
    ];

    public function employer()
    {
        return $this->belongsTo(User::class, "employer_id", "id");
    }

    public function freelancer()
    {
        return $this->belongsTo(User::class, "freelancer_id", "id");
    }

    public function job()
    {
        return $this->belongsTo(JobPost::class, "job_id", "id");
    }

    public function jobFeedbackDispute(){
        return $this->hasOne(JobFeedbackDispute::class,"feedback_id", "id");
    }


    // JobFeedback.php


}
