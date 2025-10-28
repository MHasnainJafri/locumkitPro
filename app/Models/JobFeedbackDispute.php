<?php

namespace App\Models;

use App\Models\JobFeedback;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class JobFeedbackDispute extends Model
{
    use HasFactory;

    const FEEDBACK_DISPUTE_BY_EMPLOYER = "employer";
    const FEEDBACK_DISPUTE_BY_FREELANCER = "freelancer";

    protected $fillable = [
        "feedback_id",
        "user_type",
        "comment",
        "status",
    ];

    public function jobFeedback()
    {
        return $this->belongsTo(JobFeedback::class, 'feedback_id', 'id');
    }
}
