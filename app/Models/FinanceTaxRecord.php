<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceTaxRecord extends Model
{
    use HasFactory;
    protected $fillable = [
        'finance_year', 'personal_allowance_rate', 'personal_allowance_rate_tax', 'basic_rate', 'basic_rate_tax', 'higher_rate', 'higher_rate_tax',
        'additional_rate', 'additional_rate_tax', 'company_limited_tax'
        ];
}
