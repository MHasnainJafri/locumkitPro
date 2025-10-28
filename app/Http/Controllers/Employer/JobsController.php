<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\BlockUser;
use App\Models\EmployerStoreList;
use App\Models\JobCancelation;
use App\Models\JobPost;
use App\Models\JobPostTimeline;
use App\Models\PrivateUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use App\Notifications\NotifyAdminNotification;

class JobsController extends Controller
{
    public function jobListing(Request $request)
    {
        $employer_user_id = $request->user()->id;
        $filterJobStatusId = null;
        if ($request->has("filter") && $request->input("filter") != '') {
            $filterJobStatusId = match ($request->input("filter")) {
                'waiting' => JobPost::JOB_STATUS_OPEN_WAITING,
                'close' => JobPost::JOB_STATUS_CLOSE_EXPIRED,
                'disable' => JobPost::JOB_STATUS_DISABLED,
                'accepted' => JobPost::JOB_STATUS_ACCEPTED,
                'completed' => JobPost::JOB_STATUS_DONE_COMPLETED,
                'freeze' => JobPost::JOB_STATUS_FREEZED,
                'cancel' => JobPost::JOB_STATUS_CANCELED,
            };
        }
        $jobs = JobPost::query()->with("job_cancellation", "private_user_job_actions", "job_actions")->where("employer_id", Auth::user()->id)->where("job_status", "!=", JobPost::JOB_STATUS_DELETED);
        if ($filterJobStatusId) {
            $jobs = $jobs->where("job_status", $filterJobStatusId);
        }

        $job_filter_order = strtolower($request->input("order") ?? "desc") == "asc" ? "asc" : "desc";
        $job_sort_by_filter = in_array($request->input("sort_by"), ["job_title", "job_date", "job_rate", "created_at"], true) ? $request->input("sort_by") : "job_date";

        $jobs = $jobs->orderBy($job_sort_by_filter, $job_filter_order)->paginate(20);

        foreach ($jobs as $job) {
            if ($job->job_date >= today()) {
                $cancel_action_link = "<a href='/employer/cancel-job/{$job->id}' title='Cancel Job' style='color: #ff0000;'><i class='fa fa-times' aria-hidden='true'></i></a>";
            } else {
                $cancel_action_link = "<a href='javascript:void(0);' style='cursor: no-drop;color: lightgray;' title='Cancel Job'><i class='fa fa-times' aria-hidden='true'></i></a>";
            }
            if ($job->job_type == 1) {
                $job_type = "First come first serve";
                $job_view_link = "<a href='/employer/view-job/{$job->id}' title='View Job'><i class='fa fa-eye' aria-hidden='true'></i></a>";
            } else {
                $job_type = "Build list";
                $job_view_link = "<a href='/build-list/{$job->id}' title='View Job'><i class='fa fa-eye' aria-hidden='true'></i></a>";
            }
            $freelancerName = $job->getAcceptedFreelancerData()['name'];
            $del_link = "";
            $jobStatus = "";
            $edit_link = "";
            $duplicate_link = "";
            $cancel_link = "";
            switch ($job->job_status) {
                case '1':
                    $del_link = "<li><a href='javascript:void(0);' title='Delete Job' onclick='delete_post({$job->id})'><i class='fa fa-trash-o' aria-hidden='true' title='Delete Job' style='color:red;'></i></a></li>";
                    $jobStatus = "<span style='color:green;font-size: 14px;'>Waiting</span>";
                    $edit_link = "<a href='/employer/managejob/{$job->id}' title='Edit Job'><i class='fa fa-pencil-square-o' aria-hidden='true'></i></a>";
                    $duplicate_link = "<a href='/employer/managejob/{$job->id}?duplicate_job=1' title='Duplicate Job'><i class='fa fa-files-o' aria-hidden='true'></i></a>";
                    $cancel_link = "<a href='javascript:void(0);' style='cursor: no-drop;color: lightgray;' title='Cancel Job'><i class='fa fa-times' aria-hidden='true'></i></a>";
                    break;
                case '2':
                    $jobStatus = "<span style='color:#000;font-size: 14px;'>Expired</span>";
                    $del_link = "<li><a href='javascript:void(0);' style='cursor: no-drop;color: lightgray;' title='Delete Disable'><i class='fa fa-trash-o' aria-hidden='true'></i></a></li>";
                    $edit_link = "<a href='/employer/managejob/{$job->id}?duplicate_job=1' title='Duplicate Job'><i class='fa fa-files-o' aria-hidden='true'></i></a>";
                    $cancel_link = "<a href='javascript:void(0);' style='cursor: no-drop;color: lightgray;' title='Cancel Job'><i class='fa fa-times' aria-hidden='true'></i></a>";
                    break;
                case '3':
                    $jobStatus = "<span style='color:red;font-size: 14px;'>Disable</span>";
                    $del_link = "<li><a href='javascript:void(0);' title='Delete Job' onclick='delete_post({$job->id})'><i class='fa fa-trash-o' aria-hidden='true' title='Delete Job' style='color:red;'></i></a></li>";
                    $edit_link = "<a href='/employer/managejob/{$job->id}?duplicate_job=1' title='Duplicate Job'><i class='fa fa-files-o' aria-hidden='true'></i></a>";
                    $cancel_link = $cancel_action_link;
                    break;
                case '4':
                    $jobStatus = "<span style='color:green;font-size: 14px;'>Accepted</span>";
                    $del_link = "<li><a href='javascript:void(0);' style='cursor: no-drop;color: lightgray;' title='Delete Disable'><i class='fa fa-trash-o' aria-hidden='true'></i></a></li>";
                    $edit_link = "<a href='/employer/managejob/{$job->id}?duplicate_job=1' title='Duplicate Job'><i class='fa fa-files-o' aria-hidden='true'></i></a>";
                    $cancel_link = $cancel_action_link;
                    break;
                case '5':
                    $jobStatus = "<span style='color:#00A9E0;font-size: 14px;'>Completed</span>";
                    $del_link = "<li><a href='javascript:void(0);' style='cursor: no-drop;color: lightgray;' title='Delete Disable'><i class='fa fa-trash-o' aria-hidden='true'></i></a></li>";
                    $edit_link = "<a href='/employer/managejob/{$job->id}?duplicate_job=1' title='Duplicate Job'><i class='fa fa-files-o' aria-hidden='true'></i></a>";
                    $cancel_link = "<a href='javascript:void(0);' style='cursor: no-drop;color: lightgray;' title='Cancel Job'><i class='fa fa-times' aria-hidden='true'></i></a>";
                    break;
                case '6':
                    $jobStatus = "<span style='color:#00A9E0;font-size: 14px;'>Freeze</span>";
                    $del_link = "<li><a href='javascript:void(0);' style='cursor: no-drop;color: lightgray;' title='Delete Disable'><i class='fa fa-trash-o' aria-hidden='true'></i></a></li>";
                    $edit_link = "<a href='javascript:void(0);' style='cursor: no-drop;color: lightgray;' title='Disable duplicate'><i class='fa fa-files-o' aria-hidden='true'></i></a>";
                    $cancel_link = "<a href='javascript:void(0);' style='cursor: no-drop;color: lightgray;' title='Cancel Job'><i class='fa fa-times' aria-hidden='true'></i></a>";
                    break;
                case '8':
                    if ($job->job_cancellation && $job->job_cancellation->cancel_by_user_type == JobCancelation::CANCEL_BY_EMPLOYER) {
                        $whoCancell = '(By employer) cancelled';
                    } else {
                        $whoCancell = '(By locum) cancelled';
                    }
                    $jobStatus = "<span style='color:#ff0000;font-size: 13px;'> {$whoCancell} </span>";
                    $del_link = "<li><a href='javascript:void(0);' style='cursor: no-drop;color: lightgray;' title='Delete Disable'><i class='fa fa-trash-o' aria-hidden='true'></i></a></li>";
                    $edit_link = "<a href='/employer/managejob/{$job->id}?deplicate_job=1' title='Duplicate Job'><i class='fa fa-files-o' aria-hidden='true'></i></a>";
                    $cancel_link = "<a href='javascript:void(0);' style='cursor: no-drop;color: lightgray;' title='Cancel Job'><i class='fa fa-times' aria-hidden='true'></i></a>";
                    break;
            }

            $job->cancel_action_link = $cancel_action_link;
            $job->job_type = $job_type;
            $job->job_view_link = $job_view_link;
            $job->job_delete_link = $del_link;
            $job->job_edit_link = $edit_link;
            $job->job_status_html = $jobStatus;
            $job->job_duplicate_link = $duplicate_link;
            $job->job_cancel_link = $cancel_link;
            $job->freelancerName = $freelancerName;
        }
        
        return view('employer.job-listing', compact('filterJobStatusId', 'jobs', 'job_filter_order', 'job_sort_by_filter'));
    }

