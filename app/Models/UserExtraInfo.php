<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserExtraInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "aoc_id",
        "gender",
        "dob",
        "mobile",
        "address",
        "city",
        "zip",
        "telephone",
        "company",
        "profile_image",
        "max_distance",
        "minimum_rate",
        "store_id",
        "store_data",
        "site_town_ids",
        "cet",
        "goc",
        "aop",
        "inshurance_company",
        "inshurance_no",
        "inshurance_renewal_date",
        "store_type_name",
    ];
}
