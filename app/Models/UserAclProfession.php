<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAclProfession extends Model
{
    use HasFactory;
    protected $table = 'user_acl_professions';
    protected $fillable = [
        'name',
        'description',
        'is_active',
    ];

    public function getPrfessionUsers(){
        return $this->hasMany(User::class, "user_acl_profession_id", "id");
    }
}
