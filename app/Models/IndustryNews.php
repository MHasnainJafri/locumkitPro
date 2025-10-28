<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndustryNews extends Model
{
    use HasFactory;
    protected $fillable = [
        "title",
        "slug",
        "description",
        "image_path",
        "user_type",
        "category_id",
        "status",
        "metatitle",
        "metadescription",
        "metakeywords",
    ];

    public $table = "industry_news";
}