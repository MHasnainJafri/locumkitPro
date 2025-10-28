<?php
namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class FinanceBalanceSheet extends Model
    {
        use HasFactory;
        protected $table = "finance_balancesheets";

        protected $fillable = [
            "fre_id",
            "income_tax",
            "input_data",
            "financial_year",
            "profit_plan_equip",
            "trade_other",
            "cash_equp",
            "total_cash_trade",
            "total_assets",
            "current_liability",
            "taxation",
            "total_tax_liab",
            "net_assests_liab",
            "equity",
            "retained_earning",
        ];
    }
