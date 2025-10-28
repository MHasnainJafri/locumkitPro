<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceNiTaxRecord extends Model
{
    use HasFactory;
    
    protected $fillable = ['finance_year',
            'c4_min_ammount_1',
            'c4_min_ammount_tax_1',
            'c4_min_ammount_2',
            'c4_min_ammount_tax_2',
            'c4_min_ammount_3',
            'c4_min_ammount_tax_3',
            'c2_min_amount',
            'c2_tax'];
    
}