    public function manageJob(Request $request, $job_id = null)
    {
        $job_edit_action = false;
        if ($job_id) {
            $job = JobPost::with("job_post_timelines")->findOrFail($job_id);
            $job_edit_action = true;
        } else {
            $job = null;
        }
        if ($job && $job->is_invitation_sent && $request->query("duplicate_job") != 1) {
            return back()->with("error", "You cannot edit a job for which invitation has already been sent");
        }
        if ($request->query("duplicate_job") == 1) {
            $job_edit_action = false;
        }
        /* $bookedDates = JobPost::where(function ($query) {
            $query->where("job_status", JobPost::JOB_STATUS_ACCEPTED)->orWhere("job_status", JobPost::JOB_STATUS_DONE_COMPLETED);
        })->where("employer_id", Auth::user()->id)->select("job_date")->pluck("job_date")->map(function ($date) {
            return $date->format("Y-m-d");
        })->toArray(); */
        $employer_store_list = EmployerStoreList::where("employer_id", Auth::user()->id)->select("id", "store_name")->get();

        return view('employer.manage-job', compact('employer_store_list', 'job', 'job_edit_action'));
    }

    public function saveManageJob(Request $request)
    {
        $request->validate([
            "job_store" => ["required", "integer",],
            "job_title" => ["required", "string", "max:255"],
 "job_date" => [
        "required",
        "regex:/^(0[1-9]|[12][0-9]|3[01])\/(0[1-9]|1[0-2])\/\d{4}$/"
    ],
            "job_rate" => ["required",  "min:1"],
            "set_timeline" => ["nullable"],
            "job_date_new" => ["required_if:set_timeline,1", "array"],
            "job_rate_new" => ["required_if:set_timeline,1", "array"],
            "job_timeline_hrs" => ["required_if:set_timeline,1", "array"],
        ]);

        $job_store = $request->input("job_store");
        $job_title = $request->input("job_title");
        $job_date = Carbon::createFromFormat(get_default_date_format(),  $request->input("job_date"));
        $job_rate = $request->input("job_rate");
        $set_timeline = $request->input("set_timeline");
        $job_date_new = $request->input("job_date_new");
        $job_rate_new = $request->input("job_rate_new");
        $job_timeline_hrs = $request->input("job_timeline_hrs");
        $job_post_desc = $request->input("job_post_desc");
        //create new job post
        //get job_start_time from employer selected store
        $store = EmployerStoreList::where("id", $job_store)->where("employer_id", Auth::user()->id)->select("id", "store_start_time", "store_address", "store_region", "store_zip")->first();
        if ($store == null) {
            return back()->with("error", "Select a store or add new store from Manage Store section");
        }
        $store_start_time = json_decode($store->store_start_time, true);
        if (isset($store_start_time[$job_date->format("l")])) {
            $job_start_time = $store_start_time[$job_date->format("l")];
        } else {
            $job_start_time = "09:00";
        }

        //create new job post
        $job_post = JobPost::create([
            "employer_id" => Auth::user()->id,
            "user_acl_profession_id" => Auth::user()->user_acl_profession_id,
            "job_title" => $job_title,
            "job_date" => $job_date->format("Y-m-d"),
            "job_start_time" => $job_start_time,
            "job_post_desc" => $job_post_desc,
            "job_rate" => $job_rate,
            "job_type" => 1,
            "job_address" => $store->store_address,
            "job_region" => $store->store_region,
            "job_zip" => $store->store_zip,
            "employer_store_list_id" => $store->id,
            "job_status" => 1,
        ]);
        
       $admin = User::first();

        if ($admin) {
            $admin->notify(new NotifyAdminNotification($job_post));
        }

        if ($set_timeline && $set_timeline == 1) {
            $job_timelines = array();
            foreach ($job_date_new as $key => $timeline_date) {
                $date = Carbon::createFromFormat(get_default_date_format(),  $timeline_date);
                array_push($job_timelines, [
                    "job_post_id" => $job_post->id,
                    "job_date_new" => $date->format("Y-m-d"),
                    "job_timeline_hrs" => $job_timeline_hrs[$key],
                    "job_rate_new" => $job_rate_new[$key],
                    "job_timeline_status" => 3,
                    "created_at" => now(),
                    "updated_at" => now()
                ]);
            }
            JobPostTimeline::insert($job_timelines);
        }

        return redirect("/employer/job-search/{$job_post->id}")->with("success", "Please select the locum(s) you wish to invite to your booking");
    }

