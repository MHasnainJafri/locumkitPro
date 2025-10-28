<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceProfitLoss extends Model
{
    use HasFactory;

    protected $table = "finance_profit_loss";

    protected $fillable = [
        "fre_id",
        "revenue",
        "cos",
        "othercost",
        "income_tax",
        "interest_income",
        "tax_calculation",
        "financial_year",
        "starting_month"
    ];
}
