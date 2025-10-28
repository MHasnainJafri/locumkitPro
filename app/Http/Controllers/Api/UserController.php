<?php

namespace App\Http\Controllers\Api;

use App\Helpers\AppNotificationHelper;
use App\Helpers\DistanceCalculateHelper;
use App\Helpers\FinanceHelper;
use App\Helpers\JobMailHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmployerJobPostResource;
use App\Http\Resources\FreelancerPrivateJobResource;
use App\Http\Resources\JobPostResource;
use App\Models\BlockUser;
use App\Models\FinanceExpense;
use App\Models\FinanceIncome;
use App\Models\JobAction;
use App\Models\JobPost;
use App\Models\SiteTown;
use App\Models\User;
use App\Models\UserAclProfession;
use App\Models\UserAclRole;
use App\Models\UserAnswer;
use App\Models\UserExtraInfo;
use App\Models\UsersWorkCalender;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    public function blockDate(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user_id = $user->id;
            $is_user_pkg_allow_finance = Gate::forUser($user)->check("manage_finance") ? 1 : 0;
            $block_dates = array();

            if ($user->user_acl_role_id == 2) {
                $block_dates['check_previliage'] = $is_user_pkg_allow_finance;

                $live_jobs = JobPost::with("job_store")->whereIn("job_status", [JobPost::JOB_STATUS_ACCEPTED, JobPost::JOB_STATUS_DONE_COMPLETED])
                    ->whereHas("job_actions", function ($query) use ($user_id) {
                        $query->where("freelancer_id", $user_id)->whereIn("action", [JobAction::ACTION_ACCEPT, JobAction::ACTION_DONE]);
                    })->get();
                $private_jobs = $user->private_jobs()->orderBy("job_date")->get();

                $block_dates['booked'] = array_merge(JobPostResource::collection($live_jobs)->jsonSerialize(), FreelancerPrivateJobResource::collection($private_jobs)->jsonSerialize());
                if ($block_dates['booked'] && is_array($block_dates['booked']) && sizeof($block_dates['booked']) <= 0) {
                    $block_dates['booked'] = [];
                }
                if (!$block_dates['booked']) {
                    $block_dates['booked'] = [];
                }
                $user_block_dates = $user->get_user_block_dates();
                if ($user_block_dates) {
                    $user_block_dates = array_filter($user_block_dates, function ($date) {
                        return Carbon::parse($date)->format("m/d/Y 5:30:00");
                    });
                }
                $block_dates['block'] = $user_block_dates;
                if (!$block_dates['block']) {
                    $block_dates['block'] = [];
                }
            } else {
                $employer_jobs = JobPost::whereIn("job_status", [JobPost::JOB_STATUS_ACCEPTED, JobPost::JOB_STATUS_DONE_COMPLETED])
                    ->where("employer_id", $user->id)->orderBy("job_date")->get();
                $block_dates = EmployerJobPostResource::collection($employer_jobs)->jsonSerialize();
            }
            return response()->success($block_dates);
        }
        return response()->error('User not found');
    }

    public function checkUserAvailability(Request $request)
    {
        try {
            $request->validate([
                "job_info.date" => "required|date",
            ]);
        } catch (ValidationException) {
            return response()->error("Please enter valid date.");
        }

        $request_job_date = $request->input("job_info.date");
        $request_job_date = date('Y-m-d', strtotime($request_job_date));
        $user = $request->user();
        if ($user) {
            $user_block_dates = $user->get_user_block_dates();
            if ($user_block_dates && in_array($request_job_date, $user_block_dates)) {
                return response()->success([
                    'response' => 2,
                    'availability' => 'blocked'
                ]);
            }
            $is_available = $user->is_available_on_date($request_job_date);
            return response()->success([
                'response' => $is_available,
                'availability' => $is_available ? 'available' : 'booked'
            ]);
        }
        return response()->error('User not found', 404);
    }

    public function financeSummary(Request $request)
    {
        $user = $request->user();
        if ($user && $user->user_acl_role_id == 2) {
            $finance_helper = new FinanceHelper($user);

            $financial_year_start_month = $finance_helper->get_user_financial_year_start_month();
            $user_finance_type = $finance_helper->get_user_finance_type();

            $user_total_income = $finance_helper->get_user_total_income(date('Y'), $financial_year_start_month);
            $user_total_expense = $finance_helper->get_user_total_expense(date('Y'), $financial_year_start_month);
            $profit = $user_total_income - $user_total_expense;

            $user_total_tax = $finance_helper->user_tax_calculation($financial_year_start_month, $profit, $user_finance_type, Date('Y'));

            return response()->success([
                'total_earn' => set_amount_format($user_total_income),
                'total_spent' => set_amount_format($user_total_expense),
                'total_net' => set_amount_format($profit),
                'total_tax' => set_amount_format($user_total_tax),
                'financial_year' => get_financial_year_range_string($financial_year_start_month)
            ]);
        }
        return response()->error('Not found');
    }

    public function financeSummaryChart(Request $request)
    {
        $filter_year = date('Y');
        $filter = 'month';
        $user = $request->user();
        if ($user) {
            $finance_chart = [];

            if ($user->user_acl_role_id == 2) {
                $finance_helper = new FinanceHelper($user);

                $financial_year_start_month = $finance_helper->get_user_financial_year_start_month();
                $user_finance_type = $finance_helper->get_user_finance_type();
                $user_total_income = $finance_helper->get_user_total_income(date('Y'), $financial_year_start_month);
                $user_total_expense = $finance_helper->get_user_total_expense(date('Y'), $financial_year_start_month);
                $profit = $user_total_income - $user_total_expense;
                $user_total_tax = $finance_helper->user_tax_calculation($financial_year_start_month, $profit, $user_finance_type, Date('Y'));

                $freelancer_finance_summery = [
                    'total_earn' => set_amount_format($user_total_income),
                    'total_spent' => set_amount_format($user_total_expense),
                    'total_net' => set_amount_format($profit),
                    'total_tax' => set_amount_format($user_total_tax),
                    'financial_year' => get_financial_year_range_string($financial_year_start_month)
                ];
                $finance_chart['freelancer_finance_summery'] = $freelancer_finance_summery;

                $year_start = get_financial_year_range($financial_year_start_month, $filter_year)["year_start"];
                $year_end = get_financial_year_range($financial_year_start_month, $filter_year)["year_end"];

                $income_chart_data = $finance_helper->get_chart_finance_data(FinanceIncome::query(), $year_start, $year_end, $financial_year_start_month, $filter, true);
                $expense_chart_data = $finance_helper->get_chart_finance_data(FinanceExpense::query(), $year_start, $year_end, $financial_year_start_month, $filter, true);

                $finance_chart["income"] = array();
                $finance_chart["income"]["labels"] = $income_chart_data["labels"];
                $finance_chart["income"]["x"] = $income_chart_data["labels"];
                $finance_chart["income"]["data_paid"] = $income_chart_data["data_paid"];
                $finance_chart["income"]["data_unpaid"] = $income_chart_data["data_unpaid"];
                $income_chart_data_total = array();
                $income_chart_data_color = array();
                $income_chart_data_border = array();
                foreach ($income_chart_data["data_paid"] as $index => $value) {
                    $income_chart_data_total[] = $value + $income_chart_data["data_unpaid"][$index];
                    $income_chart_data_color[] = "rgba(133, 160, 76, 0.5)";
                    $income_chart_data_border[] = "rgba(133, 160, 76, 1)";
                }
                $finance_chart["income"]["total"] = $income_chart_data_total;
                $finance_chart["income"]["y"] = $income_chart_data_total;
                $finance_chart["income"]["color"] = $income_chart_data_color;
                $finance_chart["income"]["border_color"] = $income_chart_data_border;

                $finance_chart["expense"] = array();
                $finance_chart["expense"]["labels"] = $expense_chart_data["labels"];
                $finance_chart["expense"]["x"] = $expense_chart_data["labels"];
                $finance_chart["expense"]["data_paid"] = $expense_chart_data["data_paid"];
                $finance_chart["expense"]["data_unpaid"] = $expense_chart_data["data_unpaid"];

                $expense_chart_data_total = array();
                $expense_chart_data_color = array();
                $expense_chart_data_border = array();
                foreach ($expense_chart_data["data_paid"] as $index => $value) {
                    $expense_chart_data_total[$index] = $value + $expense_chart_data["data_unpaid"][$index];
                    $expense_chart_data_color[] = "rgba(164, 68, 66, 0.5)";
                    $expense_chart_data_border[] = "rgba(164, 68, 66, 1)";
                }
                $finance_chart["expense"]["total"] = $expense_chart_data_total;
                $finance_chart["expense"]["y"] = $expense_chart_data_total;
                $finance_chart["expense"]["color"] = $expense_chart_data_color;
                $finance_chart["expense"]["border_color"] = $expense_chart_data_border;
            } else {
                $finance_helper = new FinanceHelper($user);
                $financial_year_start_month = $finance_helper->get_user_financial_year_start_month();

                $year_start = get_financial_year_range($financial_year_start_month, $filter_year)["year_start"];
                $year_end = get_financial_year_range($financial_year_start_month, $filter_year)["year_end"];

                $cost_chart_data = $finance_helper->get_employer_finance_cost_chart_data($year_start, $year_end);
                $job_chart_data = $finance_helper->get_employer_finance_jobs_chart_data($year_start, $year_end);

                $finance_chart["cost"] = array();
                $finance_chart["cost"]["labels"] = array_keys($cost_chart_data);
                $finance_chart["cost"]["x"] = array_keys($cost_chart_data);
                $finance_chart["cost"]["values"] = array_values($cost_chart_data);
                $finance_chart["cost"]["y"] = array_values($cost_chart_data);
                $finance_chart["cost"]["color"] = array_fill(0, sizeof($cost_chart_data), "rgba(164, 68, 66, 0.5)");
                $finance_chart["cost"]["border_color"] = array_fill(0, sizeof($cost_chart_data), "rgba(164, 68, 66, 1)");

                $finance_chart["job"] = array();
                $finance_chart["job"]["labels"] = array_keys($job_chart_data);
                $finance_chart["job"]["x"] = array_keys($job_chart_data);
                $finance_chart["job"]["values"] = array_values($job_chart_data);
                $finance_chart["job"]["y"] = array_values($job_chart_data);
                $finance_chart["job"]["colors"] = array_fill(0, sizeof($job_chart_data), "rgba(133, 160, 76, 0.5)");
                $finance_chart["job"]["border_color"] = array_fill(0, sizeof($job_chart_data), "rgba(133, 160, 76, 1)");
            }

            return response()->success($finance_chart);
        }
        return response()->error('User not found');
    }

    public function userCancellationRate(Request $request)
    {
        $user = $request->user();
        $user_id = $user->id;
        $cancellation_rate = 0;
        if ($user->user_acl_role_id == User::USER_ROLE_EMPLOYER) {
            $cancellation_rate = get_job_cancellation_rate_by_user($user_id, "employer");
        } elseif ($user->user_acl_role_id == User::USER_ROLE_LOCUM) {
            $cancellation_rate = get_job_cancellation_rate_by_user($user_id);
        }
        return response()->success([
            'cancellation_rate' => $cancellation_rate
        ]);
    }

    public function userPermission(Request $request)
    {
        try {
            $request->validate([
                "permission" => "required|array",
            ]);
        } catch (ValidationException) {
            return response()->error("Please enter valid permission array.");
        }

        $user = $request->user();
        $permissions = $request->input('permission', []);
        $is_permission = array();
        foreach ($permissions as $permission) {
            $is_permission[$permission] = can_user_package_has_privilege($user, $permission);
        }
        return response()->success($is_permission);
    }
    public function updatePassword(Request $request)
    {
        try {
            $request->validate([
                "user_data.password" => "required|string|min:6|max:20",
                "user_data.oldpassword" => "required|string|min:6|max:20",
            ]);
        } catch (ValidationException $ve) {
            return response()->error($ve->getMessage(), 200, $ve->errors());
        }
        $user = $request->user();
        $old_password = $request->input('user_data.oldpassword');

        if (Hash::check($old_password, $user->password)) {
            $new_password = Hash::make($request->input('user_data.password'));
            $user->password = $new_password;
            $user->save();
            return response()->success([], 'User password updated successfully');
        }
        return response()->error('Old password is wrong');
    }

    public function manageCalendar(Request $request)
    {
        try {
            $request->validate([
                "is_available" => "required|integer|in:1,2",
                "date" => "required|date",
                "rate" => "required_if:is_available,1|numeric|gt:0"
            ]);
        } catch (ValidationException $ve) {
            return response()->error($ve->getMessage(), 200, $ve->errors());
        }
        $user = $request->user();
        $availability = $request->integer("is_available"); //1 available, 2 not available

        $selected_date = Carbon::parse($request->input("date"));
        $user_works = UsersWorkCalender::where("user_id", $user->id)->first();
        $block_dates = [];
        $available_dates = [];
        if ($user_works) {
            $block_dates = $user_works->block_dates ? (json_decode($user_works->block_dates, true) ?? []) : [];
            $available_dates  = $user_works->available_dates ? (json_decode($user_works->available_dates, true) ?? []) : [];
        }

        if ($availability == 1) {
            $min_rate_date = $request->float("rate");
            //remove date from block date if present
            $block_dates = array_filter($block_dates, function ($date) use ($selected_date) {
                return Carbon::parse($date)->notEqualTo($selected_date);
            });
            //update or add into available_dates
            $available_dates = array_filter($available_dates, function ($value) use ($selected_date) {
                return Carbon::parse($value['date'])->notEqualTo($selected_date);
            });
            $available_dates[] = ["date" => $selected_date->format("Y-m-d"), "min_rate" => $min_rate_date];
        } else if ($availability == 2) {
            //update, add date into block_dates
            $block_dates = array_filter($block_dates, function ($date) use ($selected_date) {
                return Carbon::parse($date)->notEqualTo($selected_date);
            });
            array_push($block_dates, $selected_date->format("Y-m-d"));
            //remove date from available_dates
            $available_dates = array_filter($available_dates, function ($value) use ($selected_date) {
                return Carbon::parse($value['date'])->notEqualTo($selected_date);
            });
        }
        UsersWorkCalender::updateOrCreate(["user_id" => $user->id], [
            "block_dates" => json_encode($block_dates),
            "available_dates" => json_encode($available_dates)
        ]);

        return response()->success([], 'Calender updated successfully');
    }

    public function getMinRateDate(Request $request)
    {
        try {
            $request->validate([
                "date" => "required|date",
            ]);
        } catch (ValidationException $ve) {
            return response()->error($ve->getMessage(), 200, $ve->errors());
        }
        $user = $request->user();
        $date = $request->date("date");
        $min_rate = null;
        $calendar_record = UsersWorkCalender::where('user_id', $user->id)->first();
        if ($calendar_record && $calendar_record->available_dates) {
            $recordArray = json_decode($calendar_record->available_dates, true);
            if ($recordArray && sizeof($recordArray) > 0) {
                foreach ($recordArray as $key => $value) {
                    if ($date->equalTo(Carbon::parse($value["date"]))) {
                        $min_rate = ['min_rate' => set_amount_format($value['min_rate'])];
                    }
                }
            }
        }

        if (is_null($min_rate) && optional($user->user_extra_info)->minimum_rate) {
            $minimum_rate = json_decode($user->user_extra_info->minimum_rate, true);
            if ($minimum_rate) {
                $day = $date->format("l");
                $rate = $minimum_rate[$day];
                $min_rate = ['min_rate' => set_amount_format($rate)];
            }
        }
        if ($min_rate && is_array($min_rate)) {
            return response()->success($min_rate);
        }

        return response()->error("Minimum rate not found");
    }

    public function storeList(Request $request)
    {
        $distanceHelper = new DistanceCalculateHelper();
        $zipCode = str_replace(' ', '', $request->input('zip'));
        $address = str_replace(' ', '+', $request->input('full_addr'));
        $maxDistance = $request->input('max_dis') === 'Over 50' ? 6371 : intval($request->input('max_dis')); // earth's mean radius, km
        $lat = $distanceHelper->getLatitude($zipCode, $address); // latitude of center of bounding circle in degrees
        $lon = $distanceHelper->getLongitude($zipCode, $address); // longitude of center of bounding circle in degrees
        $maxLat = $lat + rad2deg($maxDistance / 6371);
        $minLat = $lat - rad2deg($maxDistance / 6371);
        $maxLon = $lon + rad2deg($maxDistance / 6371 / cos(deg2rad($lat)));
        $minLon = $lon - rad2deg($maxDistance / 6371 / cos(deg2rad($lat)));
        $lat = rad2deg($lat);
        $lon = rad2deg($lon);

        $results = SiteTown::select('id', 'town', 'country', 'region', 'type')
            ->whereBetween('lat', [$minLat, $maxLat])
            ->whereBetween('lon', [$minLon, $maxLon])
            ->orderByRaw('ACOS(SIN(RADIANS(?))*SIN(RADIANS(lat)) + COS(RADIANS(?))*COS(RADIANS(lat))*COS(RADIANS(lon)-?)) * 6371', [$lat, $lat, $lon])
            ->get();

        return response()->success($results->toArray());
    }

    public function manageBlockedUser(Request $request)
    {
        $user_data = $request->all();
        $uid = isset($user_data['uid']) ? $user_data['uid'] : '';
        if ($user_data['type'] == 'delete') {
            $results = $this->updateBlockFreelancer($user_data['bid']);
        } else if ($user_data['type'] == 'delete-account') {
            $results['delete'] = $this->deleteUserAccount($user_data);
        } else if ($user_data['type'] == 'get') {
            $results = array();
            $getvalues = BlockUser::with("freelancer")->where("employer_id", $uid)->get();
            foreach ($getvalues as $key => $value) {
                $results['results'][$key]['bid'] = $value['id'];
                $results['results'][$key]['free_id'] = $value['freelancer_id'];
                $results['results'][$key]['name'] = $value->freelancer['firstname'] . ' ' . $value->freelancer['lastname'];
                $results['results'][$key]['email'] = $value->freelancer['email'];
                $results['results'][$key]['block_date'] = $value['created_at']->format("Y-m-d");
            }
        }
        return response(json_encode($results));
    }

    /* update block Freelancer */
    public function updateBlockFreelancer($id)
    {
        $getRecord2 = BlockUser::where("id", $id)->delete();
        return $getRecord2;
    }

    public function deleteUserAccount($user_data)
    {
        $uid = isset($user_data['uid']) ? $user_data['uid'] : '';
        $user_email = isset($user_data['email']) ? $user_data['email'] : '';
        $user_name = isset($user_data['username']) ? $user_data['username'] : '';
        $reason = isset($user_data['uservalue']) ? $user_data['uservalue'] : '';
        //get user email and user name by user id
        User::where("id", $uid)->update(["active" => '5']);
        $user_data = "sucess";
        return $user_data;
    }

    public function searchTown(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->error('Town name is required to search for', 400, $validator->messages()->toArray());
        }
        $name = str_replace("'", "", $request['name']);
        $results = SiteTown::where("town", "LIKE", "%$name%")->select("town as Town")->limit(10)->get()->toArray();
        return response()->success($results);
    }

    public function fcmTest(Request $request)
    {
        $notificationHelper = new AppNotificationHelper();
        $job_id = $request->input("job_id");
        $message = $request->input("message");
        $title = $request->input("title");
        $user_id = $request->input("user_id");
        $types = $request->input("types");
        $token_id = $request->input("token_id");

        $notificationHelper->notification($job_id, $message, $title, $user_id, $types, $token_id);
        return response()->success();
    }
    
    public function verify(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');
        $emailRecord = DB::table('password_resets')->where('email', $email)->first();
    
        if (!$emailRecord) {
            return redirect('/')->with('error', 'Invalid or expired token.');
        }
    
        $createdAt = $emailRecord->created_at;
        if (now()->diffInMinutes($createdAt) > 15) {
            DB::table('password_resets')->where('email', $emailRecord->email)->delete();
    
            return redirect('/')->with('error', 'Token has expired. Please request a new verification email.');
        }
    
        $user = User::where('email', $emailRecord->email)->first();
        if ($user) {
            $user->email_verified_at = now();
            $user->save();
            
            DB::table('password_resets')->where('email', $emailRecord->email)->delete();
            return redirect()->route('verify_messges');

            return redirect('/')->with('success', 'Your email has been verified successfully.');

            session()->flash('success', 'Your email has been verified successfully.');
            return redirect()->route('index');
        }
    
        return redirect('/')->with('error', 'User not found.');
    }
    
}
