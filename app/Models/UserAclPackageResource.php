<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAclPackageResource extends Model
{
    use HasFactory;
    public $fillable=['resource_key','resource_value','allow_count'];
}