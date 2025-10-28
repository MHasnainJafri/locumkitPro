<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        "job_id",
        "job_type",
        "freelancer_id",
        "job_rate",
        "job_date",
        "expense_type_id",
        "description",
        "is_bank_transaction_completed",
        "bank_transaction_date",
        "receipt"
    ];

    public function expense_type()
    {
        return $this->belongsTo(ExpenseType::class);
    }
    public function getTransactionNumber(): string
    {
        return "EXP#" . $this->id;
    }
    public function getTransactionType(): int
    {
        return 2;
    }

    public function getCategoryType()
    {
        return $this->expense_type->expense;
    }
}