<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobCancelation extends Model
{
    use HasFactory;

    const CANCEL_BY_LIVE_FREELANCER = "live_freelancer";
    const CANCEL_BY_EMPLOYER = "employer";
    const CANCEL_BY_PRIVATE_FREELANCER = "private_freelancer";

    protected $fillable = [
        "job_id",
        "user_id",
        "reason",
        "cancel_by_user_type",
    ];
}