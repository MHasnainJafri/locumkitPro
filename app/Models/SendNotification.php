<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SendNotification extends Model
{
    use HasFactory;
    protected $fillable = ['job_post_id', 'recipient_id', 'message', 'send_date'];
    public function jobposting()
    {
        return $this->belongsTo(JobPost::class, "job_post_id", "id");
    }
}
