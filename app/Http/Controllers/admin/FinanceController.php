<?php

namespace App\Http\Controllers\admin;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Supplier;
use Illuminate\Http\Request;
use App\Models\FinanceIncome;
use App\Models\FinanceTaxRecord;
use App\Models\FinanceNiTaxRecord;
use App\Models\UserAclProfession;
use App\Http\Controllers\Controller;
use App\Models\Finance;
use App\Models\FinanceBalanceSheet;
use App\Models\FinanceExpense;
use App\Models\FinanceProfitLoss;
use App\Models\FinancialYear;
use App\Models\userAclPermisssion;
use Beta\Microsoft\Graph\Model\ComanagedDevicesSummary;
use Beta\Microsoft\Graph\Model\Status;
use Illuminate\Routing\Route;
use Maatwebsite\Excel\Concerns\ToArray;

use function PHPUnit\Framework\isFalse;
use function PHPUnit\Framework\isTrue;

class FinanceController extends Controller
{

    public $year, $profession, $professionslist;

    public function __construct(Request $request)
    {

        $this->year = $request->input('y', 2023);
        $this->profession = $request->input('c', null);
        $this->professionslist = UserAclProfession::where('is_active', 1)->get();
    }








    //     public function index(){
    // Assuming you have a User model, you can use a query to filter users.
    // $usersQuery = User::query();


    public function index()
    {
        // Get the current year
        $year = date('Y');

        // Get the profession category ID (you may get it from the request, for example)
        $catId = 3; // Set to your desired category ID

        // Retrieve users who meet the financial income criteria
        $filteredUsers = User::where('user_acl_role_id', User::USER_ROLE_LOCUM) // Assuming 2 is the role for freelancers
            ->where('active', 1)
            ->with(['financeIncomes' => function ($query) use ($year) {
                $query->whereYear('job_date', $year);
            }])
            ->get();

        // Filter users based on the category and financial income criteria
        $filteredUsers = $filteredUsers->filter(function ($user) use ($catId) {
            return $user->user_acl_profession_id == $catId && !$user->financeIncomes->isEmpty();
        });

        return view('admin.finance.index', ['filteredUsers' => $filteredUsers]);
    }





    // public function index()
    // {
    //     ///  freelancer list first
    //     $year = $this->year;
    //     $freelancer = User::where(['user_acl_role_id' => 2, 'active' => 1])->latest()->get();
    //     $year = date('Y');
    //     $currentMonth = date('n');
    //     $startMonth = 4; // Adjust this as needed

    //     $userModel = new User(); // Instantiate the User model
    //     return  $users = $userModel->getUsersWithFinancialCriteria($year, $currentMonth, $startMonth);

    //     // Now, the $users collection contains users who meet the financial income criteria

    //     return view('admin.dashboard', ['users' => $users]);
    //     // SELECT freelancer_id
    //     // FROM finance_incomes
    //     // WHERE
    //     //   (
    //     //     YEAR(job_date) = :year
    //     //     AND freelancer_id = :uid
    //     //   )
    //     //   OR (
    //     //     (
    //     //       YEAR(job_date) = :year
    //     //       OR YEAR(job_date) = :nextYear
    //     //     )
    //     //     AND freelancer_id = :uid
    //     //     AND :currentMonth >= :startMonth
    //     //   )
    //     //   OR (
    //     //     (
    //     //       YEAR(job_date) = :year
    //     //       OR YEAR(job_date) = :prevYear
    //     //     )
    //     //     AND freelancer_id = :uid
    //     //     AND :currentMonth < :startMonth
    //     //   )
    //     //   OR (
    //     //     YEAR(job_date) = :year
    //     //   )
    //     // GROUP BY freelancer_id;



    //     // Assuming you have a User model, you can use a query to filter users.
    //     // $usersQuery = User::query();

    //     // if ($this->role) {
    //     //     $usersQuery->whereHas('role', function ($query) {
    //     //         $query->where('name', $this->role);
    //     //     });
    //     // }

    //     // if ($this->profession) {
    //     //     $usersQuery->where('user_acl_profession_id', $this->profession);
    //     // }

    //     // // Paginate the results
    //     // $users = $usersQuery->paginate(1);

    //     return view('admin.finance.index');
    // }

