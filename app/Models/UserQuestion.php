<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuestion extends Model
{
    use HasFactory;

    // admin panel
    public function userAclProfession()
    {
        return $this->belongsTo(UserAclProfession::class, 'user_acl_profession_id');
    }
}
