<?php

namespace App\Http\Controllers;

use App\Helpers\FinanceHelper;
use App\Mail\FreelancerJobInvitationMail;
use App\Models\BlockUser;
use App\Models\EmployerStoreList;
use App\Models\FinanceEmployer;
use App\Models\FinanceExpense;
use App\Models\FinanceIncome;
use App\Models\FinancialYear;
use App\Models\IndustryNews;
use App\Models\JobAction;
use App\Models\JobCancelation;
use App\Models\JobFeedback;

use App\Models\JobInvitedUser;
use App\Models\JobPost;
use App\Models\JobPostTimeline;
use App\Models\PrivateUser;
use App\Models\PrivateUserJobAction;
use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserExtraInfo;
use App\Models\UserQuestion;
use App\Models\UsersWorkCalender;
use Carbon\Carbon;
use Error;
use App\Notifications\DeleteUserNotification;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;

class EmployerController extends Controller
{
    public function index(Request $request)
    {
        $employer_user_id = $request->user()->id;
        $finance_helper = new FinanceHelper(Auth::user());

        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $user_finance_type = $finance_helper->get_user_finance_type();

        $filter_year = date('Y');
        $filter = 'month';

        $year_start = get_financial_year_range($finance_year_start_month)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month)["year_end"];

        $income_chart_data = $finance_helper->get_chart_finance_data(FinanceIncome::query(), $year_start, $year_end, $finance_year_start_month, $filter, false, true);
        $expense_chart_data = $finance_helper->get_chart_finance_data(FinanceExpense::query(), $year_start, $year_end, $finance_year_start_month, $filter, false, true);

        $total_income = $finance_helper->get_user_total_income($filter_year, $finance_year_start_month);
        $total_expense = $finance_helper->get_user_total_expense($filter_year, $finance_year_start_month);
        $user_total_tax = $finance_helper->user_tax_calculation($finance_year_start_month, $total_income - $total_expense, $user_finance_type, $filter_year);

        //Live freelancer
        $bookedDates = JobPost::where(function ($query) {
            $query->where("job_status", JobPost::JOB_STATUS_ACCEPTED)->orWhere("job_status", JobPost::JOB_STATUS_DONE_COMPLETED);
        })->where("employer_id", Auth::user()->id)->select("job_date")->pluck("job_date")->map(function ($date) {
            return $date->format("Y-m-d");
        })->toArray();

        $feedbacks = JobFeedback::with('freelancer')->where("employer_id", Auth::user()->id)->where("user_type", "freelancer")->where("status", 1)->whereDate("created_at", ">=", today()->subMonths(6)->startOfMonth())->get();
        $overall_rating = get_overall_feedback_rating($feedbacks);

        //Cancallation rate
        $cancellation_rate = get_job_cancellation_rate_by_user(Auth::user()->id);

        $user_role = Auth::user()->user_acl_role_id;
        $user_profession = Auth::user()->user_acl_profession_id;

        $industry_news = IndustryNews::whereRaw("FIND_IN_SET('{$user_role}', user_type)")
            ->whereRaw("FIND_IN_SET('{$user_profession}', category_id)")
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();


        //Employer Data
        $employer_finance_cost = $finance_helper->get_employer_finance_cost_chart_data($year_start, $year_end);
        $employer_finance_job = $finance_helper->get_employer_finance_jobs_chart_data($year_start, $year_end);

        $current_month_bookings = JobPost::where(function ($query) {
            $query->where("job_status", 4)->orWhere("job_status", 5);
        })->where("employer_id", Auth::user()->id)->whereRaw("MONTH(job_date) = MONTH(NOW())")->whereRaw("YEAR(job_date) = YEAR(NOW())")->orderBy("job_date")->get();

