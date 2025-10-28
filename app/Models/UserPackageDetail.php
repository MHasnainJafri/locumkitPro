<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPackageDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "user_acl_package_id",
        "package_active_date",
        "package_expire_date",
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}