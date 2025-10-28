<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceEmployer extends Model
{
    use HasFactory;

    protected $fillable = [
        "employer_id",
        "job_id",
        "freelancer_id",
        "freelancer_type",
        "job_date",
        "job_rate",
        "bonus",
        "is_paid",
        "paid_date",
    ];
}