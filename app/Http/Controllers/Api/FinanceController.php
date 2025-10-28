<?php

namespace App\Http\Controllers\Api;

use App\Helpers\FinanceHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\FinanceEmployerResource;
use App\Http\Resources\Api\FinanceTransactionResource;
use App\Http\Resources\Api\SupplierResource;
use App\Http\Resources\UserExtendedResource;
use App\Mail\IncomeInvoiceMail;
use App\Models\ExpenseType;
use App\Models\FinanceEmployer;
use App\Models\FinanceExpense;
use App\Models\FinanceIncome;
use App\Models\Invoice;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserBankDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class FinanceController extends Controller
{
    public function finance(Request $request)
    {
        $user_id     = $request['user_id'];
        $role_id     = $request['role_id'];
        $page_id     = $request['page_id'];
        $user_data = $request->all();
        $finance_records = response()->error('Not found');
        switch ($page_id) {
            case 'all-transaction':
                $show         = isset($user_data['show_type']) ? $user_data['show_type'] : "";
                $sort_by     = isset($user_data['sort_by']) ? $user_data['sort_by'] : 'job_date';
                $finance_records = $this->getAllTransaction($user_id, $sort_by, $show);
                break;
            case 'emp-all-transaction':
                $paid = isset($user_data['paid']) ? $user_data['paid'] : null;
                $paid_date = isset($user_data['paid_date']) ? $user_data['paid_date'] : null;
                $trans_id = isset($user_data['trans_id']) ? $user_data['trans_id'] : null;
                $sort_by = isset($user_data['sort_by']) ? $user_data['sort_by'] : 'job_date';
                $finance_records = $this->getEmpAllTransaction($user_id, $sort_by, $trans_id, $paid, $paid_date);
                break;
            case 'update-transaction':
                $trans_id     = isset($user_data['trans_id']) ? $user_data['trans_id'] : "";
                $trans_date = isset($user_data['trans_date']) ? $user_data['trans_date'] : "";
                $trans_type = isset($user_data['trans_type']) ? $user_data['trans_type'] : "";
                $show         = isset($user_data['show_type']) ? $user_data['show_type'] : "";
                $action     = isset($user_data['action']) ? $user_data['action'] : '';
                $this->updateTransaction($trans_id, $trans_date, $trans_type);
                if ($action == "income-by-area") {
                    $finance_records = $this->get_income_by_area($user_id);
                } elseif ($action == "income-by-category") {
                    $filter_cat = isset($user_data['filter_cat']) ? $user_data['filter_cat'] : '';
                    $finance_records = $this->get_income_by_category($user_id, $filter_cat);
                } elseif ($action == "expense-by-category") {
                    $filter_cat = isset($user_data['filter_cat']) ? $user_data['filter_cat'] : '';
                    $finance_records = $this->get_expense_by_category($user_id, $filter_cat);
                } elseif ($action == "income-by-suplier") {
                    $filter_cat = isset($user_data['filter_cat']) ? $user_data['filter_cat'] : '';
                    $finance_records = $this->get_income_by_suplier($user_id, $filter_cat);
                } elseif ($action == "open-invoice") {
                    $invoice_status = isset($user_data['invoice_status']) ? $user_data['invoice_status'] : '';
                    $invoice_job_id = isset($user_data['invoice_job_id']) ? $user_data['invoice_job_id'] : '';
                    $finance_records = $this->get_open_invoices($user_id, $invoice_status, $invoice_job_id);
                } else {
                    $finance_records = $this->getAllTransaction($user_id, null, $show);
                }
                break;
            case 'cash-movements':
                $finance_records = $this->getCashMovements($user_id);
                break;
            case 'weekly-report':
                $finance_records = $this->getWeeklyReport($user_id);
                break;
            case 'finanace-summary':
                $finance_records = $this->get_user_finance_summary($user_id);
                break;
            case 'income-by-area':
                $finance_records = $this->get_income_by_area($user_id);
                break;
            case 'income-by-category':
                $filter_cat = isset($user_data['filter_cat']) ? $user_data['filter_cat'] : '';
                $finance_records = $this->get_income_by_category($user_id, $filter_cat);
                break;
            case 'income-by-suplier':
                $filter_cat = isset($user_data['filter_cat']) ? $user_data['filter_cat'] : '';
                $finance_records = $this->get_income_by_suplier($user_id, $filter_cat);
                break;
            case 'expense-by-category':
                $filter_cat = isset($user_data['filter_cat']) ? $user_data['filter_cat'] : '';
                $finance_records = $this->get_expense_by_category($user_id, $filter_cat);
                break;
            case 'net-income':
                $finance_records = $this->get_net_income($user_id);
                break;
            case 'open-invoices':
                $finance_records = $this->get_open_invoices($user_id);
                break;
            case 'send-invoice':
                $income_id = isset($user_data['income_id']) ? $user_data['income_id'] : '';
                $send = isset($user_data['send']) ? $user_data['send'] : '';
                $data = isset($user_data['data']) ? $user_data['data'] : '';
                $finance_records = $this->send_invoice($user_id, $income_id, $send, $data);
                break;
            case 'get-store-list':
                $finance_records = $this->get_supplier_store_list($user_data);
                break;
            case 'get-bank-detail':
                $finance_records = $this->get_bank_details($user_data);
                break;
            case 'set-bank-detail':
                $finance_records = $this->set_bank_details($user_data);
                break;
        }
        return $finance_records;
    }

    private function getAllTransaction($user_id, $sort_by, $show = null)
    {
        $finance_helper = new FinanceHelper(User::findOrFail($user_id));
        $financialYear = $finance_helper->get_user_financial_year_start_month();
        $filter_year = date('Y');
        $sort_by = in_array($sort_by, ["job_date", "job_rate", "job_id", "trans_id"]) ? $sort_by : "created_at";
        $sort_by = $sort_by == "trans_id" ? "id" : $sort_by;

        $year_start = get_financial_year_range($financialYear, $filter_year)["year_start"];
        $year_end = get_financial_year_range($financialYear, $filter_year)["year_end"];
        
        if ($show == "income") {
            $records = FinanceIncome::query()->where("freelancer_id", $user_id)->whereBetween("job_date", [$year_start, $year_end])->orderBy($sort_by, "DESC")->get();
        } else if ($show == "expense") {
            $records = FinanceExpense::query()->where("freelancer_id", $user_id)->whereBetween("job_date", [$year_start, $year_end])->orderBy($sort_by, "DESC")->get();
        } else {
            // $income_records = FinanceIncome::query()->where("freelancer_id", $user_id)->whereBetween("job_date", [$year_start, $year_end])->orderBy($sort_by, "DESC")->get();
            // $expense_records = FinanceExpense::query()->where("freelancer_id", $user_id)->whereBetween("job_date", [$year_start, $year_end])->orderBy($sort_by, "DESC")->get();
            $income_records = FinanceIncome::query()->where("freelancer_id", $user_id)->orderBy($sort_by, "DESC")->get();
            $expense_records = FinanceExpense::query()->where("freelancer_id", $user_id)->orderBy($sort_by, "DESC")->get();
            $records = $income_records->concat($expense_records)->sortBy([$sort_by => "desc"]);
        }
        
        return response()->success(FinanceTransactionResource::collection($records)->jsonSerialize());
    }

    private function getEmpAllTransaction($user_id, $sort_by, $trans_id = null, $paid = null, $paid_date = null)
    {
        $sort_by = in_array($sort_by, ["job_date", "total", "job_rate", "bonus"]) ? $sort_by : "created_at";
        $sort_by = $sort_by == "total" ? "job_rate" : $sort_by;

        $finance_helper = new FinanceHelper(User::findOrFail($user_id));
        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $filter_year = date('Y');

        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];

        $transactions = FinanceEmployer::query()->where("employer_id", $user_id)->whereBetween("job_date", [$year_start, $year_end])->orderBy($sort_by, "DESC")->get();

        if ($paid & $paid == 1) {
            $transaction = FinanceEmployer::findOrFail($trans_id);
            $transaction->is_paid = $paid == 0 ? false : true;
            $transaction->paid_date = $paid_date;
            $transaction->save();
        }

        return response()->success(FinanceEmployerResource::collection($transactions)->jsonSerialize());
    }

    private function updateTransaction($trans_id, $trans_date, $trans_type)
    {
        if ($trans_type == 1 || $trans_type == 'Income') {
            $income = FinanceIncome::findOrFail($trans_id);
            $income->is_bank_transaction_completed = true;
            $income->bank_transaction_date = $trans_date;
            $income->save();
        } elseif ($trans_type == 2 || $trans_type == 'Expense') {
            $expense = FinanceExpense::findOrFail($trans_id);
            $expense->is_bank_transaction_completed = true;
            $expense->bank_transaction_date = $trans_date;
            $expense->save();
        }
    }

    public function get_income_by_area($user_id)
    {
        $filter_year = date('Y');
        $filter = 'month';

        $finance_helper = new FinanceHelper(User::findOrFail($user_id));
        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();

        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];
        $pie_location_chart_data = $finance_helper->get_chart_by_location($year_start, $year_end);

        $income_records = FinanceIncome::query()->where("freelancer_id", $user_id)->whereBetween("job_date", [$year_start, $year_end]);
        $income_records = $income_records->get();

        $income_by_area_report = array(
            'chart'         => array(
                'label' => extract_values_by_key_from_multiarray($pie_location_chart_data, "label"),
                'data'     => extract_values_by_key_from_multiarray($pie_location_chart_data, "value"),
                'color' => extract_values_by_key_from_multiarray($pie_location_chart_data, "color"),
            ),
            'data'            => FinanceTransactionResource::collection($income_records)->jsonSerialize(),
            'finance_year'    => get_financial_year_range_string($finance_year_start_month)
        );
        return response()->json($income_by_area_report);
    }

    public function get_income_by_category($user_id, $filter_cat)
    {
        if (!$filter_cat) {
            $filter_cat = null;
        }
        $filter_year = date('Y');
        $finance_helper = new FinanceHelper(User::findOrFail($user_id));
        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $income_filter = 'month';

        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];
        $categories = FinanceIncome::get_income_type_categories_list();
        // return $categories;

        $pie_category_chart_data = $finance_helper->get_chart_by_income_type($year_start, $year_end, $filter_cat);
        $income_records = FinanceIncome::query()->where("freelancer_id", $user_id)->whereBetween("job_date", [$year_start, $year_end]);
        // $income_records = FinanceIncome::query()->where("freelancer_id", $user_id);
        if ($filter_cat) {
            $income_records = $income_records->where("income_type", $filter_cat);
        }
        $income_records = $income_records->get();

        $categories = array_map(function ($key, $category) {
            return ['cat_id' => $key, 'cat' => $category];
        }, array_keys($categories), array_values($categories));
        array_unshift($categories, [
            'cat_id' => 0,
            'cat' => 'All'
        ]);
        $income_by_cat = array(
            'category' => $categories,
            'data' => FinanceTransactionResource::collection($income_records)->jsonSerialize(),
            'chart' => array(
                'label' => extract_values_by_key_from_multiarray($pie_category_chart_data, "label"),
                'data' => extract_values_by_key_from_multiarray($pie_category_chart_data, "value"),
                'color' => extract_values_by_key_from_multiarray($pie_category_chart_data, "color"),
            ),
            'finance_year'    => get_financial_year_range_string($finance_year_start_month)
        );
        // dd($income_by_cat , 'here');

        return response()->json($income_by_cat);
    }

    public function get_expense_by_category($user_id, $category_filter = null)
    {
        $finance_helper = new FinanceHelper(User::findOrFail($user_id));
        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $filter_year = date('Y');

        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];
        $categories = ExpenseType::all();

        $pie_category_chart_data = $finance_helper->get_chart_by_expense_type($year_start, $year_end, $category_filter);

        $expense_records = FinanceExpense::query()->where("freelancer_id", $user_id)->whereBetween("job_date", [$year_start, $year_end]);
        if ($category_filter && $category_filter != "") {
            $expense_records = $expense_records->where("expense_type_id", $category_filter);
        }
        $expense_records = $expense_records->get();

        $categories = $categories->map(function ($category) {
            return [
                "id" => $category->id,
                "cat" => $category->expense,
                "expense_colour" => $category->expense_colour
            ];
        })->toArray();
        array_unshift($categories, [
            "id" => 0,
            "cat" => 'All',
            "expense_colour" => '#FFFFFF'
        ]);

        $expense_by_category_data = array(
            'category' => $categories,
            'data' => FinanceTransactionResource::collection($expense_records)->jsonSerialize(),
            'chart' => array(
                'label' => extract_values_by_key_from_multiarray($pie_category_chart_data, "label"),
                'data' => extract_values_by_key_from_multiarray($pie_category_chart_data, "value"),
                'color' => extract_values_by_key_from_multiarray($pie_category_chart_data, "color"),
            ),
            'finance_year'    => get_financial_year_range_string($finance_year_start_month)
        );
        return response()->json($expense_by_category_data);
    }

    public function get_net_income($user_id)
    {
        $filter_year = date('Y');
        $filter = 'month';
        $finance_helper = new FinanceHelper(User::findOrFail($user_id));

        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $user_finance_type = $finance_helper->get_user_finance_type();

        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];
        $income_chart_data = $finance_helper->get_chart_finance_data(FinanceIncome::query(), $year_start, $year_end, $finance_year_start_month, $filter, false, true);
        $expense_chart_data = $finance_helper->get_chart_finance_data(FinanceExpense::query(), $year_start, $year_end, $finance_year_start_month, $filter, false, true);

        $net_income_label = array();
        $net_income_data = array();
        $net_income_chart_data = array();
        foreach ($income_chart_data as $key => $value) {
            $net_income_chart_data[] = $value - $expense_chart_data[$key];
            $net_income_data[] = set_amount_format($value - $expense_chart_data[$key]);
            $net_income_label[] = $key;
        }

        $total_income = $finance_helper->get_user_total_income($filter_year, $finance_year_start_month);
        $total_expense = $finance_helper->get_user_total_expense($filter_year, $finance_year_start_month);
        $user_total_tax = $finance_helper->user_tax_calculation($finance_year_start_month, $total_income - $total_expense, $user_finance_type, $filter_year);

        $finance_summary = array();
        $finance_summary['income']     = set_amount_format($total_income);
        $finance_summary['expence'] = set_amount_format($total_expense);
        $finance_summary['profit']     = set_amount_format($total_income - $total_expense);
        $finance_summary['tax']     = set_amount_format($user_total_tax);
        $finance_summary['finance_year'] = get_financial_current_year($finance_year_start_month);
        $net_income_record  = array(
            'data'         => $net_income_data,
            'chart_data' => $net_income_chart_data,
            'x'        => $net_income_label,
            'y' => $net_income_chart_data,
            'label'        => $net_income_label,
            'summary'    => $finance_summary
        );

        return response()->json($net_income_record);
    }

    public function get_income_by_suplier($user_id, $supplier_filter = null)
    {
        $filter_year = date('Y');
        $finance_helper = new FinanceHelper(User::findOrFail($user_id));
        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];
        $suppliers = FinanceIncome::query()->where("freelancer_id", $user_id)->whereNotNull("supplier")->where("supplier", "!=", "")->select("supplier")->distinct()->pluck("supplier")->toArray();
        array_unshift($suppliers, 'All');
        $pie_supplier_chart_data = $finance_helper->get_chart_by_supplier($year_start, $year_end, $supplier_filter);

        $income_records = FinanceIncome::query()->where("freelancer_id", $user_id)->whereBetween("job_date", [$year_start, $year_end]);
        if ($supplier_filter && $supplier_filter != "") {
            $income_records = $income_records->where("supplier", $supplier_filter);
        }
        $income_records = $income_records->get();

        $chart_data = array();
        $chart_data['label'] = extract_values_by_key_from_multiarray($pie_supplier_chart_data, "label");
        $chart_data['data']  = extract_values_by_key_from_multiarray($pie_supplier_chart_data, "value");
        $chart_data['color'] = extract_values_by_key_from_multiarray($pie_supplier_chart_data, "color");

        $income_by_suplier = array(
            'suplier'         => $suppliers,
            'data'            => FinanceTransactionResource::collection($income_records)->jsonSerialize(),
            'chart_data'    => $chart_data,
            'finance_year'    => get_financial_year_range_string($finance_year_start_month)
        );

        return response()->json($income_by_suplier);
    }

    public function get_open_invoices($user_id, $status = null, $id = null)
    {
        $financialYear = date('Y');
        $finance_helper = new FinanceHelper(User::findOrFail($user_id));
        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        if ($status || $status == 0) {
            FinanceIncome::where("id", $id)->update([
                "is_invoice_required" => $status == 1 ? false : true
            ]);
        }
        $records = FinanceIncome::query()->where("freelancer_id", $user_id)->where("is_bank_transaction_completed", false)->orderBy("created_at", "DESC")->get();
        $incomeRecord = FinanceTransactionResource::collection($records)->jsonSerialize();
        $i30 = $i60 = $i90 = $i90plus = 0;
        foreach ($incomeRecord as $key => $income) {
            $diffDay  = get_relative_days_for_job($income["job_date"]);
            $incomeRecord[$key]['financial_year'] = ($financialYear - 1) . '-' . $financialYear;
            if (@$diffDay < 30) {
                $i30++;
                $incomeRecord[$key]['zero_30'] = 1;
                $incomeRecord[$key]['between_30_60'] = 0;
                $incomeRecord[$key]['between_60_90'] = 0;
                $incomeRecord[$key]['above_90'] = 0;
            } elseif (@$diffDay >= 30 && @$diffDay < 60) {
                $i60++;
                $incomeRecord[$key]['zero_30'] = 0;
                $incomeRecord[$key]['between_30_60'] = 1;
                $incomeRecord[$key]['between_60_90'] = 0;
                $incomeRecord[$key]['above_90'] = 0;
            } elseif (@$diffDay >= 60 && @$diffDay < 90) {
                $i90++;
                $incomeRecord[$key]['zero_30'] = 0;
                $incomeRecord[$key]['between_30_60'] = 0;
                $incomeRecord[$key]['between_60_90'] = 1;
                $incomeRecord[$key]['above_90'] = 0;
            } else {
                $i90plus++;
                $incomeRecord[$key]['zero_30'] = 0;
                $incomeRecord[$key]['between_30_60'] = 0;
                $incomeRecord[$key]['between_60_90'] = 0;
                $incomeRecord[$key]['above_90'] = 1;
            }
        }
        $invoiceChartRecord  = array($i30, $i60, $i90, $i90plus);
        $invoice_records = array(
            'data'             => $incomeRecord,
            'chart'            => $invoiceChartRecord,
            'x'            => ["0-30", "31-60", "61-90", "90+"],
            'y'            => $invoiceChartRecord,
            'finance_year'    => get_financial_year_range_string($finance_year_start_month)
        );
        return response()->json($invoice_records);
    }
    public function getCashMovements($user_id)
    {
        $movement_report = array();
        $filter_year = date('Y');
        $filter = 'month';
        $finance_helper = new FinanceHelper(User::findOrFail($user_id));

        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $user_finance_type = $finance_helper->get_user_finance_type();

        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];
        $income_chart_data = $finance_helper->get_chart_finance_data(FinanceIncome::query(), $year_start, $year_end, $finance_year_start_month, $filter, false, true);
        $expense_chart_data = $finance_helper->get_chart_finance_data(FinanceExpense::query(), $year_start, $year_end, $finance_year_start_month, $filter, false, true);

        $net_income_label = array();
        $net_income_data = array();
        foreach ($income_chart_data as $key => $value) {
            $net_income_data[] = $value - $expense_chart_data[$key];
            $net_income_label[] = $key;
        }
        $movement_report['chart_data'] = array(
            'label' => $net_income_label,
            'data'     => $net_income_data
        );

        $income_records = FinanceIncome::query()->where("freelancer_id", $user_id)->where("is_bank_transaction_completed", true)->whereBetween("job_date", [$year_start, $year_end])->get();
        $expense_records = FinanceExpense::query()->where("freelancer_id", $user_id)->where("is_bank_transaction_completed", true)->whereBetween("job_date", [$year_start, $year_end])->get();

        $all_transactions = $income_records->concat($expense_records)->sortBy(["job_date" => "desc"]);

        $total_income = $finance_helper->get_user_total_income($filter_year, $finance_year_start_month);
        $total_expense = $finance_helper->get_user_total_expense($filter_year, $finance_year_start_month);
        $user_total_tax = $finance_helper->user_tax_calculation($finance_year_start_month, $total_income - $total_expense, $user_finance_type, $filter_year);

        $finance_summary = array();
        $finance_summary['income']     = set_amount_format($total_income);
        $finance_summary['expence'] = set_amount_format($total_expense);
        $finance_summary['profit']     = set_amount_format($total_income - $total_expense);
        $finance_summary['tax']     = set_amount_format($user_total_tax);
        $finance_summary['finance_year'] = get_financial_current_year($finance_year_start_month);

        $movement_report['finance_sumamry'] = $finance_summary;
        $movement_report['records'] = FinanceTransactionResource::collection($all_transactions)->jsonSerialize();

        return response()->json($movement_report);
    }

    public function getWeeklyReport($user_id)
    {
        $finance_helper = new FinanceHelper(User::findOrFail($user_id));

        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $filter_year = date('Y');
        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];
        $incomeWeekchart['labels'] = array_keys(get_abbrevated_days_list());
        $incomeWeekchart['bank_yes'] = get_abbrevated_days_list();
        $incomeWeekchart['bank_no'] = get_abbrevated_days_list();

        $job_count_by_day_data = get_abbrevated_days_list();
        $income_records = FinanceIncome::query()->select(DB::raw("SUM(job_rate) as total_amount"), DB::raw("DATE_FORMAT(job_date, '%a') as day"), DB::raw("COUNT(id) as job_count"), "is_bank_transaction_completed")->where("freelancer_id", $user_id)->whereBetween("job_date", [$year_start, $year_end]);
        $income_records = $income_records->groupBy("day")->groupBy("is_bank_transaction_completed")->get();
        $incomeWeek = get_abbrevated_days_list();
        $jobWeek = get_abbrevated_days_list();
        foreach ($income_records as $record) {
            if ($record->is_bank_transaction_completed) {
                $incomeWeekchart['bank_yes'][$record->day]  += $record->total_amount;
                $incomeWeek[$record->day]  += $record->total_amount;
            } else {
                $incomeWeekchart['bank_no'][$record->day]  += $record->total_amount;
            }
            $job_count_by_day_data[$record->day]  += $record->job_count;
            $jobWeek[$record->day] += $record->job_count;
        }
        $incomeWeekchart['bank_yes'] = array_values($incomeWeekchart['bank_yes']);
        $incomeWeekchart['data'] = array_values($incomeWeekchart['bank_yes']);
        $incomeWeekchart['bank_no'] = array_values($incomeWeekchart['bank_no']);
        $incomeWeek = array_map(function ($key, $value) {
            return ["day" => $key, "bank_yes" => $value];
        }, array_keys($incomeWeek), array_values($incomeWeek));
        $jobWeek = array_map(function ($key, $value) {
            return ["day" => $key, "jobs" => $value];
        }, array_keys($jobWeek), array_values($jobWeek));

        $jobWeekchart = [
            "labels" => array_keys($job_count_by_day_data),
            "data" => array_values($job_count_by_day_data)
        ];

        $weekly_report = array(
            'incomeWeekchart'     => $incomeWeekchart,
            'incomeWeek'        => $incomeWeek,
            'jobWeekchart'        => $jobWeekchart,
            'jobWeek'            => $jobWeek,
            'finance_year'        => get_financial_year_range_string($finance_year_start_month)
        );

        return response()->json($weekly_report);
    }

    private function get_user_finance_summary($user_id)
    {
        $filter_year = date('Y');
        $finance_helper = new FinanceHelper(User::findOrFail($user_id));

        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $user_finance_type = $finance_helper->get_user_finance_type();

        $total_income = $finance_helper->get_user_total_income($filter_year, $finance_year_start_month);
        $total_expense = $finance_helper->get_user_total_expense($filter_year, $finance_year_start_month);
        $user_total_tax = $finance_helper->user_tax_calculation($finance_year_start_month, $total_income - $total_expense, $user_finance_type, $filter_year);

        $finance_summary = array();
        $finance_summary['income']     = set_amount_format($total_income);
        $finance_summary['expence'] = set_amount_format($total_expense);
        $finance_summary['profit']     = set_amount_format($total_income - $total_expense);
        $finance_summary['tax']     = set_amount_format($user_total_tax);
        $finance_summary['finance_year'] = get_financial_current_year($finance_year_start_month);

        return response()->json($finance_summary);
    }

    public function send_invoice($user_id, $income_id, $send = null, $data = null)
    {
        $finance_income = FinanceIncome::where("id", $income_id)->where("freelancer_id", $user_id)->first();
        $user = User::findOrFail($user_id);
        if (!$finance_income) {
            return response()->error('Finance income record not found');
        }

        if ($send) {
            $supplier_email = $data['supplier_email'];
            $admin_mail = config('app.admin_mail');
            $job_rate = $finance_income->job_rate;

            $invoice = Invoice::create([
                "to_email" => $supplier_email,
                "from_email" => $admin_mail,
                "amount" => $job_rate,
                "user_id" => $user_id,
            ]);

            $template_data = [
                "job_id" => $finance_income->job_id,
                "job_date" => $finance_income->job_date,
                "job_rate" => $finance_income->job_rate,
                "your_email" => $user->email,
                "your_name" => $user->firstname . ' ' . $user->lastname,
                "your_address" => $user->user_extra_info->address,
                "your_contact" => $user->user_extra_info->mobile ?? $user->user_extra_info->telephone,
                "supplier_store" => $finance_income->job_id,
                "supplier_id" => isset($data["supplier_id"]) ? $data["supplier_id"] : "N/A",
                "supplier_name" => isset($data["supplier_name"]) ? $data["supplier_name"] : "N/A",
                "supplier_email" => isset($data["supplier_email"]) ? $data["supplier_email"] : "N/A",
                "supplier_address" => isset($data["supplier_address"]) ? $data["supplier_address"] : "N/A",
                "supplier_town" => isset($data["supplier_town"]) ? $data["supplier_town"] : "N/A",
                "supplier_country" => isset($data["supplier_country"]) ? $data["supplier_country"] : "N/A",
                "supplier_postcode" => isset($data["supplier_postcode"]) ? $data["supplier_postcode"] : "N/A",
                "acc_name" => isset($data["supplier_account_name"]) ? $data["supplier_account_name"] : "N/A",
                "acc_number" => isset($data["supplier_account_no"]) ? $data["supplier_account_no"] : "N/A",
                "acc_sort_code" => isset($data["supplier_account_sortcode"]) ? $data["supplier_account_sortcode"] : "N/A",
            ];
            $data["invoice_no"] = $invoice->id;
            $template_data["invoice_no"] = $invoice->id;

            $invoice_file_name = "user-invoice-{$invoice->id}-" . time() . "-ganerated.pdf";
            $template = isset($data["template"]) ? $data["template"] : "invoice1";

            $data = $template_data;
            $invoice_html = view("components.invoice-templates.{$template}", compact('data'))->render();
            try {
                $pdf = Pdf::loadView("components.invoice-templates.layout", ["html" => $invoice_html]);
                $pdf->save(storage_path("app/invoices/{$invoice_file_name}"));
            } catch (Exception) {
                $invoice->delete();
                return response()->error('Pdf generation error. Please try again!');
            }
            $pdf_generated_file = storage_path("app/invoices/{$invoice_file_name}");

            if (!file_exists($pdf_generated_file)) {
                $invoice->delete();
                return response()->error('Pdf generation error. Please try again!');
            }

            $invoice->pdf_file_path = $pdf_generated_file;
            $invoice->save();

            $sent = Mail::to($supplier_email)->send(new IncomeInvoiceMail($pdf_generated_file, $data));
            if ($sent) {
                $finance_income->invoice_id = $invoice->id;
                $finance_income->is_invoice_required = true;
                $finance_income->save();
            } else {
                return response()->error('Email sent error. Please try again!');
            }

            return response()->success([], 'Income invoice sent successfully to supplier.');
        }
        return response()->json([
            'success' => true,
            'message' => 'No invoice has been sent.',
            'data' => (new UserExtendedResource($user))->jsonSerialize(),
        ]);
    }

    public function get_supplier_store_list($user_data)
    {
        $uid = $user_data['user_id'];
        $store_name = $user_data['store_name'];
        $suppliers = Supplier::where("created_by_user_id", $uid)->where("store_name", "like", "%{$store_name}%")->get();
        return response()->success(SupplierResource::collection($suppliers)->jsonSerialize());
    }

    public function set_bank_details($user_data)
    {
        $user_id = $user_data['user_id'];
        $record = UserBankDetail::updateOrCreate(["user_id" => $user_id], [
            "acccount_name" => $user_data['data']["supplier_account_name"],
            "acccount_number" => $user_data['data']["supplier_account_no"],
            "acccount_sort_code" => $user_data['data']["supplier_account_sortcode"],
        ]);
        return response()->success([
            'record_id' => $record->id,
            'user_bank_detail' => [
                "bank_id" => $record->id,
                "user_id" => $record->user_id,
                "acc_name" => $record->acccount_name,
                "acc_number" => $record->acccount_number,
                "acc_sort_code" => $record->acccount_sort_code,
                "created_at" => $record->created_at->toDateTimeString(),
            ]
        ], 'Bank record updated successfully');
    }

    public function get_bank_details($user_data)
    {
        $record = UserBankDetail::where("user_id", $user_data['user_id'])->first();
        if ($record) {
            return response()->success([
                "bank_id" => $record->id,
                "user_id" => $record->user_id,
                "acc_name" => $record->acccount_name,
                "acc_number" => $record->acccount_number,
                "acc_sort_code" => $record->acccount_sort_code,
                "created_at" => $record->created_at->toDateTimeString(),
            ]);
        }
        return response()->error('Record not found');
    }
}
