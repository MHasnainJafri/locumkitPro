<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockUser extends Model
{
    use HasFactory;

    protected $fillable = [
        "freelancer_id",
        "employer_id",
    ];

    public function freelancer()
    {
        return $this->belongsTo(User::class, "freelancer_id");
    }
}