    public function updateManageJob(Request $request, $job_id)
    {
        $job = JobPost::where("employer_id", Auth::user()->id)->where("id", $job_id)->first();
        if ($job == null) {
            return abort(404);
        }
        if ($job && $job->is_invitation_sent) {
            return redirect(route('employer.job-listing', ['sort_by' => 'job_date', 'order' => 'DESC']))->with("error", "You cannot edit a job for which invitation has already been sent");
        }
        $request->validate([
            "job_store" => ["required", "integer"],
            "job_title" => ["required", "string", "max:255"],
            "job_date" => ["required", "string", "size:10"],
            "job_rate" => ["required", "numeric"],
            "set_timeline" => ["nullable"],
            "job_date_new" => ["required_if:set_timeline,1", "array"],
            "job_rate_new" => ["required_if:set_timeline,1", "array"],
            "job_timeline_hrs" => ["required_if:set_timeline,1", "array"],
        ]);
        $job_store = $request->input("job_store");
        $job_title = $request->input("job_title");
        $job_date = Carbon::createFromFormat(get_default_date_format(),  $request->input("job_date"));
        $job_rate = $request->input("job_rate");
        $set_timeline = $request->input("set_timeline");
        $job_date_new = $request->input("job_date_new");
        $job_rate_new = $request->input("job_rate_new");
        $job_timeline_hrs = $request->input("job_timeline_hrs");
        $job_post_desc = $request->input("job_post_desc");
        //create new job post
        //get job_start_time from employer selected store
        $store = EmployerStoreList::where("id", $job_store)->where("employer_id", Auth::user()->id)->select("id", "store_start_time", "store_address", "store_region", "store_zip")->first();
        if ($store == null) {
            return back()->with("error", "Select a store or add new store from Manage Store section");
        }
        $store_start_time = json_decode($store->store_start_time, true);
        if (isset($store_start_time[$job_date->format("l")])) {
            $job_start_time = $store_start_time[$job_date->format("l")];
        } else {
            $job_start_time = "09:00";
        }

        //update the job post
        JobPost::where("id", $job->id)->update([
            "job_title" => $job_title,
            "job_date" => $job_date->format("Y-m-d"),
            "job_start_time" => $job_start_time,
            "job_post_desc" => $job_post_desc,
            "job_rate" => $job_rate,
            "job_type" => 1,
            "job_address" => $store->store_address,
            "job_region" => $store->store_region,
            "job_zip" => $store->store_zip,
            "employer_store_list_id" => $store->id,
        ]);

        //delete all previous timelines and create new ones with new data
        $job->job_post_timelines()->delete();

        $job_timelines = array();
        if ($set_timeline && $set_timeline == 1) {
            foreach ($job_date_new as $key => $timeline_date) {
                $date = Carbon::createFromFormat(get_default_date_format(),  $timeline_date);
                array_push($job_timelines, [
                    "job_post_id" => $job->id,
                    "job_date_new" => $date->format("Y-m-d"),
                    "job_timeline_hrs" => $job_timeline_hrs[$key],
                    "job_rate_new" => $job_rate_new[$key],
                    "job_timeline_status" => 3,
                    "created_at" => now(),
                    "updated_at" => now()
                ]);
            }
        }
        JobPostTimeline::insert($job_timelines);

        return redirect("/employer/job-search/{$job->id}")->with("success", "Please select the locum(s) you wish to invite to your booking");
    }

