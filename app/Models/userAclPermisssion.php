<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class userAclPermisssion extends Model
{
    use HasFactory;
    public $table="user_acl_permission";

    public function roles()
{
    return $this->belongsToMany(UserAclRole::class, 'user_acl', 'user_acl_permission_id', 'user_acl_role_id');
}
}
