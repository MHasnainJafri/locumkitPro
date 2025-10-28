<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAction extends Model
{
    use HasFactory;

    const ACTION_NONE = 0;
    const ACTION_FREEZE = 1;
    const ACTION_APPLY = 2;
    const ACTION_ACCEPT = 3;
    const ACTION_DONE = 4;
    const ACTION_WAITING_FOR_UNFREEZE = 5;
    const ACTION_CANCEL_JOB_BY_FREELANCER = 6;
    const ACTION_CANCEL_ACCEPTED_JOB_BY_EMPLOYER = 7;
    const ACTION_CANCEL_OPEN_JOB_BY_EMPLOYER = 8;
    const FeedBack_Completed=9;

    protected $with = ['freelancer'];

    public function freelancer()
    {
        return $this->belongsTo(User::class, "freelancer_id");
    }

    public function jobposting()
    {

        return $this->belongsTo(JobPost::class,"job_post_id",'id');
    }
    
    public function job()
    {
        return $this->belongsTo(JobPost::class, "job_post_id",'id');
    }

    public function freelanceprivatejob()
    {
        return $this->belongsTo(FreelancerPrivateJob::class,'freelancer_id');
    }

}