    // // Paginate the results
    // $users = $usersQuery->paginate(1);

    //         return view('admin.finance.index');
    //     }

    public function listSupplier()
    {
        $suppliers = Supplier::all();
        return view('admin.finance.allSuppliers', compact('suppliers'));
    }
    
    public function isDateBetween($checkDate, $startDate, $endDate) {
        $checkDate = Carbon::createFromFormat("H:i:s d:m:Y", $checkDate);
        $startDate = Carbon::createFromFormat("H:i:s d:m:Y", $startDate);
        $endDate = Carbon::createFromFormat("H:i:s d:m:Y", $endDate);

        return $checkDate->between($startDate, $endDate);
    }
    public function setMonth($month){
        if($month == 1){
            return 'Jan';
        }
        elseif($month == 2){
            return 'Feb';
        }
        elseif($month == 3){
            return 'Mar';
        }
        elseif($month == 4){
            return 'Apr';
        }
        elseif($month == 5){
            return 'May';
        }
        elseif($month == 6){
            return 'Jun';
        }
        elseif($month == 7){
            return 'Jul';
        }
        elseif($month == 8){
            return 'Aug';
        }
        elseif($month == 9){
            return 'Sep';
        }
        elseif($month == 10){
            return 'Oct';
        }
        elseif($month == 11){
            return 'Nov';
        }
        elseif($month == 12){
            return 'Dec';
        }
    }

    public function getAvailableYear(){

        $user = User::where('user_acl_role_id','2')->get();
        $year = [];
        foreach ($user as $iterator => $value) {
            $getUser = FinancialYear::where('user_id', $value->id)->get()->ToArray();
            if($getUser){
                $getStartMonth = $this->setMonth($getUser['0']['month_start']);
                $getEndMonth = $this->setMonth($getUser['0']['month_end']);
            }
            if($getUser != null){
                $incomeTransactions = FinanceIncome::where('freelancer_id', $value->id)->get()->toArray();
                $expenseTransactions = FinanceExpense::where('freelancer_id', $value->id)->get()->toArray();
                
                $transactions = array_values(array_merge($incomeTransactions, $expenseTransactions));
                
                if($transactions != null){
                    foreach ($transactions as $key => $tranaction) {
                        $carbonMonth = Carbon::parse($tranaction['job_date']);

                        $completeYear = $carbonMonth->format('Y');
                        $years = array($completeYear);
                        

                        if ($carbonMonth->format('m') >= $getUser['0']['month_start']) {
                            $currentYear = $completeYear;
                        
                            if (array_key_exists($currentYear, $year)) {
                                $existingUserId = array_search($value['id'], array_column($year[$currentYear], 'user_id'));
                        
                                if ($existingUserId === false) {
                                    $year[$currentYear][] = [
                                        'user_id' => $value['id'],
                                        'start_month' => $getStartMonth,
                                        'end_month' => $getEndMonth,
                                        'fin_year' => $currentYear,
                                        'user_type' => $getUser[0]['user_type'],
                                        'login' => $value['login'],
                                        'user_acl_profession_id' => $value['user_acl_profession_id']
                                    ];
                                }
                            } else {
                                $year[$currentYear] = [
                                    [
                                        'user_id' => $value['id'],
                                        'start_month' => $getStartMonth,
                                        'end_month' => $getEndMonth,
                                        'fin_year' => $currentYear,
                                        'user_type' => $getUser[0]['user_type'],
                                        'login' => $value['login'],
                                        'user_acl_profession_id' => $value['user_acl_profession_id']
                                    ],
                                ];
                            }
                        } else {
                            $previousYear = $completeYear - 1;
                        
                            if (array_key_exists($previousYear, $year)) {
                                $existingUserId = array_search($value['id'], array_column($year[$previousYear], 'user_id'));
                        
                                if ($existingUserId === false) {
                                    $year[$previousYear][] = [
                                        'user_id' => $value['id'],
                                        'start_month' => $getStartMonth,
                                        'end_month' => $getEndMonth,
                                        'fin_year' => $previousYear,
                                        'user_type' => $getUser[0]['user_type'],
                                        'login' => $value['login'],
                                        'user_acl_profession_id' => $value['user_acl_profession_id']
                                    ];
                                }
                            } else {
                                $year[$previousYear] = [
                                    [
                                        'user_id' => $value['id'],
                                        'start_month' => $getStartMonth,
                                        'end_month' => $getEndMonth,
                                        'fin_year' => $previousYear,
                                        'user_type' => $getUser[0]['user_type'],
                                        'login' => $value['login'],
                                        'user_acl_profession_id' => $value['user_acl_profession_id']
                                    ],
                                ];
                            }
                        }
                    }
                }
            }
        }
        
        $available_year = array_keys($year);
        return $available_year;
    }
    
