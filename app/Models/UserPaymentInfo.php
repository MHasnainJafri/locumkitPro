<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPaymentInfo extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "user_acl_package_id",
        "payment_type",
        "price",
        "payment_status",
        "payment_token"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
