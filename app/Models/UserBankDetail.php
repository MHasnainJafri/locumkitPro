<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserBankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "acccount_name",
        "acccount_number",
        "acccount_sort_code",
    ];
}