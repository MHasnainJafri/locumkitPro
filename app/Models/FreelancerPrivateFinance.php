<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FreelancerPrivateFinance extends Model
{
    use HasFactory;

    protected $fillable  = [
        "freelancer_id",
        "freelancer_private_job_id",
        "job_rate",
        "job_date",
        "employer_name",
    ];
}