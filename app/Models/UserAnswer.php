<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "user_question_id",
        "type_value",
    ];
    protected $with = ['question'];

    public function question()
    {
        return $this->belongsTo(UserQuestion::class, "user_question_id", "id");
    }
}