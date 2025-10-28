<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class FinanceIncome extends Model
{
    use HasFactory;

    protected $fillable = [
        "job_id",
        "job_type",
        "freelancer_id",
        "employer_id",
        "job_rate",
        "job_date",
        "income_type",
        "is_bank_transaction_completed",
        "bank_transaction_date",
        "store",
        "location",
        "supplier",
        "status",
    ];

    public function get_income_type(): string
    {
        $income_types = [
            1 => "Income",
            2 => "Bonus",
            3 => "Other",
        ];
        if ($this->income_type && intval($this->income_type) > 0 && intval($this->income_type) <= 3) {
            return $income_types[intval($this->income_type)];
        }
        return "All";
    }
    public function get_job_type(): string
    {
        $job_types = [
            1 => "Website",
            2 => "Private",
            3 => "Other",
        ];
        if ($this->job_type && intval($this->job_type) > 0 && intval($this->job_type) <= 3) {
            return $job_types[intval($this->job_type)];
        }
        return "N/A";
    }

    static public function get_income_type_categories_list(): array
    {
        return [
            "1" => "Income", "2" => "Bonus", "3" =>  "Other"
        ];
    }

    public function getTransactionNumber(): string
    {
        return "INC#" . $this->id;
    }

    public function getTransactionType(): int
    {
        return 1;
    }

    public function getCategoryType()
    {
        return $this->get_income_type();
    }
    
    public function setInDateAttribute($value)
    {
        $this->attributes['in_date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }


    public function setInBankdateAttribute($value)
    {
        $this->attributes['in_bankdate'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
    }
    
  public function setJobDateAttribute($value)
{
    if (is_string($value)) {
        // Convert from d/m/Y string to Carbon instance
        $value = Carbon::createFromFormat('d/m/Y', $value);
    }

    if ($value instanceof Carbon) {
        $this->attributes['job_date'] = $value->format('Y-m-d');
    } else {
        $this->attributes['job_date'] = null;
    }
}
    
    public function setBankTransactionDateAttribute($value)
    {
        if ($value) {
            $this->attributes['bank_transaction_date'] = Carbon::createFromFormat('d/m/Y', $value)->format('Y-m-d');
        } else {
            $this->attributes['bank_transaction_date'] = null;
        }
    }
}