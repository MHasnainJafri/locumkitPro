<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    use HasFactory;
    public $table = "document"; 
    protected $fillable = [
        'name',
        'url_key',
        'status',
        'sort_order',
        'show_in_nav',
        'can_be_cached',
        'locale',
        'user_id',
        'document_type_id',
        'view_id',
        'layout_id',
        'parent_id',
    ];

}
