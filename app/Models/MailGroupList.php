<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MailGroupList extends Model
{
    use HasFactory;

    public function mail_group_mails()
    {
        return $this->hasMany(MailGroupMail::class, "mail_group_list_id", "id");
    }
}
