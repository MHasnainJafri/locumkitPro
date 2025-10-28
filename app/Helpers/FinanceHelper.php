<?php

namespace App\Helpers;

use App\Models\FinanceEmployer;
use App\Models\FinanceExpense;
use App\Models\FinanceIncome;
use App\Models\FinanceNiTaxRecord;
use App\Models\FinanceTaxRecord;
use App\Models\FinancialYear;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class FinanceHelper
{
    private FinancialYear|null $user_financial_year;
    private User $user;
    public function __construct(User $user)
    {
        $this->user = $user;
        $this->user_financial_year = FinancialYear::where("user_id", $user->id)->first();
    }

    public function get_chart_finance_data(Builder $data_object, Carbon $year_start, Carbon $year_end, int $finance_year_start_month, $filter = 'month', bool $split_data_by_pay_status = false, bool $is_paid = false, string $filter_key = null, string $filter_value = null): array
    {
        $income_chart_data = array();
        if ($filter == "month") {
            $chart_year_start = $year_start->copy();
            $chart_year_end = $year_end->copy();
            if ($year_end->greaterThan(today()->endOfMonth())) {
                $chart_year_end = today()->endOfMonth();
            }
            $income_records = $data_object->select("job_rate", "job_date", "is_bank_transaction_completed")->where("freelancer_id", $this->user->id)->whereBetween("job_date", [$year_start, $year_end]);
            if ($filter_key && $filter_key != "") {
                if ($filter_value == null) {
                    $income_records = $income_records->whereNull($filter_key);
                } else {
                    $income_records = $income_records->where($filter_key, $filter_value);
                }
            }
            if ($is_paid) {
                $income_records = $income_records->where("is_bank_transaction_completed", true);
            }
            $income_records = $income_records->get();
            if ($split_data_by_pay_status) {
                $income_chart_paid_data = array();
                $income_chart_unpaid_data = array();
                $month_labels = array();
                for ($i = $chart_year_start; $i <= $chart_year_end; $i->addMonth()) {
                    $month_total_paid = $income_records->filter(function ($record) use ($i) {
                        if ($record->job_date == null || $record->job_date == "") {
                            return false;
                        }
                        return Carbon::parse($record->job_date)->greaterThanOrEqualTo($i) && Carbon::parse($record->job_date)->lessThanOrEqualTo($i->copy()->endOfMonth()) && $record->is_bank_transaction_completed;
                    })->sum("job_rate");
                    $income_chart_paid_data[] = $month_total_paid;
                    $month_total_unpaid = $income_records->filter(function ($record) use ($i) {
                        if ($record->job_date == null || $record->job_date == "") {
                            return false;
                        }
                        return Carbon::parse($record->job_date)->greaterThanOrEqualTo($i) && Carbon::parse($record->job_date)->lessThanOrEqualTo($i->copy()->endOfMonth()) && !$record->is_bank_transaction_completed;
                    })->sum("job_rate");
                    $income_chart_unpaid_data[] = $month_total_unpaid;
                    $month_labels[] = $i->format("M");
                }
                $income_chart_data["labels"] = $month_labels;
                $income_chart_data["data_paid"] = $income_chart_paid_data;
                $income_chart_data["data_unpaid"] = $income_chart_unpaid_data;
            } else {
                for ($i = $chart_year_start; $i <= $chart_year_end; $i->addMonth()) {
                    $month_total = $income_records->filter(function ($record) use ($i) {
                        if ($record->job_date == null || $record->job_date == "") {
                            return false;
                        }
                        return Carbon::parse($record->job_date)->greaterThanOrEqualTo($i) && Carbon::parse($record->job_date)->lessThanOrEqualTo($i->copy()->endOfMonth());
                    })->sum("job_rate");
                    $income_chart_data[$i->format("M")] = $month_total;
                }
            }
        } else {
            $user_creation_year_start = Carbon::parse($this->user->created_at)->startOfYear();
            $creation_month = $this->user->created_at->month;
            $current_year = today()->endOfYear();
            if ($creation_month < $finance_year_start_month) {
                $user_creation_year_start->subYear();
            }
            /* Show only last 3 years */
            if ($user_creation_year_start->copy()->addYears(3)->lessThanOrEqualTo(today())) {
                $user_creation_year_start = today()->subYears(3)->startOfYear();
            }
            $yearly_income_record = $data_object->select("job_rate", "job_date", "is_bank_transaction_completed")->where("freelancer_id", $this->user->id)->whereBetween("job_date", [$user_creation_year_start, today()]);
            if ($filter_key && $filter_key != "") {
                if ($filter_value == null) {
                    $yearly_income_record = $yearly_income_record->whereNull($filter_key);
                } else {
                    $yearly_income_record = $yearly_income_record->where($filter_key, $filter_value);
                }
            }
            if ($is_paid) {
                $yearly_income_record = $yearly_income_record->where("is_bank_transaction_completed", true);
            }
            $yearly_income_record = $yearly_income_record->get();
            if ($split_data_by_pay_status) {
                $income_chart_paid_data = array();
                $income_chart_unpaid_data = array();
                $year_labels = array();
                for ($i = $user_creation_year_start; $i <= today(); $i->addYear()) {
                    $year_total_paid = $yearly_income_record->filter(function ($record) use ($i) {
                        if ($record->job_date == null || $record->job_date == "") {
                            return false;
                        }
                        return Carbon::parse($record->job_date)->greaterThanOrEqualTo($i) && Carbon::parse($record->job_date)->lessThanOrEqualTo($i->copy()->endOfYear()) && $record->is_bank_transaction_completed;
                    })->sum("job_rate");
                    $year_total_unpaid = $yearly_income_record->filter(function ($record) use ($i) {
                        if ($record->job_date == null || $record->job_date == "") {
                            return false;
                        }
                        return Carbon::parse($record->job_date)->greaterThanOrEqualTo($i) && Carbon::parse($record->job_date)->lessThanOrEqualTo($i->copy()->endOfYear()) && !$record->is_bank_transaction_completed;
                    })->sum("job_rate");
                    $year_label = $i->format("Y") . "-" . $i->copy()->addYear()->format("Y");
                    $income_chart_paid_data[] = $year_total_paid;
                    $income_chart_unpaid_data[] = $year_total_unpaid;
                    $year_labels[] = $year_label;
                }
                $income_chart_data["labels"] = $year_labels;
                $income_chart_data["data_paid"] = $income_chart_paid_data;
                $income_chart_data["data_unpaid"] = $income_chart_unpaid_data;
            } else {
                for ($i = $user_creation_year_start; $i <= today(); $i->addYear()) {
                    $year_total = $yearly_income_record->filter(function ($record) use ($i) {
                        if ($record->job_date == null || $record->job_date == "") {
                            return false;
                        }
                        return Carbon::parse($record->job_date)->greaterThanOrEqualTo($i) && Carbon::parse($record->job_date)->lessThanOrEqualTo($i->copy()->endOfYear());
                    })->sum("job_rate");
                    $year_label = $i->format("Y") . "-" . $i->copy()->addYear()->format("Y");
                    $income_chart_data[$year_label] = $year_total;
                }
            }
        }
        return $income_chart_data;
    }

    public function get_user_financial_year_start_month(): int
    {
        $finance_year_start_month = 4;
        if ($this->user_financial_year && $this->user_financial_year->month_start && $this->user_financial_year->month_start != "") {
            $finance_year_start_month = $this->user_financial_year->month_start;
        }
        return $finance_year_start_month;
    }

    public function get_user_finance_type(): string
    {
        if ($this->user_financial_year && $this->user_financial_year->user_type) {
            return $this->user_financial_year->user_type;
        }
        return "";
    }

    public function get_user_total_income(int $financial_year = null, int $finance_year_start_month): float
    {
        if ($financial_year) {
            $year_start = get_financial_year_range($finance_year_start_month, $financial_year)["year_start"];
            $year_end = get_financial_year_range($finance_year_start_month, $financial_year)["year_end"];

            $total_income = FinanceIncome::query()->where("freelancer_id", $this->user->id)->whereBetween("job_date", [$year_start, $year_end])->sum("job_rate");
        } else {
            $total_income = FinanceIncome::query()->where("freelancer_id", $this->user->id)->sum("job_rate");
        }
        return $total_income;
    }
    public function get_user_total_expense(int $financial_year = null, int $finance_year_start_month): float
    {
        if ($financial_year) {
            $year_start = get_financial_year_range($finance_year_start_month, $financial_year)["year_start"];
            $year_end = get_financial_year_range($finance_year_start_month, $financial_year)["year_end"];

            $total_expense = FinanceExpense::query()->where("freelancer_id", $this->user->id)->whereBetween("job_date", [$year_start, $year_end])->sum("job_rate");
        } else {
            $total_expense = FinanceExpense::query()->where("freelancer_id", $this->user->id)->sum("job_rate");
        }
        return $total_expense;
    }

    public function getMonthFinancialYear($user_financial_month_start, $year)
    {
        $y0 = $year - 1;
        $y1 = $year;
        $y2 = $year + 1;

        if ($user_financial_month_start == 1) {
            return $y1 . '-' . $y2;
        } elseif ((date('n') >= $user_financial_month_start)) {
            return $y1 . '-' . $y2;
        } else {
            return  $y0 . '-' . $y1;
        }
    }

    /**
     * Calculate tax of user
     * @param int $user_financial_month_start Start of financial year month number from 1 to 12
     * @param float $amount Amount on which tax to apply
     * @param string $user_finance_type User finance type from financial_years table
     * @param int financial_year Financial year for which calculating tax. Like 2022, 2023 etc
     * @return float
     */
    public function user_tax_calculation(int $user_financial_month_start, float $amount = 0, string $user_finance_type = null, int $financial_year = null)
    {

        if ($financial_year == null) {
            $financial_year = date('Y');
        }
        $financeyear = $this->getMonthFinancialYear($user_financial_month_start, $financial_year); //2018-2019
        $tax_data = FinanceTaxRecord::where("finance_year", $financeyear)->first();

        if ($user_finance_type != null && $user_finance_type == 'limitedcompany') {

            if ($tax_data) {
                $taxper = $tax_data->company_limited_tax;
            } else {
                $taxper = '20';
            }
            $totaltax = $amount * $taxper / 100;
            return $totaltax;
        } else {

            /* Normal Tax */
            if ($tax_data) {
                $basicrate_amt      = $tax_data->personal_allowance_rate;
                $higherrate_amt     = $tax_data->basic_rate;
                $additionalrate_amt = $tax_data->higher_rate;

                $basicrate_per      = $tax_data->basic_rate_tax;
                $higherrate_per     = $tax_data->higher_rate_tax;
                $additionalrate_per = $tax_data->additional_rate_tax;
            } else {
                $basicrate_amt      = '11000';
                $higherrate_amt     = '44500';
                $additionalrate_amt = '150000';

                $basicrate_per      = '20';
                $higherrate_per     = '40';
                $additionalrate_per = '45';
            }


            if ($basicrate_amt >= $amount) { // 0% Personal Allowance
                $totaltax = 0;
            } elseif ($basicrate_amt < $amount && $amount <= $higherrate_amt) { // 20% Basic rate
                $val_44500 = $amount - $basicrate_amt; // 20%
                $val_44500_per =  $val_44500 * $basicrate_per / 100;
                $totaltax = $val_44500_per;
            } elseif ($higherrate_amt < $amount && $amount <= $additionalrate_amt) {  // 40% Higher rate
                $val_44500 = $higherrate_amt - $basicrate_amt;    // 20%
                $val_150000 = $amount - $higherrate_amt; // 40%
                $val_44500_per =  $val_44500 * $basicrate_per / 100;
                $val_150000_per =  $val_150000 * $higherrate_per / 100;
                $totaltax = $val_44500_per + $val_150000_per;
            } elseif ($additionalrate_amt < $amount) { // 45% Additional rate
                $val_44500 = $higherrate_amt - $basicrate_amt; // 20%
                $val_150000 = $additionalrate_amt - $higherrate_amt; // 40%
                $val_150000_above = $amount - $additionalrate_amt;
                $val_44500_per =  $val_44500 * $basicrate_per / 100;
                $val_150000_per =  $val_150000 * $higherrate_per / 100;
                $val_150000_above_per = $val_150000_above * $additionalrate_per / 100;
                $totaltax = $val_44500_per + $val_150000_per + $val_150000_above_per;
            }

            /* Ni Tax */
            $niTaxData = FinanceNiTaxRecord::where("finance_year", $financeyear)->first();
            if ($niTaxData) {
                $c4_min_amount_1    = $niTaxData->c4_min_ammount_1; // Nil
                $c4_min_amount_2    = $niTaxData->c4_min_ammount_2; // 9%
                $c4_above_amount_3  = $niTaxData->c4_min_ammount_3; // 2%
                $c2_amount          = $niTaxData->c2_min_amount; // 2.85 % per week of year

                $c4_min_amount_2_tax    = $niTaxData->c4_min_ammount_tax_2;
                $c4_above_amount_3_tax  = $niTaxData->c4_min_ammount_tax_3;
                $c2_amount_tax          = $niTaxData->c2_tax;
            } else {
                $c4_min_amount_1    = '8000'; // Nil
                $c4_min_amount_2    = '45000'; // 9%
                $c4_above_amount_3  = '45001'; // 2%
                $c2_amount          = '6025'; // 2.85 % per week of year

                $c4_min_amount_2_tax    = '2';
                $c4_above_amount_3_tax  = '9';
                $c2_amount_tax          = '148.2';
            }


            if ($c4_min_amount_1 >= $amount) { // 0% Personal Allowance
                $nitotaltax = 0;
            } elseif ($c4_min_amount_1 < $amount && $amount <= $c4_min_amount_2) { // 9%
                $val_45000      = $amount - $c4_min_amount_1; // 9%
                $val_45000_per  = $val_45000 * $c4_min_amount_2_tax / 100;
                $nitotaltax     = $val_45000_per;
            } elseif ($c4_min_amount_2 < $amount) {  // 2%
                $val_45000          = $c4_min_amount_2 - $c4_min_amount_1;    // 9%
                $val_45k_plus       = $amount - $c4_min_amount_2; // 2%
                $val_45000_per      = $val_45000 * $c4_min_amount_2_tax / 100;
                $val_45k_plus_per   = $val_45k_plus * $c4_above_amount_3_tax / 100;
                $nitotaltax         = $val_45000_per + $val_45k_plus_per;
            }

            if ($c2_amount < $amount) { // add yearlly charge
                $nitotaltax = $nitotaltax + $c2_amount_tax;
            }

            $fullTotalTax = $totaltax + $nitotaltax;
            return $fullTotalTax;
        }
    }

    public function get_chart_by_supplier(Carbon $year_start, Carbon $year_end, string $supplier = null): array
    {

        $income_records = FinanceIncome::query()->select(DB::raw("SUM(job_rate) as total_amount"), "supplier")->where("freelancer_id", $this->user->id)->whereBetween("job_date", [$year_start, $year_end]);
        if ($supplier && $supplier != "") {
            $income_records = $income_records->where("supplier", $supplier);
        }
        $income_records = $income_records->groupBy("supplier")->get();
        $chart_data = array();
        $i = 1;
        foreach ($income_records as $income) {
            $color = generate_good_color($i);
            $chart_data[] =   array(
                'value' => floatval($income['total_amount']),
                'color' => "#" . $color,
                'highlight' => "#" . $color,
                'label' => $income['supplier']
            );
            $i += 1;
        }

        return $chart_data;
    }
    public function get_chart_by_location(Carbon $year_start, Carbon $year_end): array
    {
        $income_records = FinanceIncome::query()->select(DB::raw("SUM(job_rate) as total_amount"), "location")->where("freelancer_id", $this->user->id)->whereBetween("job_date", [$year_start, $year_end]);
        $income_records = $income_records->groupBy("location")->get();
        $chart_data = array();
        $i = 0;
        foreach ($income_records as $income) {
            $color = generate_good_color($i);
            $i += 1;
            $chart_data[] =   array(
                'value' => $income['total_amount'],
                'color' => "#" . $color,
                'highlight' => "#" . $color,
                'label' => $income['location']
            );
        }

        return $chart_data;
    }

    public function get_chart_by_income_type(Carbon $year_start, Carbon $year_end, string $income_type = null): array
    {
        $income_records = FinanceIncome::query()->select(DB::raw("SUM(job_rate) as total_amount"), "income_type")->where("freelancer_id", $this->user->id)->whereBetween("job_date", [$year_start, $year_end]);
        if ($income_type && $income_type != "") {
            $income_records = $income_records->where("income_type", $income_type);
        }
        $income_records = $income_records->groupBy("income_type")->get();
        $chart_data = array();
        $i = 0;
        foreach ($income_records as $income) {
            $color = generate_good_color($i);
            $i += 1;
            $chart_data[] =   array(
                'value' => $income['total_amount'],
                'color' => "#" . $color,
                'highlight' => "#" . $color,
                'label' => $income->get_income_type()
            );
        }

        return $chart_data;
    }
    public function get_chart_by_expense_type(Carbon $year_start, Carbon $year_end, string $expense_type_id = null): array
    {
        $expense_records = FinanceExpense::query()->with("expense_type")->select(DB::raw("SUM(job_rate) as total_amount"), "expense_type_id")->where("freelancer_id", $this->user->id)->whereBetween("job_date", [$year_start, $year_end]);
        if ($expense_type_id && $expense_type_id != "") {
            $expense_records = $expense_records->where("expense_type_id", $expense_type_id);
        }
        $expense_records = $expense_records->groupBy("expense_type_id")->get();
        $chart_data = array();
        $i = 0;
        foreach ($expense_records as $expense) {
            $color = generate_good_color($i);
            $i += 1;
            $chart_data[] =   array(
                'value' => $expense['total_amount'],
                'color' => "#" . $color,
                'highlight' => "#" . $color,
                'label' => $expense->expense_type->expense
            );
        }

        return $chart_data;
    }

    public function get_employer_finance_cost_chart_data(Carbon $year_start, Carbon $year_end): array
    {
        $chart_year_start = $year_start->copy();
        $chart_year_end = $year_end->copy();
        if ($year_end->greaterThan(today()->endOfMonth())) {
            $chart_year_end = today()->endOfMonth();
        }
        $employer_fonance_monthly = FinanceEmployer::select(DB::raw("DATE_FORMAT(job_date, '%b') as month, SUM(job_rate) as job_rate_month_total"))->where("employer_id", $this->user->id)->whereBetween("job_date", [$year_start, $year_end])->groupBy("month")->get();
        $finance_chart_data = array();
        for ($i = $chart_year_start; $i <= $chart_year_end; $i->addMonth()) {
            $month_total = $employer_fonance_monthly->first(function ($record) use ($i) {
                return $i->format("M") == $record->month;
            })?->job_rate_month_total ?? 0;
            $finance_chart_data[$i->format("M,y")] = floatval($month_total);
        }
        return $finance_chart_data;
    }
    public function get_employer_finance_jobs_chart_data(Carbon $year_start, Carbon $year_end): array
    {
        $chart_year_start = $year_start->copy();
        $chart_year_end = $year_end->copy();
        if ($year_end->greaterThan(today()->endOfMonth())) {
            $chart_year_end = today()->endOfMonth();
        }
        $employer_fonance_monthly = FinanceEmployer::select(DB::raw("DATE_FORMAT(job_date, '%b') as month, COUNT(id) as jobs_count_month_total"))->where("employer_id", $this->user->id)->whereBetween("job_date", [$year_start, $year_end])->groupBy("month")->get();
        $finance_chart_data = array();
        for ($i = $chart_year_start; $i <= $chart_year_end; $i->addMonth()) {
            $month_total = $employer_fonance_monthly->first(function ($record) use ($i) {
                return $i->format("M") == $record->month;
            })?->jobs_count_month_total ?? 0;
            $finance_chart_data[$i->format("M,y")] = floatval($month_total);
        }
        return $finance_chart_data;
    }
}