        return view('employer.dashboard', compact('bookedDates', 'finance_year_start_month', 'current_month_bookings', 'employer_finance_cost', 'employer_finance_job', 'feedbacks', 'industry_news', 'overall_rating', 'cancellation_rate'));
    }

    public function editProfile()
    {
        return view('employer.edit-profile');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            "firstname" => ["required", "max:255", "regex:/^[a-zA-Z]+$/"],
            "lastname" => ["required", "max:255", "regex:/^[a-zA-Z]+$/"],
            "company" => ["required", "max:255"],
            "address" => ["required", "max:255"],
            "city" => ["required", "max:255"],
            "zip" => ["required", "max:255"],
            "telephone" => ["required", "digits:11"],
            "mobile" => ["nullable", "digits:11"],
        ],[
            'telephone.digits' => 'The telephone number must be 11 digits.',
            'mobile.digits' => 'The mobile number must be 11 digits.',
        ]);
        
        $firstname = $request->input("firstname");
        $lastname = $request->input("lastname");
        $company = $request->input("company");
        $address = $request->input("address");
        $city = $request->input("city");
        $zip = $request->input("zip");
        $telephone = $request->input("telephone");
        $mobile = $request->input("mobile");

        $user = User::with("user_extra_info")->findOrFail(Auth::id());
        $user_extra_info = $user->user_extra_info;

        $user->firstname = $firstname;
        $user->lastname = $lastname;
        $user_extra_info->company = $company;
        $user_extra_info->address = $address;
        $user_extra_info->city = $city;
        $user_extra_info->zip = $zip;
        $user_extra_info->telephone = $telephone;
        $user_extra_info->mobile = $mobile;

        $user->save();
        $user_extra_info->save();

        return redirect(route('employer.dashboard'))->with("success", "Profile updated successfully");
    }

    public function deleteProfile()
    {
        User::where("id", Auth::user()->id)->update(["active" => '5']);
        $user = User::where('user_acl_role_id', 1)->first();
        $user->notify(new DeleteUserNotification());
        Auth::logout();
        return redirect("/")->with("success", "Your account has been deleted");
    }

    public function editQuestions()
    {
        $user_database_questions_html = get_user_database_questions(Auth::user()->user_acl_role_id, Auth::user()->user_acl_profession_id, true);

        return view('employer.edit-questions', ['user_database_questions_html' => $user_database_questions_html]);
    }

    public function updateQuestions(Request $request)
    {
        $request->validate([
            'ans_val_for_question_id_24' => ['required'],
        ], [
            'ans_val_for_question_id_24.required' => 'Areas of Specialization is required.',
        ]);
        
        $store_type_name = $request->input("store_id_emp");

        UserExtraInfo::where("user_id", Auth::user()->id)->update([
            "store_type_name" => $store_type_name,
        ]);

        $question_ids = $request->input("question_id");
        if ($request && is_array($question_ids) && sizeof($question_ids) > 0) {
            UserAnswer::where("user_id", Auth::user()->id)->delete();
            $answer_inserted_records = array();
            foreach ($question_ids as $question_id) {
                $value = $request->input("ans_val_for_question_id_{$question_id}") ?? "";
                if ($value && is_array($value)) {
                    $value = json_encode($value);
                }
                $answer_inserted_records[] = [
                    "user_id" => Auth::user()->id,
                    "user_question_id" => $question_id,
                    "type_value" => $value,
                ];
            }
            UserAnswer::insert($answer_inserted_records);
        }
        $popup = $request->input("popup");
        if ($popup && $popup == '1') {
            return "<script> window.close(); </script>";
        }

        return redirect(route('employer.dashboard'))->with("success", "Profile questions updated successfully");
    }

    public function showHelpJobBooking()
    {
        return view('employer.job-booking-employer');
    }

    public function showHelpFinanceModel()
    {
        return view('employer.finance-model-employer');
    }

    public function showManageStore()
    {
        $stores = EmployerStoreList::where("employer_id", Auth::user()->id)->get();

        return view("employer.manage-store", compact('stores'));
    }

    public function updateStoreList(Request $request)
    {
        // dd($request -> all());
        // $request->validate([
        //     "store_name_2" => ["required", "max:255", "regex:/^[a-zA-Z\s]+$/"],
        //     "store_address_2" => ["required", "max:255"],
        //     "store_region_2" => ["required", "max:255"],
        //     "store_zip_2" => ["required", "max:255"],
        // ]);
        $store_ids = $request->input("store_ids", []);
        if (is_array($store_ids) && sizeof($store_ids) > 0) {
            foreach ($store_ids as $store_id) {
                $start_time = $request->input('job_start_time_' . $store_id);
                $end_time = $request->input('job_end_time_' . $store_id);
                $lunch_time = $request->input('job_lunch_time_' . $store_id);

                $store_name = $request->input("store_name_" . $store_id);
                $store_address = $request->input("store_address_" . $store_id);
                $store_region = $request->input("store_region_" . $store_id);
                $store_zip = $request->input("store_zip_" . $store_id);

                $store_update_data = array(
                    'store_name'    => $store_name,
                    'store_address' => $store_address,
                    'store_region'  => $store_region,
                    'store_zip'     => $store_zip,
                    'store_start_time' => json_encode($start_time),
                    'store_end_time'   => json_encode($end_time),
                    'store_lunch_time' => json_encode($lunch_time),
                    'updated_at' => now()
                );
                EmployerStoreList::where("employer_id", Auth::user()->id)->where("id", $store_id)->update($store_update_data);
            }
        }
        return back()->with("success", "Store list updated successfully");
    }

    public function deleteStore($store_id)
    {
        try {
            EmployerStoreList::where("employer_id", Auth::user()->id)->where("id", $store_id)->delete();
        } catch (Exception) {
            return new JsonResponse(["success" => false, "message" => "Store is connected with jobs. You cannot delete it"]);
        }
        return new JsonResponse(["success" => true]);
    }

    public function saveNewStore(Request $request)
    {
        $request->validate([
            "total_emp_stores" => ["required", "array", "min:1"]
        ]);
        $total_emp_stores = $request->input("total_emp_stores");
        $rules = array();
        foreach ($total_emp_stores as $store_key) {
            $rules = array_merge($rules, [
                "emp_store_name_" . $store_key => ["required", "string"],
                "emp_store_address_" . $store_key => ["required", "string"],
                "emp_store_region_" . $store_key => ["required", "string"],
                "emp_store_zip_" . $store_key => ["required", "string"],
                "job_start_time_" . $store_key => ["required", "array", "size:7"],
                "job_end_time_" . $store_key => ["required", "array", "size:7"],
                "job_lunch_time_" . $store_key => ["required", "array", "size:7"]
            ]);
        }
        $request->validate($rules);
        $emp_store_result = array();
        if ($total_emp_stores && is_array($total_emp_stores)) {
            foreach ($total_emp_stores as $key => $store_key) {
                $emp_start_time = $request->input('job_start_time_' . $store_key);
                $emp_end_time = $request->input('job_end_time_' . $store_key);
                $emp_lunch_time = $request->input('job_lunch_time_' . $store_key);
                $emp_store_name = $request->input('emp_store_name_' . $store_key);
                $emp_store_address = $request->input('emp_store_address_' . $store_key);
                $emp_store_region = $request->input('emp_store_region_' . $store_key);
                $emp_store_zip = $request->input('emp_store_zip_' . $store_key);

                $emp_store_result[] = array(
                    'employer_id' => Auth::user()->id,
                    'store_name'    => $emp_store_name,
                    'store_address' => $emp_store_address,
                    'store_region'  => $emp_store_region,
                    'store_zip'     => $emp_store_zip,
                    'store_start_time' => json_encode($emp_start_time),
                    'store_end_time'   => json_encode($emp_end_time),
                    'store_lunch_time' => json_encode($emp_lunch_time),
                    'created_at' => now(),
                    'updated_at' => now()
                );
            }
        }

        if (sizeof($emp_store_result) > 0) {
            EmployerStoreList::insert($emp_store_result);
        }
        return back()->with("success", "New store added successfully");
    }

    public function updateFinancialYear(Request $request)
    {
        $request->validate([
            "month" => "required|integer|min:1|max:12",
            "usertype" => "required|in:soletrader,limitedcompany"
        ]);
        $month = intval($request->input("month"));
        $usertype = $request->input("usertype");

        $month_end = $month == 1 ? 12 : $month - 1;

        FinancialYear::updateOrCreate([
            "user_id" => Auth::user()->id
        ], [
            "user_type" => $usertype,
            "month_start" => $month,
            "month_end" => $month_end,
        ]);

        return new JsonResponse(["success" => true]);
    }

    public function feedbackDetails()
    {
        $userType = 'Locum(s)';
        $feedbacks = JobFeedback::with('freelancer')->where("employer_id", Auth::user()->id)->where("user_type", "freelancer")->where("status", 1)->whereDate("created_at", ">=", today()->subMonths(6)->startOfMonth())->get();
        $overall_rating = get_overall_feedback_rating($feedbacks);

        return view('employer.feedback-detail', compact('feedbacks', 'overall_rating'));
    }

    public function financeHome(Request $request)
    {
        $finance_helper = new FinanceHelper(Auth::user());

        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();

        $filter_year = $request->has("year") ? intval($request->input("year")) : date('Y');

        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];

        $employer_finance_cost = $finance_helper->get_employer_finance_cost_chart_data($year_start, $year_end);
        $employer_finance_job = $finance_helper->get_employer_finance_jobs_chart_data($year_start, $year_end);

        $transactions = FinanceEmployer::query()->where("employer_id", Auth::user()->id)->whereBetween("job_date", [$year_start, $year_end])->get();
        //dd($transactions , $employer_finance_cost , $employer_finance_job , $filter_year);

        return view("employer.finance.finance-detail", compact('finance_year_start_month', 'transactions', 'employer_finance_cost', 'employer_finance_job', 'filter_year'));
    }

    public function updateBankTransactionDate(Request $request)
    {
        $transaction_id = $request->input("in_bankid");
        $transaction_date = parse_date_from_default_format($request->input("in_bankdate"));

        if (is_null($transaction_date)) {
            return back()->with("error", "Please choose valid date");
        }

        $transaction = FinanceEmployer::query()->where("employer_id", Auth::user()->id)->where("id", $transaction_id)->first();
        if (is_null($transaction)) {
            return back()->with("error", "No transaction found");
        }

        $transaction->is_paid = true;
        $transaction->paid_date = $transaction_date->format("Y-m-d");
        $transaction->save();

        return back()->with("success", "Transactipn bank date updated successfully");
    }
    public function deleteFinanceTransaction($id)
    {
        $transaction = FinanceEmployer::query()->where("employer_id", Auth::user()->id)->where("id", $id)->first();
        if (is_null($transaction)) {
            return back()->with("error", "No transaction found");
        }

        $transaction->delete();

        return back()->with("success", "Transactipn deleted successfully");
    }

    public function manageFinanceTransaction($id = null)
    {
        if ($id) {
            $transaction = FinanceEmployer::query()->where("employer_id", Auth::user()->id)->where("id", $id)->first();
            if (is_null($transaction)) {
                return abort(404);
            }
        } else {
            $transaction = null;
        }
        return view("employer.manage-finance", compact("transaction"));
    }
    public function saveFinanceTransaction(Request $request)
    {

        $request->validate([
            "paid" => "nullable",
            "fre_type" => "required|in:1,2",
            "job_id" => "required",
            "fre_id" => "required_if:fre_type,1",
            "in_date" => "required",
            "rate" => "required",
            "paid_date" => "required_if:paid,1",
        ]);

        $paid = $request->input("paid") == 1 ? true : false;
        $fre_type = $request->input("fre_type");
        $job_id = $request->input("job_id");
        $fre_id = $request->input("fre_id");
        $in_date = parse_date_from_default_format($request->input("in_date"));
        $rate = $request->input("rate");
        $bonus = $request->input("bonus");
        $paid_date = parse_date_from_default_format($request->input("paid_date"));

        $transaction = new FinanceEmployer();
        $transaction->employer_id = Auth::user()->id;
        $transaction->job_id = $job_id;
        $transaction->freelancer_id = $fre_id;
        $transaction->freelancer_type = $fre_type;
        $transaction->job_date = $in_date->format("Y-m-d");
        $transaction->job_rate = $rate;
        $transaction->bonus = $bonus;
        $transaction->is_paid = $paid;
        $transaction->paid_date = $paid_date ? $paid_date->format("Y-m-d") : null;

        $transaction->save();
        return redirect("/employer/finance")->with("success", "Transaction saved successfully");
    }

    public function updateFinanceTransaction(Request $request, $id)
    {
        $transaction = FinanceEmployer::query()->where("employer_id", Auth::user()->id)->where("id", $id)->first();
        if (is_null($transaction)) {
            return back()->with("error", "No transaction found");
        }
        $request->validate([
            "paid" => "nullable",
            "fre_type" => "required|in:1,2",
            "job_id" => "required",
            "fre_id" => "required_if:fre_type,1",
            "in_date" => "required",
            "rate" => "required",
            "paid_date" => "required_if:paid,1",
        ]);

        $paid = $request->input("paid") == 1 ? true : false;
        $fre_type = $request->input("fre_type");
        $job_id = $request->input("job_id");
        $fre_id = $request->input("fre_id");
        $in_date = parse_date_from_default_format($request->input("in_date"));
        $rate = $request->input("rate");
        $bonus = $request->input("bonus");
        $paid_date = parse_date_from_default_format($request->input("paid_date"));

        $transaction->job_id = $job_id;
        $transaction->freelancer_id = $fre_id;
        $transaction->freelancer_type = $fre_type;
        $transaction->job_date = $in_date->format("Y-m-d");
        $transaction->job_rate = $rate;
        $transaction->bonus = $bonus;
        $transaction->is_paid = $paid;
        $transaction->paid_date = $paid_date ? $paid_date->format("Y-m-d") : null;

        $transaction->save();
        return redirect("/employer/finance")->with("success", "Transaction updated successfully");
    }
}