    public function record(Request $request)
    {
        
        $users = User::all()->where('user_acl_role_id','2');
        $professions = UserAclProfession::where('is_active', 1)->get();
        $available_years = [];
        $available_year = $this->getAvailableYear();
       
        if($request->y != null){
            //dd($request->all());
            $user = [];
            $year = [];
             foreach ($users as $iterator => $value) {
                $getUser = FinancialYear::where('user_id', $value->id)->get()->toArray();
            
                if ($getUser != null) {
                    $getStartMonth = $this->setMonth($getUser[0]['month_start']);
                    $getEndMonth = $this->setMonth($getUser[0]['month_end']);
            
                    // Dynamically filter transactions for the requested year
                    $incomeTransactions = FinanceIncome::where('freelancer_id', $value->id)
                        ->whereYear('job_date', $request->y)
                        ->get()
                        ->toArray();
                    $expenseTransactions = FinanceExpense::where('freelancer_id', $value->id)
                        ->whereYear('job_date', $request->y)
                        ->get()
                        ->toArray();
            
                    $transactions = array_values(array_merge($incomeTransactions, $expenseTransactions));
            
                    if ($transactions != null) {
                        foreach ($transactions as $key => $transaction) {
                            $carbonMonth = Carbon::parse($transaction['job_date']);
                            $transactionYear = $carbonMonth->format('Y');
            
                            // Determine financial year dynamically based on start month
                            if ($carbonMonth->format('m') >= $getUser[0]['month_start']) {
                                $currentYear = $transactionYear;
                            } else {
                                $currentYear = $transactionYear - 1;
                            }
            
                            // Skip if the calculated financial year doesn't match the requested year
                            if ($currentYear != $request->y) {
                                continue;
                            }
            
                            // Handle financial year grouping
                            if (array_key_exists($currentYear, $year)) {
                                $existingUserId = array_search($value['id'], array_column($year[$currentYear], 'user_id'));
            
                                if ($existingUserId === false) {
                                    $year[$currentYear][] = [
                                        'user_id' => $value['id'],
                                        'start_month' => $getStartMonth,
                                        'end_month' => $getEndMonth,
                                        'fin_year' => $currentYear,
                                        'user_type' => $getUser[0]['user_type'],
                                        'login' => $value['login'],
                                        'user_acl_profession_id' => $value['user_acl_profession_id']
                                    ];
                                }
                            } else {
                                $year[$currentYear] = [
                                    [
                                        'user_id' => $value['id'],
                                        'start_month' => $getStartMonth,
                                        'end_month' => $getEndMonth,
                                        'fin_year' => $currentYear,
                                        'user_type' => $getUser[0]['user_type'],
                                        'login' => $value['login'],
                                        'user_acl_profession_id' => $value['user_acl_profession_id']
                                    ],
                                ];
                            }
                        }
                    }
                }
            }
            if ($request->filled('search')) {
    $search = strtolower($request->search);

    // Filter $year array manually
    foreach ($year as $finYear => &$users) {
        $users = array_filter($users, function ($user) use ($search) {
            return strpos(strtolower($user['login']), $search) !== false ||
                   strpos((string) $user['user_id'], $search) !== false;
        });

        // Re-index the array after filtering
        $users = array_values($users);
    }

    // Remove any empty financial year groups after filtering
    $year = array_filter($year);
}

            rsort($available_year);
            return view('admin.finance.record', compact('user','professions', 'year', 'available_year'));
        }
        else{
            $user = User::where('user_acl_role_id','2')->get();
            $year = [];
            foreach ($user as $iterator => $value) {
                $getUser = FinancialYear::where('user_id', $value->id)->get()->ToArray();
                if($getUser){
                    $getStartMonth = $this->setMonth($getUser['0']['month_start']);
                    $getEndMonth = $this->setMonth($getUser['0']['month_end']);    
                }
                if ($getUser != null) {
                        $incomeTransactions = FinanceIncome::where('freelancer_id', $value->id)->get()->toArray();
                        $expenseTransactions = FinanceExpense::where('freelancer_id', $value->id)->get()->toArray();
                
                        $transactions = array_values(array_merge($incomeTransactions, $expenseTransactions));
                        
                        if ($transactions != null) {
                            foreach ($transactions as $key => $transaction) {
                                $carbonMonth = Carbon::parse($transaction['job_date']);
                                $completeYear = $carbonMonth->format('Y');
                                if($value['id'] === 53){
                                }
                        
                                if ($carbonMonth->format('m') <= $getUser[0]['month_end']) {
                                    $currentYear = $completeYear;
                
                                    if ($carbonMonth->format('m') >= $getUser[0]['month_start']) {
                                        if (array_key_exists($currentYear, $year)) {
                                            $existingUserId = array_search($value['id'], array_column($year[$currentYear], 'user_id'));
                
                                            if ($existingUserId === false) {
                                                $year[$currentYear][] = [
                                                    'user_id' => $value['id'],
                                                    'start_month' => $getStartMonth,
                                                    'end_month' => $getEndMonth,
                                                    'fin_year' => $currentYear,
                                                    'user_type' => $getUser[0]['user_type'],
                                                    'login' => $value['login'],
                                                    'user_acl_profession_id' => $value['user_acl_profession_id']
                                                ];
                                            }
                                        } else {
                                            $year[$currentYear] = [
                                                [
                                                    'user_id' => $value['id'],
                                                    'start_month' => $getStartMonth,
                                                    'end_month' => $getEndMonth,
                                                    'fin_year' => $currentYear,
                                                    'user_type' => $getUser[0]['user_type'],
                                                    'login' => $value['login'],
                                                    'user_acl_profession_id' => $value['user_acl_profession_id']
                                                ],
                                            ];
                                        }
                                    } elseif ($carbonMonth->format('m') < $getUser[0]['month_start'] && array_key_exists($currentYear - 1, $year)) {
                                        $existingUserId = array_search($value['id'], array_column($year[$currentYear - 1], 'user_id'));
                
                                        if ($existingUserId === false) {
                                            $year[$currentYear - 1][] = [
                                                'user_id' => $value['id'],
                                                'start_month' => $getStartMonth,
                                                'end_month' => $getEndMonth,
                                                'fin_year' => $currentYear - 1,
                                                'user_type' => $getUser[0]['user_type'],
                                                'login' => $value['login'],
                                                'user_acl_profession_id' => $value['user_acl_profession_id']
                                            ];
                                        }
                                    }
                                } else {
                                    $previousYear = $completeYear + 1;
                
                                    if (array_key_exists($previousYear, $year)) {
                                        $existingUserId = array_search($value['id'], array_column($year[$previousYear], 'user_id'));
                
                                        if ($existingUserId === false) {
                                            $year[$previousYear][] = [
                                                'user_id' => $value['id'],
                                                'start_month' => $getStartMonth,
                                                'end_month' => $getEndMonth,
                                                'fin_year' => $previousYear,
                                                'user_type' => $getUser[0]['user_type'],
                                                'login' => $value['login'],
                                                'user_acl_profession_id' => $value['user_acl_profession_id']
                                            ];
                                        }
                                    } else {
                                        $year[$previousYear] = [
                                            [
                                                'user_id' => $value['id'],
                                                'start_month' => $getStartMonth,
                                                'end_month' => $getEndMonth,
                                                'fin_year' => $previousYear,
                                                'user_type' => $getUser[0]['user_type'],
                                                'login' => $value['login'],
                                                'user_acl_profession_id' => $value['user_acl_profession_id']
                                            ],
                                        ];
                                    }
                                }
                            }
                        }
                }
                
            }
            // 
            // $available_year = array_keys($year);
            $available_year = $this->getAvailableYear();
            rsort($available_year);
            //return $year;
            if ($request->filled('search')) {
    $search = strtolower($request->search);

    // Filter $year array manually
    foreach ($year as $finYear => &$users) {
        $users = array_filter($users, function ($user) use ($search) {
            return strpos(strtolower($user['login']), $search) !== false ||
                   strpos((string) $user['user_id'], $search) !== false;
        });

        // Re-index the array after filtering
        $users = array_values($users);
    }

    // Remove any empty financial year groups after filtering
    $year = array_filter($year);
}

            return view('admin.finance.record', compact('user','professions', 'year', 'available_year'));
        }
    }

