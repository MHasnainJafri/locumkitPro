<?php

namespace App\Http\Controllers;

use App\Exports\TransactionExport;
use App\Helpers\FinanceHelper;
use App\Mail\IncomeInvoiceMail;
use App\Models\ExpenseType;
use App\Models\Finance;
use App\Models\FinanceExpense;
use App\Models\FinanceIncome;
use App\Models\FinancialYear;
use App\Models\FreelancerPrivateFinance;
use App\Models\FreelancerPrivateJob;
use App\Models\IndustryNews;
use App\Models\Invoice;
use App\Models\JobAction;
use App\Models\JobCancelation;
use App\Models\JobFeedback;
use App\Models\JobPost;
use App\Models\Leavers;
use App\Models\SendNotification;
use App\Models\Supplier;
use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserBankDetail;
use App\Models\UserExtraInfo;
use App\Models\UsersWorkCalender;
use App\Notifications\DeleteUserNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use CreateUserLeaversTableTable;
use Exception;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class FreelancerController extends Controller
{
    public function index(Request $request)
    {
        $freelancer_user_id = $request->user()->id;
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

        $allLiveJobs = JobPost::with("job_store")->select("id", "job_date", "job_post_desc", "job_rate", "employer_store_list_id", "job_address")
            ->whereIn("job_status", [JobPost::JOB_STATUS_ACCEPTED, JobPost::JOB_STATUS_DONE_COMPLETED])
            ->whereHas("job_actions", function ($query) use ($freelancer_user_id) {
                $query->where("freelancer_id", $freelancer_user_id);
                $query->whereIn("action", [JobAction::ACTION_ACCEPT, JobAction::ACTION_DONE]);
            })->orderBy("job_date")->get();
        // dd($allLiveJobs);

        $liveBookDates = $allLiveJobs->map(function ($job) {
            return $job->job_date->format("Y-m-d");
        })->toArray();
        $currentMonthLiveJobs = $allLiveJobs->filter(function ($job) {
            if ($job->job_date >= today()->startOfMonth() && $job->job_date <= today()->endOfMonth()) {
                return true;
            }
            return false;
        });

        //Private jobs
        $privateJobs = $request->user()->private_jobs()->orderBy("job_date")->get();
        $privateBookDates = $privateJobs->map(function ($job) {
            return $job->job_date->format("Y-m-d");
            ;
        })->toArray();
        $bookedDates = array_merge($liveBookDates, $privateBookDates);

        $currentMonthPrivateJobs = $privateJobs->filter(function ($job) {
            if ($job->job_date >= today()->startOfMonth()) {
                return true;
            }
            return false;
        });

        $intersetedJobs = JobPost::with("job_store")->select("id", "job_rate", "job_date", "job_address", "job_region", "job_zip", "job_post_desc", "job_status", "employer_store_list_id")
            ->whereIn("job_status", [JobPost::JOB_STATUS_OPEN_WAITING, JobPost::JOB_STATUS_FREEZED])
            ->whereHas("job_actions", function ($query) use ($freelancer_user_id) {
                $query->where("freelancer_id", $freelancer_user_id);
                $query->whereIn("action", [JobAction::ACTION_NONE, JobAction::ACTION_FREEZE]);
            })
            ->whereNotIn("job_date", $bookedDates)->orderBy("job_date")->get();

        $userBlockDates = UsersWorkCalender::where("user_id", Auth::user()->id)->select("block_dates")->first();
        if ($userBlockDates) {
            $userBlockDates = json_decode($userBlockDates->block_dates) ? array_values(json_decode($userBlockDates->block_dates)) : [];
        } else {
            $userBlockDates = [];
        }

        $feedbacks = JobFeedback::with('employer')->where("freelancer_id", Auth::user()->id)->where("user_type", JobFeedback::FEEDBACK_BY_EMPLOYER)->where("status", 1)->whereDate("created_at", ">=", today()->subMonths(6)->startOfMonth())->get();
        $overall_rating = get_overall_feedback_rating($feedbacks);

        $cancellation_rate = get_job_cancellation_rate_by_user(Auth::user()->id);

        $user_role = Auth::user()->user_acl_role_id;
        $user_profession = Auth::user()->user_acl_profession_id;
        // $notify = SendNotification::where('recipient_id', auth()->user()->id)
        //     ->where(function ($query) {
        //         $query->where('status', '0')
        //             ->orWhere('status', '2');
        //     })->get();
        $notify = [];
        $job_action=JobAction::where('freelancer_id',auth()->user()->id)
                             ->where('action','4')->get();
        $industry_news = IndustryNews::whereRaw("FIND_IN_SET('{$user_role}', user_type)")
            ->whereRaw("FIND_IN_SET('{$user_profession}', category_id)")
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        return view('freelancer.dashboard', compact('bookedDates', 'job_action','notify', 'finance_year_start_month', 'currentMonthLiveJobs', 'feedbacks', 'industry_news', 'overall_rating', 'cancellation_rate', 'income_chart_data', 'expense_chart_data', 'currentMonthPrivateJobs', 'intersetedJobs', 'userBlockDates', 'total_income', 'total_expense', 'user_total_tax'))->with('message', 'Have You arrived at work..?');
    }

    public function editProfile()
    {
        return view('freelancer.edit-profile');
    }


    public function update_notification_yes($id)
    {
        $notification = SendNotification::find($id);
        $notification->status = 2;
        $notification->message = 'What was you expenses..?';
        $notification->save();
        return back();
    }
    public function update_notification_no($id)
    {
        $notification = SendNotification::find($id);
        $notification->status = 1;
        $notification->save();
        return back();
    }

    public function Final_update_notification($id)
    {
        $notification = SendNotification::find($id);
        $notification->status = 3;
        $notification->save();
        $notification->jobposting()->update(['job_status'=>5]);
        $notification->jobposting->job_actions()->update(['action' => 4]);
        return back();
    }

    public function AddFeedBack(Request $request)
    {
        // dd($request->all());
        $userType = $request->user_type == 2 ? 'freelancer' : 'employer';
        JobFeedback::create([
          "employer_id"=> $request->employer_id,
        "freelancer_id"=> $request->freelancer_id,
        "job_id"=> $request->job_id,
        "rating"=> $request->rating,
        "feedback"=> $request->feedback,
        "comments"=> $request->comments,
        "user_type"=> $userType,
        "cat_id"=> $request->cat_id,
        ]);
        return back();


    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            "firstname" => ["required", "max:255", "regex:/^[a-zA-Z\s]+$/"],
            "lastname" => ["required", "max:255", "regex:/^[a-zA-Z\s]+$/"],
            "company" => ["required", "max:255"],
            "address" => ["required", "max:255"],
            "city" => ["required", "max:255"],
            "zip" => ["required", "max:255"],
            // "telephone" => ["required", "min:11"],
            "mobile" => ["nullable", "min:11"],
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

        return redirect(route('freelancer.dashboard'))->with("success", "Profile updated successfully");
    }

    public function deleteProfile(Request $request)
    {
        $reasons = $request->reason;

        foreach ($reasons as $index => $reason) {
            $formattedReasons[] = ($index + 1) . '. ' . $reason;
        }
        $result = implode(PHP_EOL, $formattedReasons);

        $leavers = new Leavers();
        $leavers->lid = Auth::user()->id;
        $leavers->uid = Auth::user()->id;
        $leavers->user_email = Auth::user()->email;
        $leavers->user_name = Auth::user()->firstname ?? ' ' . '' . Auth::user()->lastname;
        $leavers->user_reason_to_leave = $result;
        $leavers->save();
        $user = User::where('user_acl_role_id', 1)->first();
        
        $user->notify(new DeleteUserNotification());
        User::where("id", Auth::user()->id)->update(["active" => '5']);
        Session::flush();
        Auth::logout();
        return redirect("/")->with("success", "Your account has been deleted");
    }

    public function editQuestions()
    {
        $user_database_questions_html = get_user_database_questions(Auth::user()->user_acl_role_id, Auth::user()->user_acl_profession_id, true);
        // $minimum_rate = json_decode(Auth::user()->user_extra_info->minimum_rate, true) ?? [];
        $minimum_rate = json_decode(Auth::user()?->user_extra_info?->minimum_rate, true) ?? [];
        $max_distance = Auth::user()->user_extra_info->max_distance;

        return view('freelancer.edit-questions', ['user_database_questions_html' => $user_database_questions_html, 'minimum_rate' => $minimum_rate, 'max_distance' => $max_distance]);
    }

    public function updateQuestions(Request $request)
    {
        $request->validate(
            [
                'min_rate.*' => 'required|numeric|min:0',
                'max_distance' => 'required',
                'ans_val_for_question_id_24' => 'required',
            ],
            collect($request->input('min_rate', []))->mapWithKeys(function ($value, $index) {
                $dayNames = [
                    0 => 'Monday',
                    1 => 'Tuesday',
                    2 => 'Wednesday',
                    3 => 'Thursday',
                    4 => 'Friday',
                    5 => 'Saturday',
                    6 => 'Sunday',
                ];
        
                $dayName = $dayNames[$index] ?? "Day $index";
        
                return [
                    "min_rate.$index.required" => "$dayName rate is required.",
                    "min_rate.$index.min" => "$dayName rate cannot be negative.",
                    "min_rate.$index.numeric" => "$dayName rate must be a valid number.",
                ];
            })->merge([
                'max_distance.required' => 'The maximum distance is required.',
                'ans_val_for_question_id_24.required' => 'Areas of Specialization is required.',
            ])->all()
        );

        $cet = $request->get('cet');

        $min_rate = $request->get('min_rate');
        $minimum_rate = json_encode([]);
        if ($min_rate && is_array($min_rate) && sizeof($min_rate) === 7) {
            $day_with_rate = array(
                'Monday' => $min_rate[0] ?? 0,
                'Tuesday' => $min_rate[1] ?? 0,
                'Wednesday' => $min_rate[2] ?? 0,
                'Thursday' => $min_rate[3] ?? 0,
                'Friday' => $min_rate[4] ?? 0,
                'Saturday' => $min_rate[5] ?? 0,
                'Sunday' => $min_rate[6] ?? 0,
            );
            $minimum_rate = json_encode($day_with_rate);
        }

        $aoc_id = $request->input('aoc_id') ?? "";
        $max_distance = $request->input('max_distance') ?? "";
        $store_list = $request->input('store_list') ?? [];
        if ($store_list && is_array($store_list) && sizeof($store_list) > 0) {
            $store_list = json_encode($store_list);
        } else {
            $store_list = json_encode([]);
        }
        $goc = $request->input('goc') ?? "";
        $aop = $request->input('aop') ?? "";
        $inshurance_company = $request->input('inshurance_company') ?? "";
        $inshurance_no = $request->input('inshurance_no') ?? "";
        $inshurance_renewal_date = $request->input('inshurance_renewal_date') ?? "";

        $userUpdatedRecord = [
            "aoc_id" => $aoc_id,
            "max_distance" => $max_distance,
            "minimum_rate" => $minimum_rate,
            "cet" => $cet,
            "goc" => $goc,
            "aop" => $aop,
            "inshurance_company" => $inshurance_company,
            "inshurance_no" => $inshurance_no,
            "inshurance_renewal_date" => $inshurance_renewal_date,
        ];
        if ($request->input('store_list') && is_array($request->input('store_list')) && sizeof($request->input('store_list')) > 0) {
            $userUpdatedRecord["site_town_ids"] = $store_list;
        }

        UserExtraInfo::where("user_id", Auth::user()->id)->update($userUpdatedRecord);

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

        return redirect(route('freelancer.dashboard'))->with("success", "Profile questions updated successfully");
    }

    public function jobListing(Request $request)
    {
        $freelancer_user_id = $request->user()->id;
        $filterJobStatusId = null;
        if ($request->has("filter") && $request->input("filter") != '') {
            $filterJobStatusId = match ($request->input("filter")) {
                'accepted' => JobPost::JOB_STATUS_ACCEPTED,
                'completed' => JobPost::JOB_STATUS_DONE_COMPLETED,
                'cancel' => JobPost::JOB_STATUS_CANCELED,
            };
        }
        $jobs = JobPost::query()->with(["job_store", "job_actions", "private_user_job_actions", "job_cancellation"]);
        if ($filterJobStatusId) {
            $jobs = $jobs->where("job_status", $filterJobStatusId);
        } else {
            $jobs = $jobs->whereIn("job_status", [JobPost::JOB_STATUS_ACCEPTED, JobPost::JOB_STATUS_DONE_COMPLETED, JobPost::JOB_STATUS_CANCELED]);
        }
        // dd($jobs->get() , 'under development');
        $jobs = $jobs->whereHas("job_actions", function ($query) use ($freelancer_user_id) {
            $query->where("freelancer_id", $freelancer_user_id);
            $query->whereIn("action", [JobAction::ACTION_ACCEPT, JobAction::ACTION_DONE, JobAction::ACTION_CANCEL_JOB_BY_FREELANCER, JobAction::ACTION_CANCEL_ACCEPTED_JOB_BY_EMPLOYER]);
        });
        // dd($jobs->get(), $freelancer_user_id);
        $job_filter_order = strtolower($request->input("order") ?? "desc") == "asc" ? "asc" : "desc";
        $job_sort_by_filter = in_array($request->input("sort_by"), ["job_date", "job_rate"]) ? $request->input("sort_by") : "job_date";

        $jobs = $jobs->orderBy($job_sort_by_filter, $job_filter_order)->paginate(20);

        foreach ($jobs as $job) {
            if ($job->job_date >= today()) {
                $cancel_action_link = "<a href='/freelancer/cancel-job/{$job->id}' title='Cancel Job' style='color: #ff0000;'><i class='fa fa-times' aria-hidden='true'></i></a>";
            } else {
                $cancel_action_link = "<a href='javascript:void(0);' style='cursor: no-drop;color: lightgray;' title='Cancel Job'><i class='fa fa-times' aria-hidden='true'></i></a>";
            }
            if ($job['job_type'] == 1) {
                $job_type = "First come first serve";
                $job_view_link = "<a href='/freelancer/single-job/{$job->id}' title='View Job'><i class='fa fa-eye' aria-hidden='true'></i></a>";
            } else {
                $job_type = "Build list";
                $job_view_link = "<a href='/build-list/{$job->id}' title='View Job'><i class='fa fa-eye' aria-hidden='true'></i></a>";
            }

            if ($job['job_status'] == JobPost::JOB_STATUS_ACCEPTED) {
                $job_status_html = "<span style='color:green;font-size: 14px;'>Accepted</span>";
            } elseif ($job['job_status'] == JobPost::JOB_STATUS_DONE_COMPLETED) {
                $job_status_html = "<span style='color:#00A9E0;font-size: 14px;'>Completed</span>";
                $cancel_action_link = "<a href='javascript:void(0);' style='cursor: no-drop;color: lightgray;' title='Cancel Job'><i class='fa fa-times' aria-hidden='true'></i></a>";
            } elseif ($job['job_status'] == JobPost::JOB_STATUS_CANCELED) {
                //check who cancelled the job
                $job_action = JobAction::where("job_post_id", $job->id)->where("freelancer_id", Auth::user()->id)->first();
                if ($job_action && ($job_action->action == JobAction::ACTION_CANCEL_ACCEPTED_JOB_BY_EMPLOYER || $job_action->action == JobAction::ACTION_CANCEL_OPEN_JOB_BY_EMPLOYER)) {
                    $job_status_html = "<span style='color:#ff0000;font-size: 14px;'>(By employer) cancelled</span>";
                } elseif ($job_action && $job_action->action == JobAction::ACTION_CANCEL_JOB_BY_FREELANCER) {
                    $job_status_html = "<span style='color:#ff0000;font-size: 14px;'>(By locum) cancelled</span>";
                } else {
                    $job_status_html = "<span style='color:#ff0000;font-size: 14px;'>Cancelled</span>";
                }
                $cancel_action_link = "<a href='javascript:void(0);' style='cursor: no-drop;color: lightgray;' title='Cancel Job'><i class='fa fa-times' aria-hidden='true'></i></a>";
            } else {
                $job_status_html = "<span style='color:red;font-size: 14px;'>Disable</sapn>";
                $cancel_action_link = "<a href='javascript:void(0);' style='cursor: no-drop;color: lightgray;' title='Cancel Job'><i class='fa fa-times' aria-hidden='true'></i></a>";
            }

            $job->cancel_action_link = $cancel_action_link;
            $job->job_type = $job_type;
            $job->job_view_link = $job_view_link;
            $job->job_status_html = $job_status_html;
        }

        return view('freelancer.job-listing', compact('filterJobStatusId', 'jobs', 'job_filter_order', 'job_sort_by_filter'));
    }

    public function privateJob(Request $request)
    {
        $freelancer_user_id = Auth::user()->id;

        // Pagination added to private jobs
        $jobs = $request->user()->private_jobs()->orderBy("job_date", "DESC")->paginate(10); // Adjust the number 10 based on how many items per page you want

        $userBlockDates = UsersWorkCalender::where("user_id", Auth::user()->id)->select("block_dates")->first();
        if ($userBlockDates) {
            $userBlockDates = json_decode($userBlockDates->block_dates) ?? [];
        } else {
            $userBlockDates = [];
        }

        // Live Job Bookings
        $allLiveJobs = JobPost::select("job_date")
            ->whereIn("job_status", [JobPost::JOB_STATUS_ACCEPTED, JobPost::JOB_STATUS_DONE_COMPLETED])
            ->whereHas("job_actions", function ($query) use ($freelancer_user_id) {
                $query->where("freelancer_id", $freelancer_user_id);
                $query->whereIn("action", [JobAction::ACTION_ACCEPT, JobAction::ACTION_DONE]);
            })->whereDate("job_date", ">=", today()->startOfMonth())->orderBy("job_date")->get();

        $liveBookDates = $allLiveJobs->map(function ($job) {
            return $job->job_date->format("Y-m-d");
        })->toArray();

        // Private jobs
        $privateJobs = $request->user()->private_jobs()->whereDate("job_date", ">=", today()->startOfMonth())->orderBy("job_date")->get();
        $privateBookDates = $privateJobs->map(function ($job) {
            return $job->job_date->format("Y-m-d");
        })->toArray();

        $bookedDates = array_merge($liveBookDates, $privateBookDates);

        // Search functionality
        if ($request->search) {
            $searchTerm = $request->search;

            $jobs = $request->user()->private_jobs()
                ->where(function ($query) use ($searchTerm) {
                    $query->where('emp_name', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('job_title', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('job_rate', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhere('job_location', 'LIKE', '%' . $searchTerm . '%')
                        ->orWhereDate('job_date', '=', $searchTerm);
                })
                ->orderBy('job_date', 'DESC')
                ->paginate(2);  // Add pagination here as well
        }
        // dd('here',$jobs);
        return view('freelancer.private-jobs', compact("jobs", "bookedDates", "userBlockDates"));
    }
    

    public function storePrivateJobs(Request $request)
    {
        $request->validate([
            "emp_name" => "required|array",
            "emp_name.*" => "required|string",
            "priv_job_title" => "required|array",
            "priv_job_title.*" => "required|string",
            "priv_job_rate" => "required|array",
            "priv_job_rate.*" => "required|numeric|gt:0",
            "priv_job_location" => "required|array",
            "priv_job_start_date" => "required|array",
            "priv_job_start_date.*" => "required|date_format:d/m/Y",
        ]);

        $employers_names = $request->input("emp_name");
        $priv_job_titles = $request->input("priv_job_title");
        $priv_job_rates = $request->input("priv_job_rate");
        $priv_job_locations = $request->input("priv_job_location");
        $priv_job_start_dates = $request->input("priv_job_start_date");
        $employers_emails = $request->input("emp_email", []);

        $total_private_jobs = sizeof($employers_names);

        if ($total_private_jobs != sizeof($priv_job_titles) || $total_private_jobs != sizeof($priv_job_rates) || $total_private_jobs != sizeof($priv_job_locations) || $total_private_jobs != sizeof($priv_job_start_dates)) {
            return back()->with("error", "Please fill all fields");
        }

        $data = array();
        $past_jobs_private_finances = array();
        $past_jobs_finance_incomes = array();
        $last_private_job_id = optional($request->user()->private_jobs()->orderBy("id", "DESC")->first())->id ?? 0;
        $last_private_job_id = $last_private_job_id + 1;
        for ($i = 0; $i < $total_private_jobs; $i++) {
            $job_date = date('Y-m-d', strtotime(str_replace('/', '-', $priv_job_start_dates[$i])));
            $private_job = array();
            $private_job['freelancer_id'] = Auth::user()->id;
            $private_job['emp_name'] = $employers_names[$i];
            $private_job['job_title'] = $priv_job_titles[$i];
            $private_job['job_rate'] = $priv_job_rates[$i];
            $private_job['job_location'] = $priv_job_locations[$i];
            $private_job['job_date'] = $job_date;
            if (isset($employers_emails[$i]) && $employers_emails[$i]) {
                $private_job['emp_email'] = $employers_emails[$i];
            }
            $private_job['created_at'] = now();
            $private_job['updated_at'] = now();

            //Past date private jobs
            if (today()->greaterThan($job_date)) {
                $past_jobs_private_finances[] = [
                    "freelancer_id" => Auth::user()->id,
                    "freelancer_private_job_id" => $last_private_job_id,
                    "job_rate" => $priv_job_rates[$i],
                    "job_date" => $job_date,
                    "employer_name" => $employers_names[$i],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                $past_jobs_finance_incomes[] = [
                    "job_id" => $last_private_job_id,
                    "job_type" => 2,
                    "freelancer_id" => Auth::user()->id,
                    "employer_id" => null,
                    "job_rate" => $priv_job_rates[$i],
                    "job_date" => $job_date,
                    "income_type" => 1,
                    "is_bank_transaction_completed" => false,
                    "bank_transaction_date" => null,
                    "store" => $employers_names[$i],
                    "location" => $priv_job_locations[$i],
                    "supplier" => $employers_names[$i],
                    "status" => 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            $data[] = $private_job;
            $last_private_job_id += 1;
        }
        try {
            FreelancerPrivateJob::insert($data);
        } catch (Throwable) {
            return back()->with("error", "Some error occured during record insertion. Some of your record are not able to insert. Please check all fields and try again");
        }
        try {
            if (sizeof($past_jobs_private_finances) > 0) {
                FreelancerPrivateFinance::insert($past_jobs_private_finances);
            }
            if (sizeof($past_jobs_finance_incomes) > 0) {
                FinanceIncome::insert($past_jobs_finance_incomes);
            }
        } catch (Throwable) {
        }

        return redirect()->back()->with('success', 'private jobs inserted successfully!');
        return back()->with("successs", "{$total_private_jobs} private jobs inserted successfully");
    }

    public function updatePrivateJobs(Request $request)
    {
        $request->validate([
            "job_id" => "required|array",
            "emp_name" => "required|array",
            "emp_name.*" => "required|string",
            "priv_job_title" => "required|array",
            "priv_job_title.*" => "required|string",
            "priv_job_rate" => "required|array",
            "priv_job_rate.*" => "required|numeric|gt:0",
            "priv_job_location" => "required|array",
            "priv_job_start_date" => "required|array",
            "priv_job_start_date.*" => "required|date_format:d/m/Y",
        ]);

        $job_ids = $request->input("job_id");
        $employers_names = $request->input("emp_name");
        $priv_job_titles = $request->input("priv_job_title");
        $priv_job_rates = $request->input("priv_job_rate");
        $priv_job_locations = $request->input("priv_job_location");
        $priv_job_start_dates = $request->input("priv_job_start_date");
        $employers_emails = $request->input("emp_email", []);

        $unique_dates = array_unique($priv_job_start_dates);
        if (sizeof($unique_dates) != sizeof($priv_job_start_dates)) {
            return back()->with("error", "You choosed same dates for different private jobs");
        }

        $total_private_jobs = sizeof($job_ids);
        if ($total_private_jobs != sizeof($employers_names) || $total_private_jobs != sizeof($priv_job_titles) || $total_private_jobs != sizeof($priv_job_rates) || $total_private_jobs != sizeof($priv_job_locations) || $total_private_jobs != sizeof($priv_job_start_dates)) {
            return back()->with("error", "Please fill all fields");
        }
        try {
            DB::beginTransaction();
            for ($i = 0; $i < $total_private_jobs; $i++) {
                $job_id = $job_ids[$i];
                $private_job = FreelancerPrivateJob::find($job_id);
                if ($private_job) {
                    $job_date = date('Y-m-d', strtotime(str_replace('/', '-', $priv_job_start_dates[$i])));
                    $private_job['freelancer_id'] = Auth::user()->id;
                    $private_job['emp_name'] = $employers_names[$i];
                    $private_job['job_title'] = $priv_job_titles[$i];
                    $private_job['job_rate'] = $priv_job_rates[$i];
                    $private_job['job_location'] = $priv_job_locations[$i];
                    $private_job['job_date'] = $job_date;
                    if (isset($employers_emails[$i]) && $employers_emails[$i]) {
                        $private_job['emp_email'] = $employers_emails[$i];
                    }
                    $private_job->save();
                }
            }
            DB::commit();
        } catch (Exception) {
            return back()->with("error", "Data insertion error. Please check your inputs");
        }

        return back()->with("success", "Data updated successfully");
    }

    public function financeDetail(Request $request)
    {
        $finance_helper = new FinanceHelper(Auth::user());

        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $user_finance_type = $finance_helper->get_user_finance_type();

        $filter_year = $request->has("year") ? intval($request->input("year")) : date('Y');
        $income_filter = $request->has("income-filter") ? $request->input("income-filter") : 'month';
        $expense_filter = $request->has("expense-filter") ? $request->input("expense-filter") : 'month';

        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];

        $income_records = FinanceIncome::query()->where("freelancer_id", Auth::user()->id)->whereBetween("job_date", [$year_start, $year_end])->get();
        $expense_records = FinanceExpense::query()->where("freelancer_id", Auth::user()->id)->whereBetween("job_date", [$year_start, $year_end])->get();

        //year_start, year_end or upto
        $income_chart_data = $finance_helper->get_chart_finance_data(FinanceIncome::query(), $year_start, $year_end, $finance_year_start_month, $income_filter);
        $expense_chart_data = $finance_helper->get_chart_finance_data(FinanceExpense::query(), $year_start, $year_end, $finance_year_start_month, $expense_filter);

        $total_income = $finance_helper->get_user_total_income($filter_year, $finance_year_start_month);
        $total_expense = $finance_helper->get_user_total_expense($filter_year, $finance_year_start_month);
        $user_total_tax = $finance_helper->user_tax_calculation($finance_year_start_month, $total_income - $total_expense, $user_finance_type, $filter_year);

        return view("freelancer.finance.finance-detail", compact('finance_year_start_month', 'total_income', 'total_expense', 'user_total_tax', 'income_records', 'income_chart_data', 'expense_records', 'expense_chart_data', 'filter_year', 'income_filter', 'expense_filter'));
    }

    public function showAddIncome()
    {
        return view('freelancer.finance.add-income');
    }
    public function showEditIncome($id)
    {
        $income = FinanceIncome::query()->where("id", $id)->where("freelancer_id", Auth::user()->id)->first();
        if ($income == null) {
            return abort(404);
        }
        return view('freelancer.finance.edit-income', compact('income'));
    }

    public function saveIncome(Request $request)
    {
        $request->validate([
            "in_store" => ["required", "max:255", "regex:/^[a-zA-Z\s]+$/"],
            "in_location" => ["required", "max:255"],
            "in_supplier" => ["required", "max:255", "regex:/^[a-zA-Z\s]+$/"],
        ]);
        
       
        $in_emp_id = $request->input("in_emp_id");
        $in_job_type = $request->input("in_job_type");
        $in_jobno = $request->input("in_jobno");
        $in_date = $request->input("in_date");
        $in_rate = $request->input("in_rate");
        $in_store = $request->input("in_store");
        $in_income_type = $request->input("in_category");
        $in_location = $request->input("in_location");
        $in_supplier = $request->input("in_supplier");
        $in_bank = $request->input("in_bank");
        $in_bankdate = $request->input("in_bankdate");
        $income_submit = $request->input("income_submit");

        $is_record_exists = FinanceIncome::where("job_id", $in_jobno)
            ->where("job_type", $in_job_type)
            ->where("freelancer_id", Auth::user()->id)
            ->whereDate("job_date", $in_date)
            ->where("income_type", $in_income_type)->count();

        if ($is_record_exists > 0) {
            return back()->with("error", "Record already exists");
        }

        $is_bank_transaction_completed = $in_bank == "1" ? true : false;
        FinanceIncome::create([
            "job_id" => $in_jobno,
            "job_type" => $in_job_type,
            "freelancer_id" => Auth::user()->id,
            "employer_id" => $in_emp_id,
            "job_rate" => $in_rate,
            "job_date" => $in_date,
            "income_type" => $in_income_type,
            "is_bank_transaction_completed" => $is_bank_transaction_completed,
            "bank_transaction_date" => $in_bankdate,
            "store" => $in_store,
            "location" => $in_location,
            "supplier" => $in_supplier,
            "status" => 1,
        ]);

        return redirect("/freelancer/finance")->with("success", "Record addedd successfully");
    }

    public function updateIncome(Request $request, $id)
    {
        $income = FinanceIncome::query()->where("id", $id)->where("freelancer_id", Auth::user()->id)->count();
        if ($income == 0) {
            return abort(404);
        }
        $in_emp_id = $request->input("in_emp_id");
        $in_job_type = $request->input("in_job_type");
        $in_jobno = $request->input("in_jobno");
        $in_date = $request->input("in_date");
        $in_rate = $request->input("in_rate");
        $in_store = $request->input("in_store");
        $in_income_type = $request->input("in_category");
        $in_location = $request->input("in_location");
        $in_supplier = $request->input("in_supplier");
        $in_bank = $request->input("in_bank");
        $in_bankdate = $request->input("in_bankdate");

        $is_record_exists = FinanceIncome::where("id", "!=", $id)
            ->where("job_id", $in_jobno)
            ->where("job_type", $in_job_type)
            ->where("freelancer_id", Auth::user()->id)
            ->whereDate("job_date", $in_date)
            ->where("income_type", $in_income_type)->count();

        if ($is_record_exists > 0) {
            return back()->with("error", "Record conflicted with already present record");
        }

        $is_bank_transaction_completed = $in_bank == "1" ? true : false;

        if ($is_bank_transaction_completed == false) {
            $in_bankdate = null;
        }

        FinanceIncome::where("id", $id)->update([
            "job_id" => $in_jobno,
            "job_type" => $in_job_type,
            "employer_id" => $in_emp_id,
            "job_rate" => $in_rate,
            "job_date" => $in_date,
            "income_type" => $in_income_type,
            "is_bank_transaction_completed" => $is_bank_transaction_completed,
            "bank_transaction_date" => $in_bankdate,
            "store" => $in_store,
            "location" => $in_location,
            "supplier" => $in_supplier,
        ]);

        return redirect("/freelancer/finance")->with("success", "Record updated successfully");
    }

    public function showAddExpense()
    {
        $expense_categories = ExpenseType::all();
        return view('freelancer.finance.add-expense', compact('expense_categories'));
    }
    public function showEditExpense($id)
    {
        $expense_categories = ExpenseType::all();
        $expense = FinanceExpense::query()->where("id", $id)->where("freelancer_id", Auth::user()->id)->first();
        if ($expense == null) {
            return abort(404);
        }
        return view('freelancer.finance.edit-expense', compact('expense_categories', 'expense'));
    }

    public function saveExpense(Request $request)
    {
        $ex_job_type = $request->input("ex_job_type");
        $ex_job_id = $request->input("ex_job_id");
        $ex_job_date = $request->input("ex_job_date");
        $ex_job_cost = $request->input("ex_job_cost");
        $ex_job_description = $request->input("ex_job_description");
        $ex_category = $request->input("ex_category");
        $ex_bank = $request->input("ex_bank");
        $ex_bank_date = $request->input("ex_bank_date");
        $receipt = $request->file("receipt");

        $is_bank_transaction_completed = $ex_bank === "on" ? true : false;
        $expense = new FinanceExpense();
        $expense->job_id = $ex_job_id;
        $expense->job_type = $ex_job_type;
        $expense->freelancer_id = Auth::user()->id;
        $expense->job_rate = $ex_job_cost;
$expense->job_date = \Carbon\Carbon::createFromFormat('d/m/Y', $ex_job_date)->format('Y-m-d');
        $expense->expense_type_id = $ex_category;
        $expense->description = $ex_job_description;
        $expense->is_bank_transaction_completed = $is_bank_transaction_completed;
        $expense->bank_transaction_date =$ex_bank_date? \Carbon\Carbon::createFromFormat('d/m/Y', $ex_bank_date)->format('Y-m-d'):null ;

        if ($receipt) {
            $fileName = "receipt-" . time() . "-" . $receipt->getClientOriginalName();
            $filePath = public_path("/media/receipt");
            $receipt->move($filePath, $fileName);
            $expense->receipt = "/media/receipt/" . $fileName;
        }



   
        $expense->save();

        return redirect("/freelancer/finance")->with("success", "Record addedd successfully");
    }

    public function updateExpense(Request $request, $id)
    {
        
        $expense = FinanceExpense::findOrFail($id);
        $receipt = $request->file("receipt");
        // if ($receipt && $expense->receipt && file_exists(public_path($expense->receipt))) {
        //     unlink($expense->receipt);
        // }

        $ex_job_type = $request->input("ex_job_type");
        $ex_job_id = $request->input("ex_job_id");
        $ex_job_date = $request->input("ex_job_date");
        $ex_job_cost = $request->input("ex_job_cost");
        $ex_job_description = $request->input("ex_job_description");
        $ex_category = $request->input("ex_category");
        $ex_bank = $request->input("ex_bank");
        $ex_bank_date = $request->input("ex_bank_date");

        $is_bank_transaction_completed = $ex_bank === "on" ? true : false;
        if ($is_bank_transaction_completed == false) {
            $ex_bank_date = null;
        }

        $expense->job_id = $ex_job_id;
        $expense->job_type = $ex_job_type;
        $expense->job_rate = $ex_job_cost;
        $expense->job_date = $ex_job_date;
        $expense->expense_type_id = $ex_category;
        $expense->description = $ex_job_description;
        $expense->is_bank_transaction_completed = $is_bank_transaction_completed;
        $expense->bank_transaction_date = $ex_bank_date;

        if ($receipt) {
            $fileName = "receipt-" . time() . "-" . $receipt->getClientOriginalName();
            $filePath = public_path("/media/receipt");
            $receipt->move($filePath, $fileName);
            $expense->receipt = "/media/receipt/" . $fileName;
        }
        $expense->update();
        
        return redirect("/freelancer/finance")->with("success", "Record updated successfully");
    }

    public function showSupplierList()
    {
        $suppliers = Supplier::where("created_by_user_id", Auth::user()->id)->get();
        return view('freelancer.finance.supplier-list', compact('suppliers'));
    }
    public function showAddSupplier()
    {
        return view('freelancer.finance.add-supplier');
    }
    public function saveSupplier(Request $request)
    {
        $request->validate([
            "cname" => ["required", "string", "max:255", "regex:/^[a-zA-Z\s]+$/"],
            "sname" => ["required", "string", "max:255"],
            "address" => ["required", "string", "max:255"],
            "addresssec" => ["nullable", "string", "max:255"],
            "town" => ["required", "string", "max:255"],
            "country" => ["required", "string", "max:255"],
                'postal_code' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->country === 'US' && !preg_match('/^\d{5}(?:-\d{4})?$/', $value)) {
                        $fail('Please enter a valid US ZIP code (e.g., 12345 or 12345-6789)');
                    }
                    if ($request->country === 'UK' && !preg_match('/^([A-Za-z][A-Ha-hJ-Yj-y]?[0-9][A-Za-z0-9]? ?[0-9][A-Za-z]{2})$/i', $value)) {
                        $fail('Please enter a valid UK postcode (e.g., SW1A 1AA or M1 1AA)');
                    }
                }
            ],
            // "postcode" => ["required", "string", "max:10", "regex:/^[a-zA-Z0-9\s\-]+$/"],
            "cnumber" => ["required", "numeric", "digits_between:7,20"],
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:suppliers,email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.com$/'
            ],

        ]);

        Supplier::create([
            "name" => $request->input("cname"),
            "store_name" => $request->input("sname"),
            "address" => trim($request->input("address")),
            "second_address" => trim($request->input("addresssec")),
            "town" => $request->input("town"), 
            "country" => $request->input("country"),
            "postcode" => $request->input("postcode"),
            "contact_no" => $request->input("cnumber"),
            "email" => $request->input("email"),
            'created_by_user_id' => Auth::user()->id
        ]);

        return redirect(route('freelancer.manage-supplier'))->with("success", "Supplier added successfully");
    }

    public function showEditSupplier($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('freelancer.finance.add-supplier', compact('supplier'));
    }

    public function updateSupplier(Request $request, $id)
    {
        $request->validate([
            "cname" => ["required", "string", "max:255", "regex:/^[a-zA-Z\s]+$/"],
            "sname" => ["required", "string", "max:255"],
            "address" => ["required", "string", "max:255"],
            "addresssec" => ["nullable", "string", "max:255"],
            "town" => ["required", "string", "max:255"],
            "country" => ["required", "string", "max:255"],
            "postcode" => ["required", "string", "max:10", "regex:/^[a-zA-Z0-9\s\-]+$/"],
            "cnumber" => ["required", "numeric", "digits_between:7,20"],
            "email" => ["required", "email", "max:255"],
        ]);

        Supplier::where("id", $id)->where("created_by_user_id", Auth::user()->id)->update([
            "name" => $request->input("cname"),
            "store_name" => $request->input("sname"),
            "address" => trim($request->input("address")),
            "second_address" => trim($request->input("addresssec")),
            "town" => $request->input("town"),
            "country" => $request->input("country"),
            "postcode" => $request->input("postcode"),
            "contact_no" => $request->input("cnumber"),
            "email" => $request->input("email"),
        ]);

        return redirect(route('freelancer.manage-supplier'))->with("success", "Supplier updated successfully");
    }

    public function incomeBySupplier(Request $request)
    {
        $finance_helper = new FinanceHelper(Auth::user());
        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $filter_year = $request->has("year") ? $request->input("year") : date('Y');
        $income_filter = $request->has("income-filter") ? $request->input("income-filter") : 'month';
        $supplier_filter = $request->has("supplier") ? $request->input("supplier") : null;

        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];

        $suppliers = FinanceIncome::query()->where("freelancer_id", Auth::user()->id)->select("supplier")->distinct()->pluck("supplier")->toArray();

        $income_chart_data = $finance_helper->get_chart_finance_data(FinanceIncome::query(), $year_start, $year_end, $finance_year_start_month, $income_filter, true, false, $supplier_filter ? 'supplier' : null, $supplier_filter);
        $pie_supplier_chart_data = $finance_helper->get_chart_by_supplier($year_start, $year_end, $supplier_filter);

        $income_records = FinanceIncome::query()->where("freelancer_id", Auth::user()->id)->whereBetween("job_date", [$year_start, $year_end]);
        if ($supplier_filter && $supplier_filter != "") {
            $income_records = $income_records->where("supplier", $supplier_filter);
        }
        $income_records = $income_records->get();

        return view('freelancer.finance.income-by-supplier', compact('finance_year_start_month', 'filter_year', 'income_filter', 'income_records', 'suppliers', 'supplier_filter', 'income_chart_data', 'pie_supplier_chart_data'));
    }

    public function bankDetails()
    {
        $detail = UserBankDetail::where("user_id", Auth::user()->id)->first();
        return view('freelancer.finance.bank-details', compact('detail'));
    }
    public function saveBankDetails(Request $request)
    {
        $request->validate([
            "acc_name" => ["required", "string", "max:255", "regex:/^[a-zA-Z\s]+$/"],
            "acc_number" => ["required", "numeric"],
            "acc_sort_code" => ["required", "max:8"],
        ]);

        UserBankDetail::updateOrCreate(["user_id" => Auth::user()->id], [
            "acccount_name" => $request->input("acc_name"),
            "acccount_number" => $request->input("acc_number"),
            "acccount_sort_code" => $request->input("acc_sort_code"),
        ]);

        return back()->with("success", "Details update successfully");
    }

    public function showOpenInvoices(Request $request)
    {
        $finance_helper = new FinanceHelper(Auth::user());

        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $filter_year = $request->has("year") ? $request->input("year") : date('Y');

        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];

        $income_records = FinanceIncome::query()->where("freelancer_id", Auth::user()->id)->whereBetween("job_date", [$year_start, $year_end])->where("is_bank_transaction_completed", false)->get();

        return view('freelancer.finance.open-invoices', compact('finance_year_start_month', 'filter_year', 'income_records'));
    }

    public function updateInvoice(Request $request)
    {
        $income_id = $request->input("id_income");
        $income = FinanceIncome::where("id", $income_id)->where("freelancer_id", Auth::user()->id)->first();
        if ($income) {
            $invoice_req_val = $request->input("invoice-req-val") == "1" ? true : false;
            $income->is_invoice_required = $invoice_req_val;
            $income->save();
            return back()->with("success", "Status updated successfully");
        }
        return abort(404);
    }

    public function sendInvoice($id)
    {
        $income_record = FinanceIncome::where("id", $id)->where("freelancer_id", Auth::user()->id)->whereNull("invoice_id")->first();
        $user_invoice_data = get_user_data_for_invoice(Auth::user());
        $suppliers = Supplier::query()->active()->where("created_by_user_id", Auth::user()->id)->get();
        if ($income_record) {
            return view('freelancer.finance.send-invoice', compact('income_record', 'user_invoice_data', 'suppliers'));
        }
        return abort(404);
    }

    public function sendAndSaveInvoice(Request $request)
    {
        $request->validate([
            "income_id" => ["required"],
            // "job_id" => ["required"],
            "job_date" => ["required", "date"],
            "job_rate" => ["required"],
            'your_email' => ['required', 'email'],
            'your_name' => ['required', 'string'],
            'your_address' => ['required', 'string'],
            'your_contact' => ['required', 'string'],
            'supplier_store' => ['required', 'string'],
            'supplier_name' => ['required', 'string'],
            'supplier_email' => ['required', 'email'],
            'supplier_address' => ['required', 'string'],
            'supplier_town' => ['required', 'string'],
            'supplier_country' => ['required', 'string'],
            'supplier_postcode' => ['required', 'string'],
            'acc_name' => ["required", "string", "max:255"],
            'acc_number' => ["required", "numeric"],
            'acc_sort_code' => ["required", "max:8"],
            'template-choice' => ['required', 'in:invoice1,invoice2'],
        ]);

        $income_id = $request->input("income_id");
        $finance_income = FinanceIncome::where("id", $income_id)->where("freelancer_id", Auth::user()->id)->first();
        if (!$finance_income) {
            return abort(404);
        }

        $supplier_email = $request->input("supplier_email");
        $admin_mail = config('app.admin_mail');
        $job_rate = $request->input("job_rate");
        $user_id = Auth::user()->id;

        $invoice = Invoice::create([
            "to_email" => $supplier_email,
            "from_email" => $admin_mail,
            "amount" => $job_rate,
            "user_id" => $user_id,
        ]);

        $data = $request->all(["job_id", "job_date", "job_rate", "your_email", "your_name", "your_address", "your_contact", "supplier_store", "supplier_id", "supplier_name", "supplier_email", "supplier_address", "supplier_town", "supplier_country", "supplier_postcode", "acc_name", "acc_number", "acc_sort_code"]);
        $data["invoice_no"] = $invoice->id;

        $invoice_file_name = "user-invoice-{$invoice->id}-" . time() . "-ganerated.pdf";

        $template = $request->input("template-choice");

        $invoice_html = view("components.invoice-templates.{$template}", compact('data'))->render();
        // dd('here', $invoice_file_name, $data , $request->all() , $invoice_html);
        try {
            // $pdf = Pdf::loadView("components.invoice-templates.layout", ["html" => $invoice_html]);
            // $pdf->save(storage_path("app/invoices/{$invoice_file_name}"));
            
            $invoicePath = storage_path("app/invoices/");
            if (!file_exists($invoicePath)) {
                mkdir($invoicePath, 0755, true);
            }
            
            $pdf = Pdf::loadView("components.invoice-templates.layout", ["html" => $invoice_html]);
            
            $pdf->save($invoicePath . $invoice_file_name);

        } catch (Exception $ingore) {
            return ['here in exception', $invoice_file_name];
            $invoice->delete();
        }
        $pdf_generated_file = storage_path("app/invoices/{$invoice_file_name}");

        if (!file_exists($pdf_generated_file)) {
            $invoice->delete();
            return back()->with("error", "Pdf file not found. Try again");
        }

        $invoice->pdf_file_path = $pdf_generated_file;
        $invoice->save();

        //Send invoice as email to supplier
        $supplier = [
            "email" => $data["supplier_email"]
        ];
        $sent = Mail::to($supplier)->send(new IncomeInvoiceMail($pdf_generated_file, $data));
        if ($sent) {
            $finance_income->invoice_id = $invoice->id;
            $finance_income->save();
        } else {
            $invoice->delete();
            return back()->with("error", "Not able to send email to supplier");
        }

        return redirect(route('freelancer.open-invoices'))->with("success", "Invoice generated and email to supplier successfully");
    }

    public function showReports()
    {

        $finance_helper = new FinanceHelper(Auth::user());

        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $user_finance_type = $finance_helper->get_user_finance_type();

        $filter_year = date('Y');

        $total_income = $finance_helper->get_user_total_income($filter_year, $finance_year_start_month);
        $total_expense = $finance_helper->get_user_total_expense($filter_year, $finance_year_start_month);
        $user_total_tax = $finance_helper->user_tax_calculation($finance_year_start_month, $total_income - $total_expense, $user_finance_type, $filter_year);

        return view('freelancer.finance.reports', compact('total_income', 'total_expense', 'user_total_tax'));
    }

    public function showCashMovementReport(Request $request)
    {
        $finance_helper = new FinanceHelper(Auth::user());

        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $user_finance_type = $finance_helper->get_user_finance_type();

        $filter_year = $request->has("year") ? $request->input("year") : date('Y');
        $filter = $request->has("filter") ? $request->input("filter") : 'month';

        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];

        $income_chart_data = $finance_helper->get_chart_finance_data(FinanceIncome::query(), $year_start, $year_end, $finance_year_start_month, $filter, false, true);
        $expense_chart_data = $finance_helper->get_chart_finance_data(FinanceExpense::query(), $year_start, $year_end, $finance_year_start_month, $filter, false, true);

        $income_records = FinanceIncome::query()->where("freelancer_id", Auth::user()->id)->where("is_bank_transaction_completed", true)->whereBetween("job_date", [$year_start, $year_end])->get();
        $expense_records = FinanceExpense::query()->where("freelancer_id", Auth::user()->id)->where("is_bank_transaction_completed", true)->whereBetween("job_date", [$year_start, $year_end])->get();

        $all_transactions = $income_records->concat($expense_records)->sortBy(["job_date" => "desc"]);

        $total_income = $finance_helper->get_user_total_income($filter_year, $finance_year_start_month);
        $total_expense = $finance_helper->get_user_total_expense($filter_year, $finance_year_start_month);
        $user_total_tax = $finance_helper->user_tax_calculation($finance_year_start_month, $total_income - $total_expense, $user_finance_type, $filter_year);

        return view('freelancer.finance.cash-movement-report', compact('finance_year_start_month', 'total_income', 'total_expense', 'user_total_tax', 'filter_year', 'filter', 'income_chart_data', 'expense_chart_data', 'all_transactions'));
    }

    public function showWeeklyReport(Request $request)
    {

        $finance_helper = new FinanceHelper(Auth::user());

        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $user_finance_type = $finance_helper->get_user_finance_type();

        $filter_year = $request->has("year") ? $request->input("year") : date('Y');
        $filter = $request->has("filter") ? $request->input("filter") : 'month';

        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];

        $income_by_day_data = get_abbrevated_days_list();
        $job_count_by_day_data = get_abbrevated_days_list();

        $income_records = FinanceIncome::query()->select(DB::raw("SUM(job_rate) as total_amount"), DB::raw("DATE_FORMAT(job_date, '%a') as day"), DB::raw("COUNT(id) as job_count"))->where("freelancer_id", Auth::user()->id)->whereBetween("job_date", [$year_start, $year_end]);
        $income_records = $income_records->groupBy("day")->get();
        foreach ($income_records as $record) {
            $income_by_day_data[$record->day] = $record->total_amount;
            $job_count_by_day_data[$record->day] = $record->job_count;
        }

        return view('freelancer.finance.weekly-report', compact('finance_year_start_month', 'job_count_by_day_data', 'income_by_day_data', 'filter_year'));
    }

    public function feedbackDetails()
    {
        $userType = 'Employer(s)';
        $feedbacks = JobFeedback::with('employer')->where("freelancer_id", Auth::user()->id)->where("user_type", "employer")->where("status", 1)->whereDate("created_at", ">=", today()->subMonths(6)->startOfMonth())->get();
        $overall_rating = get_overall_feedback_rating($feedbacks);

        return view('freelancer.feedback-detail', compact('feedbacks', 'overall_rating'));
    }

    public function showHelpJobBooking()
    {
        return view('freelancer.job-booking-freelancer');
    }

    public function showHelpFinanceModel()
    {
        return view('freelancer.finance-model-freelancer');
    }

    public function showAllTransactions(Request $request)
    {
        $show = $request->input("show", "all");
        $finance_helper = new FinanceHelper(Auth::user());

        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();

        $filter_year = $request->has("year") ? $request->input("year") : date('Y');

        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];

        if ($show == "income") {
            $records = FinanceIncome::query()->where("freelancer_id", Auth::user()->id)->whereBetween("job_date", [$year_start, $year_end])->get();
        } else if ($show == "expense") {
            $records = FinanceExpense::query()->where("freelancer_id", Auth::user()->id)->whereBetween("job_date", [$year_start, $year_end])->get();
        } else {
            $income_records = FinanceIncome::query()->where("freelancer_id", Auth::user()->id)->whereBetween("job_date", [$year_start, $year_end])->get();
            $expense_records = FinanceExpense::query()->where("freelancer_id", Auth::user()->id)->whereBetween("job_date", [$year_start, $year_end])->get();
            $records = $income_records->concat($expense_records)->sortBy(["job_date" => "desc"]);
        }
        $total_income = $finance_helper->get_user_total_income($filter_year, $finance_year_start_month);
        $total_expense = $finance_helper->get_user_total_expense($filter_year, $finance_year_start_month);
       
        return view('freelancer.finance.all-transactions', compact('show', 'records', 'total_income', 'total_expense', 'filter_year', 'finance_year_start_month'));
    }

    public function exportAllTransactions(Request $request)
    {
        $type = $request->input("type");
        $export_type = $request->input("export_type");
        if (in_array($export_type, ["pdf", "csv", "xlsx"]) == false) {
            return back()->with("error", "Choose a export type");
        }
        $finance_helper = new FinanceHelper(Auth::user());

        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();

        $filter_year = $request->has("year") ? $request->input("year") : date('Y');

        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];

        if ($type == "income") {
            $records = FinanceIncome::query()->where("freelancer_id", Auth::user()->id)->whereBetween("job_date", [$year_start, $year_end])->get();
        } else if ($type == "expense") {
            $records = FinanceExpense::query()->with("expense_type")->where("freelancer_id", Auth::user()->id)->whereBetween("job_date", [$year_start, $year_end])->get();
        } else {
            $income_records = FinanceIncome::query()->where("freelancer_id", Auth::user()->id)->whereBetween("job_date", [$year_start, $year_end])->get();
            $expense_records = FinanceExpense::query()->with("expense_type")->where("freelancer_id", Auth::user()->id)->whereBetween("job_date", [$year_start, $year_end])->get();
            $records = $income_records->concat($expense_records)->sortByDesc("job_date");
        }

        $financeyear = $finance_helper->getMonthFinancialYear($finance_year_start_month, $filter_year); //2018-2019

        $exportable_array_main = array();
        foreach ($records as $record) {
            if (is_a($record, FinanceIncome::class)) {
                $type = 'income';
                $id = "INC#" . $record->id;
                $inner_type = $record->get_income_type();
            } else {
                $type = 'expense';
                $id = "EXP#" . $record->id;
                $inner_type = $record->expense_type?->expense;
            }
            $exportable_array["#"] = $record->getTransactionNumber();
            $exportable_array["Type"] = $type;
            $exportable_array["Date"] = get_date_with_default_format($record->job_date);
            $exportable_array["Amount"] = set_amount_format($record->job_rate);
            $exportable_array["Category"] = $inner_type;
            $exportable_array["Banked"] = $record->is_bank_transaction_completed ? "Already Banked" : "Pending";
            $exportable_array["Bank Date"] = $record->bank_transaction_date ? get_date_with_default_format($record->bank_transaction_date) : "N/A";

            array_push($exportable_array_main, $exportable_array);
        }
        
      
        if ($export_type === "pdf") {
            $financeyear = $finance_helper->getMonthFinancialYear($finance_year_start_month, $filter_year); // e.g., 2023-2024
            $title = "Financial Transactions Report ({$financeyear})";
        
            $total_income = $records->whereInstanceOf(FinanceIncome::class)->sum('job_rate');
            $total_expense = $records->whereInstanceOf(FinanceExpense::class)->sum('job_rate');
        
            return PDF::loadView('exports.transactions', [
                'records' => $exportable_array_main,
                'title' => $title,
                'total_income' => $total_income,
                'total_expense' => $total_expense,
                'financeyear' => $financeyear,
            ])->download('transactions_' . $financeyear . '.pdf');
        }


        $file_name = 'transactions_' . $financeyear . '.' . $export_type;

        return Excel::download(new TransactionExport($exportable_array_main), $file_name);
    }

    public function updateBankTransaction(Request $request)
    {
        $request->validate([
            'in_bankid' => 'required',
            'in_bankdate' => 'required|date'
        ]);

        $income_id = $request->input("in_bankid");
        $date = $request->input("in_bankdate");

        $income = FinanceIncome::where("id", $income_id)->where("freelancer_id", Auth::user()->id)->first();
        if (!$income) {
            return abort(404);
        }

        $income->is_bank_transaction_completed = true;
        $income->bank_transaction_date = Carbon::parse($date)->format('Y-m-d');
        $income->save();

        return back()->with("success", "Bank transaction updated from income record");
    }

    public function updateBankTransactionExpense(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'ex_bankid' => 'required',
            'ex_bankdate' => 'required|date'
        ]);
        $expense_id = $request->input("ex_bankid");
        $date = $request->input("ex_bankdate");

        $expense = FinanceExpense::where("id", $expense_id)->where("freelancer_id", Auth::user()->id)->first();
        if (!$expense) {
            return abort(404);
        }

        $expense->is_bank_transaction_completed = true;
        $expense->bank_transaction_date = Carbon::parse($date)->format('Y-m-d');
        $expense->save();

        return back()->with("success", "Bank transaction updated from expense record");
    }

    public function incomeByArea(Request $request)
    {
        $finance_helper = new FinanceHelper(Auth::user());
        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $filter_year = $request->has("year") ? $request->input("year") : date('Y');
        $income_filter = $request->has("income-filter") ? $request->input("income-filter") : 'month';

        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];

        $pie_location_chart_data = $finance_helper->get_chart_by_location($year_start, $year_end);

        $income_records = FinanceIncome::query()->where("freelancer_id", Auth::user()->id)->whereBetween("job_date", [$year_start, $year_end]);
        $income_records = $income_records->get();

        return view('freelancer.finance.income-by-area', compact('finance_year_start_month', 'filter_year', 'income_filter', 'income_records', 'pie_location_chart_data'));
    }

    public function incomeByCategory(Request $request)
    {
        $finance_helper = new FinanceHelper(Auth::user());
        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $filter_year = $request->has("year") ? $request->input("year") : date('Y');
        $income_filter = $request->has("income-filter") ? $request->input("income-filter") : 'month';
        $category_filter = $request->has("category") ? $request->input("category") : null;

        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];

        $categories = FinanceIncome::get_income_type_categories_list();

        $income_chart_data = $finance_helper->get_chart_finance_data(FinanceIncome::query(), $year_start, $year_end, $finance_year_start_month, $income_filter, true, false, $category_filter ? "income_type" : null, $category_filter);
        $pie_category_chart_data = $finance_helper->get_chart_by_income_type($year_start, $year_end, $category_filter);

        $income_records = FinanceIncome::query()->where("freelancer_id", Auth::user()->id)->whereBetween("job_date", [$year_start, $year_end]);
        if ($category_filter && $category_filter != "") {
            $income_records = $income_records->where("income_type", $category_filter);
        }
        $income_records = $income_records->get();

        return view('freelancer.finance.income-by-category', compact('finance_year_start_month', 'filter_year', 'income_filter', 'income_records', 'categories', 'category_filter', 'income_chart_data', 'pie_category_chart_data'));
    }

    public function expensesTypeFilter(Request $request)
    {
        $finance_helper = new FinanceHelper(Auth::user());
        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $filter_year = $request->has("year") ? $request->input("year") : date('Y');
        $income_filter = $request->has("income-filter") ? $request->input("income-filter") : 'month';
        $category_filter = $request->has("category") ? $request->input("category") : null;

        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];

        $categories = ExpenseType::all();

        $expense_chart_data = $finance_helper->get_chart_finance_data(FinanceExpense::query(), $year_start, $year_end, $finance_year_start_month, $income_filter, true, false, $category_filter ? "expense_type_id" : null, $category_filter);
        $pie_category_chart_data = $finance_helper->get_chart_by_expense_type($year_start, $year_end, $category_filter);

        $expense_records = FinanceExpense::query()->where("freelancer_id", Auth::user()->id)->whereBetween("job_date", [$year_start, $year_end]);
        if ($category_filter && $category_filter != "") {
            $expense_records = $expense_records->where("expense_type_id", $category_filter);
        }
        $expense_records = $expense_records->get();

        return view('freelancer.finance.expenses-type-filter', compact('finance_year_start_month', 'filter_year', 'income_filter', 'expense_records', 'categories', 'category_filter', 'expense_chart_data', 'pie_category_chart_data'));
    }

    public function netIncome(Request $request)
    {
        $finance_helper = new FinanceHelper(Auth::user());

        $finance_year_start_month = $finance_helper->get_user_financial_year_start_month();
        $user_finance_type = $finance_helper->get_user_finance_type();

        $filter_year = $request->has("year") ? $request->input("year") : date('Y');
        $filter = $request->has("filter") ? $request->input("filter") : 'month';

        $year_start = get_financial_year_range($finance_year_start_month, $filter_year)["year_start"];
        $year_end = get_financial_year_range($finance_year_start_month, $filter_year)["year_end"];

        $income_chart_data = $finance_helper->get_chart_finance_data(FinanceIncome::query(), $year_start, $year_end, $finance_year_start_month, $filter, false, true);
        $expense_chart_data = $finance_helper->get_chart_finance_data(FinanceExpense::query(), $year_start, $year_end, $finance_year_start_month, $filter, false, true);

        $net_chart_data = array();
        foreach ($income_chart_data as $key => $value) {
            $net_chart_data[$key] = $value - $expense_chart_data[$key];
        }

        $total_income = $finance_helper->get_user_total_income($filter_year, $finance_year_start_month);
        $total_expense = $finance_helper->get_user_total_expense($filter_year, $finance_year_start_month);
        $user_total_tax = $finance_helper->user_tax_calculation($finance_year_start_month, $total_income - $total_expense, $user_finance_type, $filter_year);

        return view('freelancer.finance.net-income', compact('finance_year_start_month', 'total_income', 'total_expense', 'user_total_tax', 'filter_year', 'filter', 'net_chart_data'));
    }

    public function deleteIncome(Request $request, $id)
    {
        $income = FinanceIncome::findOrFail($id);
        $income->delete();
        return back()->with("success", "Income deleted successfully");
    }
    public function deleteExpense(Request $request, $id)
    {
        $expense = FinanceExpense::findOrFail($id);
        $expense->delete();
        return back()->with("success", "Expense deleted successfully");
    }

    public function updateEmploymentStatus(Request $request)
    {
        $request->validate([
            "user_finance_type" => "required|in:soletrader,limitedcompany"
        ]);
        $type = $request->input("user_finance_type");

        $financial_year = $request->user()->financial_year;
        if ($financial_year) {
            $financial_year->user_type = $type;
        } else {
            if ($request->user_finance_type == 'limitedcompany') {
                $financial_year = new FinancialYear();
                $financial_year->user_id = $request->user()->id;
                $financial_year->user_type = $type;
                $financial_year->month_start = $request->start_month + 1;
                $financial_year->month_end = $request->start_month;
            } else {
                $financial_year = new FinancialYear();
                $financial_year->user_id = $request->user()->id;
                $financial_year->user_type = $type;
                $financial_year->month_start = 4;
                $financial_year->month_end = 3;
            }
        }
        $financial_year->save();
        return back()->with("success", "Employment status updated successfully");
    }
}