    public function jobSearch(Request $request, $id)
    {
        // dd($request->all());
        $job = JobPost::where("employer_id", Auth::user()->id)->where("id", $id)->first();
        if ($job == null) {
            return abort(404);
        }
        $employer_answers = Auth::user()->user_answers;
        $employer_answer_questions = [];
        if ($employer_answers) {
            foreach ($employer_answers as $user_answer) {
                $employer_answer_questions[$user_answer->user_question_id] = $user_answer;
            }
        }
        $freelancers = User::query()->with(["user_answers", "user_work_calender", "user_extra_info", "user_acl_package", "user_package_detail"])->where("user_acl_role_id", 2);

        $private_freelancers = PrivateUser::with("private_user_job_actions")->where("employer_id", Auth::user()->id)->where("status", "!=", '2')->get();
        $private_freelancers = $private_freelancers->filter(function ($freelancer) {
            $count = $freelancer->private_user_job_actions->count();
            if ($count > 10) {
                return false;
            }
            return true;
        });

        //sort by check
        $sort_id = 'ASC';
        $sort_id_icon = '';
        $sort_feed = 'ASC';
        $sort_feed_icon = '';
        $sort_canrate = 'ASC';
        $sort_canrate_icon = '';
        if ($request->has('sortByID')) {
            if ($request->input('sortByID') == 'ASC') {
                $sort_id = 'DESC';
                $sort_id_icon = 'fa-sort-desc';
                $freelancers = $freelancers->orderBy("id");
            } else {
                $freelancers = $freelancers->orderBy("id", "DESC");
                $sort_id_icon = 'fa-sort-up';
            }
        }
        $freelancers = $freelancers->get();

        //filter freelancers according to job
        $freelancers = $freelancers->filter(function ($freelancer) use ($job, $employer_answer_questions) {
            $freelancer_answer_questions = [];
            foreach ($freelancer->user_answers as $user_answer) {
                $freelancer_answer_questions[$user_answer->user_question_id] = $user_answer;
            }
            $answer_match_count = 0;
            $total_answerable_question_count = 0;
            foreach ($employer_answer_questions as $question_id => $answer_questions) {
                if ($answer_questions->type_value && $answer_questions->type_value != "") {
                    $question = $answer_questions->question;
                    if ($question->employer_question && $question->employer_question != "" && $question->freelancer_question && $question->freelancer_question != "") {
                        $match = false;
                        if (key_exists($question_id, $freelancer_answer_questions)) {
                            if ($question->type == 2) {
                                $match = $freelancer_answer_questions[$question_id]->type_value === $answer_questions->type_value;
                            } elseif ($question->type == 3 && json_decode($answer_questions->type_value) && json_decode($freelancer_answer_questions[$question_id]->type_value)) {
                                $match = count(array_intersect(json_decode($answer_questions->type_value), json_decode($freelancer_answer_questions[$question_id]->type_value))) >= 1;
                            } elseif ($question->type == 5 && key_exists($question->range_type_condition, array("1" => ">", "2" => ">=", "3" => "<", "4" => "<=", "5" => "="))) {
                                $range_type_condition = array("1" => ">", "2" => ">=", "3" => "<", "4" => "<=", "5" => "=")[$question->range_type_condition];
                                // dd($freelancer_answer_questions , $freelancer_answer_questions[$question_id]->type_value);
                                // dd($answer_questions->type_value, $freelancer_answer_questions[$question_id]->type_value ?? '0', $range_type_condition);
                                if($freelancer_answer_questions[$question_id]->type_value == null)
                                {
                                    // dd('stop here', $freelancer_answer_questions[$question_id]);
                                    return $freelancer_answer_questions[$question_id]->type_value;
                                }
                                $match = is_range_condition_succeed($answer_questions->type_value, $freelancer_answer_questions[$question_id]->type_value, $range_type_condition);
                            } else if ($question->type == 6) {
                                if (strtolower($answer_questions->type_value) == "yes") {
                                    $match = $freelancer_answer_questions[$question_id]->type_value === $answer_questions->type_value;
                                } else {
                                    $match = true;
                                }
                            }
                        }
                        if ($match) {
                            $answer_match_count++;
                        }
                        $total_answerable_question_count++;
                    }
                }
            }
            if ($total_answerable_question_count > 0) {
                $average_answers = $answer_match_count / $total_answerable_question_count * 100;
                if ($average_answers < 100) {
                    Log::info("Answer test failed for freelancer {$freelancer->id} \n <br />");
                    return false;
                }
            }

            $is_block_by_employer = BlockUser::where("freelancer_id", $freelancer->id)->where("employer_id", Auth::user()->id)->count() > 0 ? true : false;
            if ($is_block_by_employer) {
                return false;
            }
            $distance = calculate_distance_for_job_search_freelancers($freelancer, $job);
            $town_status = compare_job_town_with_user_selected_towns($freelancer, $job);
            if (strtolower($freelancer->user_extra_info?->max_distance) != "over 50") {
                Log::info("Distance for {$freelancer->id} is: {$distance} \n <br />");
                Log::info("Town Status for {$freelancer->id} is: {$town_status} \n <br />");
                if (is_null($distance) || ($distance > intval($freelancer->user_extra_info?->max_distance))) {
                    if (!$town_status) {
                        Log::info("Distance test failed for freelancer {$freelancer->id} \n <br />");
                        return false;
                    }
                }
            }

            $freelancer_rate = $freelancer->get_freelancer_rate_on_date($job->job_date);
            $job_rate = $job->job_rate;
            if ($freelancer_rate > $job_rate) {
                Log::info("Rate test failed for freelancer {$freelancer->id} \n <br />");
                return false;
            }
            if ($freelancer->can_freelancer_get_job_invitation() == false) {
                return false;
            }
            $user_package_detail = $freelancer->user_package_detail;
            if (is_null($user_package_detail) || ($user_package_detail && Carbon::parse($user_package_detail->package_expire_date)->lessThan(today()))) {
                return false;
            }

            $is_available_on_date = $freelancer->is_available_on_date($job->job_date);
            return $is_available_on_date;
        });
        Log::info("All freelancers are \n <br />");

        foreach ($freelancers as $freelancer) {
            $freelancer->job_cancellation_rate = get_job_cancellation_rate_by_user($freelancer->id);
            $freelancer->overall_feedback_rating = get_overall_feedback_rating_by_user($freelancer->id);
        }

        if ($request->has('sortByFeedAvg')) {
            if ($request->input('sortByFeedAvg') == 'ASC') {
                $sort_feed = 'DESC';
                $sort_feed_icon = 'fa-sort-desc';
                $freelancers = $freelancers->sortBy("overall_feedback_rating");
            } else {
                $sort_feed_icon = 'fa-sort-up';
                $freelancers = $freelancers->sortByDesc("overall_feedback_rating");
            }
        }
        if ($request->has('sortByCancelRate')) {
            if ($request->input('sortByCancelRate') == 'ASC') {
                $sort_canrate = 'DESC';
                $sort_canrate_icon = 'fa-sort-desc';
                $freelancers = $freelancers->sortBy("job_cancellation_rate");
            } else {
                $sort_canrate_icon = 'fa-sort-up';
                $freelancers = $freelancers->sortByDesc("job_cancellation_rate");
            }
        }
        $freelancers = User::where('user_acl_role_id', 2)->get();
        foreach ($freelancers as $freelancer) {
            $freelancer->job_cancellation_rate = get_job_cancellation_rate_by_user($freelancer->id);
            $freelancer->overall_feedback_rating = get_overall_feedback_rating_by_user($freelancer->id);
        }
        // dd('here in request', $freelancers->job_cancellation_rate = get_job_cancellation_rate_by_user($freelancers->id));
        return view("employer.job-search", compact("freelancers", "job", "private_freelancers", "sort_id", "sort_id_icon", "sort_feed", "sort_feed_icon", "sort_canrate", "sort_canrate_icon"));
    }