    public function profitAndLossSave(Request $request){

        $year = explode('-',$request['financial_year']);

        // $year = 2024;
        $save_data = FinanceProfitLoss::where('fre_id', $request['user_id'])
        ->first();
        // ->whereYear('created_at', $year)

        if ($save_data !== null) {
            $save_data->update([
                'revenue' => $request['pat'] ?? '',
                'cos' => $request['cos'] ?? '',
                'othercost' => $request['other_cost'] ?? '',
                'income_tax' => $request['tax_cal'] ?? '',
                'interest_income' => $request['interest_income'] ?? '',
                'tax_calculation' => ($request['tax_cal'] + $request['ni_tax']),
                'financial_year' => $request['financial_year'] ?? '',
                'starting_month' => $request['start_month'] ?? '',
            ]);
        } else {
            $save_new_data = new FinanceProfitLoss([
                'fre_id' => $request['user_id'] ?? '',
                'revenue' => $request['pat'] ?? '',
                'cos' => $request['cos'] ?? '',
                'othercost' => $request['other_cost'] ?? '',
                'income_tax' => $request['tax_cal'] ?? '',
                'interest_income' => $request['interest_income'] ?? '',
                'tax_calculation' => ($request['tax_cal'] + $request['ni_tax']),
                'financial_year' => $request['financial_year'] ?? '',
                'starting_month' => $request['start_month'] ?? '',
            ]);
            $save_new_data->save();
        }
        
        return response()->json([
            'status' => 200
        ]);
    }
    public function profitAndLoss($id, $year){
        $id = User::where('id', $id)->first();
        $incomes = floatval($id->income_sum_price($year, $id));
        $expenses = $id->expense_sum_price($year, $id->id);
        $cos = floatval($expenses['cos']);
        $gross_profit = floatval($incomes - $cos);
        if($incomes > 0){
            $GP = floatval($gross_profit / $incomes);
        }
        else{
            $GP = 0;
        }
        $ad_exp = floatval($expenses['adm_exp']);
        $profit_from_operations = floatval($gross_profit - $ad_exp);
        if($incomes > 0){
            $op = floatval($profit_from_operations / $incomes);
        }
        else{
            $op = 0;
        }
        $current_year = intval($year);
        $previous_year = $year - 1 ;
        $year = $previous_year .'-'. $current_year;
        $finance_tax_record = FinanceTaxRecord::where('finance_year', $year)->get()->first();
        $finance_ni_tax = FinanceNiTaxRecord::where('finance_year', $year)->get()->first();

        $data =[
            'revenue' => $incomes, 'cos' => $cos, 'gross_profit' => $gross_profit, 'GP' => round($GP, 2), 'ad_exp' => $ad_exp, 'prof_frm_oper' => $profit_from_operations, 'op' => round($op, 2), 'login' => $id->login, 'user_id' => $id->id, 'first_limit' => $finance_tax_record->personal_allowance_rate ?? '', 'first_limit_rate' => $finance_tax_record?->personal_allowance_rate_tax, 'second_limit' => $finance_tax_record?->basic_rate, 'second_limit_rate' => $finance_tax_record?->basic_rate_tax, 'third_limit' => $finance_tax_record?->higher_rate, 'third_limit_rate' => $finance_tax_record?->higher_rate_tax, 'final_limit' => $finance_tax_record?->additional_rate, 'final_limit_rate' => $finance_tax_record?->additional_rate_tax, 'c4_min_ammount_1' => $finance_ni_tax['c4_min_ammount_1'] ?? '', 'c4_min_ammount_tax_1' => $finance_ni_tax['c4_min_ammount_tax_1'] ?? '', 'c4_min_ammount_2' => $finance_ni_tax['c4_min_ammount_2'] ?? '', 'c4_min_ammount_tax_2' => $finance_ni_tax['c4_min_ammount_tax_2'] ?? '', 'c4_min_ammount_3' => $finance_ni_tax['c4_min_ammount_3'] ?? '', 'c4_min_ammount_tax_3' => $finance_ni_tax['c4_min_ammount_tax_3'] ?? '', 'c2_min_amount' => $finance_ni_tax['c2_min_amount'] ?? '', 'c2_tax' => $finance_ni_tax['c2_tax'] ?? '', 'financial_year' => $year
        ];
        return view('admin.finance.profitloss', compact('data'));
    }
    public function balanceSheet(User $id, $years){

        $year = $years;
        
        $incometax = FinanceProfitLoss::where('fre_id', $id->id)->whereYear('created_at', $year)->first();
        $balance_sheet = FinanceBalanceSheet::where('fre_id', $id->id)->first();
        $data = ['year' => $years, 'user_id' => $id->id, 'login' => $id->login, 'income_tax' => $incometax->income_tax ?? ''];
        return view('admin.finance.balancesheet', compact('data', 'balance_sheet'));
    }

