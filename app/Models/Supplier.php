<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'store_name',
        'address',
        'second_address',
        'town',
        'country',
        'postcode',
        'email',
        'contact_no',
        'created_by_user_id',
    ];

    public function created_by_user()
    {
        return $this->belongsTo(User::class, "created_by_user_id", "id");
    }

    public function scopeActive($query)
    {
        return $query->where("status", "active");
    }
}