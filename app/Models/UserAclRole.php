<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAclRole extends Model
{
    use HasFactory;
    public $fillable = ['name', 'description', 'is_public'];

    public function permissions()
    {
        return $this->belongsToMany(userAclPermisssion::class, 'user_acl', 'user_acl_role_id', 'user_acl_permission_id');
    }
    
    public function users()
    {
        return $this->hasMany(User::class, 'user_acl_role_id');
    }

    
}
