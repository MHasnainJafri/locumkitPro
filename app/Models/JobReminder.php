<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        "job_post_id",
        "employer_id",
        "freelancer_id",
        "job_date",
        "job_reminder_date"
    ];

    public function job_post()
    {
        return $this->belongsTo(JobPost::class, "job_post_id", "id");
    }
    public function employer()
    {
        return $this->belongsTo(User::class, "employer_id", "id");
    }
    public function freelancer()
    {
        return $this->belongsTo(User::class, "freelancer_id", "id");
    }
}