    public function balanceSheetSave(Request $request){

        $user = FinanceBalanceSheet::where('fre_id', $request['user_id'])->delete();
        

        
        
        $save_new_data = new FinanceBalanceSheet([
            'fre_id' => $request['user_id'],
            'financial_year' => $request['finance_year'],
            'trade_other' => $request['trade_other'],
            'cash_equp' => $request['cash_equp'],
            'total_cash_trade' => $request['total_cash_trade'],
            'total_assets' => $request['total_assets'],
            'current_liability' => $request['current_liability'],
            'taxation' => $request['taxation'],
            'income_tax' => $request['taxation'],
            'total_tax_liab' => $request['total_tax_liab'],
            'net_assests_liab' => $request['net_assests_liab'],
            'equity' => $request['equity'],
            'retained_earning' => $request['retained_earning'],
            'profit_plan_equip' => $request['pro_plan'],
            'input_data' => 'Testing input',
        ]);
        $save_new_data->save();
        return response()->json([
            'status' => 200
        ]);
    }
    
    public function transactions(User $id, $year, Request $request){
        $income_tranactions = FinanceIncome::where('freelancer_id', $id->id)->get();
        $expense_transactions = FinanceExpense::with('expense_type')->where('freelancer_id', $id->id)->get();
        // dd('here');
        return view('admin.finance.alltransactions', compact('income_tranactions','expense_transactions', 'id', 'year'));
    } 
    
    
    // public function transactions(User $id, $year, Request $request)
    // {
    //     $perPage = 10;
    