    public function deleteJobListing($job_id)
    {
        $job = JobPost::where("employer_id", Auth::user()->id)->where("id", $job_id)->first();
        if ($job == null) {
            return new JsonResponse(["error" => true, "message" => "Job not found"], 500);
        }
        $job->job_status = '7';
        $job->save();
        return new JsonResponse();
    }

    public function viewJob($job_id)
    {
        $job = JobPost::where("employer_id", Auth::user()->id)->where("id", $job_id)->first();
        if ($job == null) {
            return abort(404);
        }
        $job->get_store_start_time();
        $store_contact_details = $job->employer->user_extra_info->mobile;
        if ($store_contact_details == null || empty($store_contact_details)) {
            $store_contact_details = $job->employer->user_extra_info->telephone;
        }

        $statusUrl = 0;
        $status = 0;
        $setTitle = "";
        $setTitle2 = "";
        $type_jst = "blue";
        $cssstyle = "";
        if ($job->job_status == 3) {
            $cssstyle = 'style="color: #7aae00"';
            $statusUrl = '/single-job?view=' . $job->job_id . '&action=disable';
            $status = 'Enable';
            $actionupdatemsg = "<div class='alert alert-danger'>Job status is Disable.</div>";
        } elseif ($job->job_status == 1) {
            $cssstyle = 'style="color: #ff0000"';
            $statusUrl = '/single-job?view=' . $job->job_id . '&action=enable';
            $status = 'Disable';
        }
        if ($job->job_status == 4) {
            $type_jst = "green";
        }
        if ($job->job_status == 2 || $job->job_status == 4) {
            $setTitle1 = "  Booking confirmation (Key Details)";
            $setTitle2 = "  Booking confirmation (additional information)";
            $setTitle3 = "  Booking Confirmation – Details of Locum booked for you";
        } else {
            $setTitle1 = "  Job Invitation (Key Details)";
            $setTitle2 = "  Job Invitation (additional information)";
            $setTitle3 = "  Job invitation – Details of Locum booked for you";
        }
        $job_status_html = match ($job->job_status) {
            1 => "<span style='color:green'>Enable</span>",
            2 => "Close",
            3 => "<span style='color:red'>Disable</span>",
            4 => "Accepted",
            5 => "Done",
            6 => "Freeze",
            default => ""
        };


        return view("shared.view-single-job", compact("job", "type_jst", "setTitle2", "job_status_html", "setTitle1", "store_contact_details"));
    }
}
