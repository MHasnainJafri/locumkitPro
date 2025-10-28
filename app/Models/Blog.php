<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;
    protected $fillable = ["title","slug","description","image_path","user_acl_role_id","blog_category_id","status","metatitle","metadescription","metakeywords"];

    public function getBlogcategory(){
        return $this->hasOne(BlogCategory::class, 'id', 'blog_category_id');
    }
}