    //     income_tranactions = FinanceIncome::where('freelancer_id', $id->id)
    //         ->paginate($perPage);
    
    //     $expense_transactions = FinanceExpense::where('freelancer_id', $id->id)
    //         ->paginate($perPage);
    
    //     return view('admin.finance.alltransactions', compact('income_tranactions', 'expense_transactions', 'id', 'year'));
    // }
    
    public function supplierList(Request $request){
        $suppliers = Supplier::where('created_by_user_id', $request['id'])->get();
        $user = User::where('id', $request['id'])->get()->first();
        
        $data = ['user_id' => $user->id, 'login' => $user->login, 'year' => '2023'];
                
        return view('admin.finance.supplierlist', compact('suppliers','data'));
    }
    public function taxList(){
        //
        $financetaxrecord = FinanceTaxRecord::all();
        return view('admin.taxSetting.index',compact('financetaxrecord'));
    }
    public function taxdelete($id){
        //
        $financetaxrecord = FinanceTaxRecord::find($id)->delete();
        return redirect()->back();
    }
    public function taxCreate(){
        return view('admin.taxSetting.create');
    }
    public function taxEdit($id){
        $tax = FinanceTaxRecord::find($id);
        return view('admin.taxSetting.edit', compact('tax'));
    }
    
    public function taxupdate(Request $request) {
        $tax = FinanceTaxRecord::find($request->tax_id);
        $tax -> finance_year = $request -> finance_year ?? '';
        $tax -> personal_allowance_rate = $request -> personal_allowance_rate ?? '';
        $tax -> personal_allowance_rate_tax = $request -> personal_allowance_rate_tax ?? '';
        $tax -> basic_rate = $request -> basic_rate ?? '';
        $tax -> basic_rate_tax = $request -> basic_rate_tax ?? '';
        $tax -> higher_rate = $request -> higher_rate ?? '';
        $tax -> higher_rate_tax = $request -> higher_rate_tax ?? '';
        $tax -> company_limited_tax = $request -> company_limited_tax ?? '';
        $tax -> save();
        
        return redirect()->route('tax.list');
    }

    
    public function niTaxList(){
        //dalta
        $niTaxSetting = FinanceNiTaxRecord::all();
        return view('admin.niTaxSetting.index',compact('niTaxSetting'));
    }
    public function niTaxCreate(){
        return view('admin.niTaxSetting.create');
    }
    public function niTaxEdit($id){
        $tax = FinanceNiTaxRecord::where('id' , $id)->first();
        return view('admin.niTaxSetting.edit' , compact('tax'));
    }
    public function nitaxdelete($id){
        $financetaxrecord = FinanceNiTaxRecord::find($id)->delete();
        return redirect()->back();
    }
    public function nitaxStore(Request $request){
        $validatedData = $request->validate([
            'finance_year' => 'required|digits:9|integer|min:1900|max:' . (date('Y') + 1),
            'c4_min_ammount_1' => 'required|numeric|min:0',
            'c4_min_ammount_tax_1' => 'required|numeric|min:0',
            'c4_min_ammount_2' => 'required|numeric|min:0',
            'c4_min_ammount_tax_2' => 'required|numeric|min:0',
            'c4_min_ammount_3' => 'required|numeric|min:0',
            'c4_min_ammount_tax_3' => 'required|numeric|min:0',
            'c2_min_amount' => 'required|numeric|min:0',
            'c2_tax' => 'required|numeric|min:0',
        ]);
        
        FinanceNiTaxRecord::create([
            'finance_year' => $validatedData['finance_year'],
            'c4_min_ammount_1' => $validatedData['c4_min_ammount_1'],
            'c4_min_ammount_tax_1' => $validatedData['c4_min_ammount_tax_1'],
            'c4_min_ammount_2' => $validatedData['c4_min_ammount_2'],
            'c4_min_ammount_tax_2' => $validatedData['c4_min_ammount_tax_2'],
            'c4_min_ammount_3' => $validatedData['c4_min_ammount_3'],
            'c4_min_ammount_tax_3' => $validatedData['c4_min_ammount_tax_3'],
            'c2_min_amount' => $validatedData['c2_min_amount'],
            'c2_tax' => $validatedData['c2_tax'],
        ]);

        return redirect()->route('nitax.list')->with('success', 'Data has been Added');
    }
    
