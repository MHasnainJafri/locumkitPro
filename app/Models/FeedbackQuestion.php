<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeedbackQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['question_freelancer', 'question_employer', 'question_cat_id', 'question_sort_order', 'question_status'];
}