    public function nitaxUpdate(Request $request){
        // Step 3: Validate the incoming data
        $validatedData = $request->validate([
            'finance_year' => 'required|digits:4|integer|min:1900|max:' . (date('Y') + 1),
            'c4_min_ammount_1' => 'required|numeric|min:0',
            'c4_min_ammount_tax_1' => 'required|numeric|min:0',
            'c4_min_ammount_2' => 'required|numeric|min:0',
            'c4_min_ammount_tax_2' => 'required|numeric|min:0',
            'c4_min_ammount_3' => 'required|numeric|min:0',
            'c4_min_ammount_tax_3' => 'required|numeric|min:0',
            'c2_min_amount' => 'required|numeric|min:0',
            'c2_tax' => 'required|numeric|min:0',
        ]);
    
        $financeRecord = FinanceNiTaxRecord::findOrFail($request->id);
        $financeRecord->update($validatedData);
    
        return redirect()->route('nitax.list')->with('success', 'Finance NI Tax Record has been updated successfully.');
    }
    
    public function taxStore(Request $request) {
        FinanceTaxRecord::create([
            'finance_year' => $request['finance_year'],
            'personal_allowance_rate' => $request['personal_allowance_rate'],
            'personal_allowance_rate_tax' => $request['personal_allowance_rate_tax'],
            'basic_rate' => $request['basic_rate'],
            'basic_rate_tax' => $request['basic_rate_tax'],
            'higher_rate' => $request['higher_rate'],
            'higher_rate_tax' => $request['higher_rate_tax'],
            'additional_rate' => $request['additional_rate'],
            'additional_rate_tax' => $request['additional_rate_tax'],
            'company_limited_tax' => $request['company_limited_tax'],
        ]);
        return redirect()->route('tax.list')->with('success', 'Data has been Added');
    }

}