<?php

namespace App\Http\Controllers\Api;

use App\Helpers\AppNotificationHelper;
use App\Models\MobileNotification;
use App\Helpers\JobMailHelper;
use App\Helpers\JobSmsHelper;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\JobManagementController;
use App\Http\Resources\Api\JobPostCurrentMonthResource;
use App\Http\Resources\Api\PrivateJobCurrentMonthResource;
use App\Http\Resources\EmployerJobPostResource;
use App\Http\Resources\JobPostExtendedResource;
use App\Http\Resources\SearchFreelancerResource;
use App\Http\Resources\SearchPrivateFreelancerResource;
use App\Mail\FreelancerJobInvitationMail;
use App\Mail\JobNegotiateMail;
use App\Models\BlockUser;
use App\Models\EmployerStoreList;
use App\Models\ExpenseType;
use App\Models\FinanceEmployer;
use App\Models\FinanceExpense;
use App\Models\FinanceIncome;
use App\Models\FreelancerPrivateFinance;
use App\Models\FreelancerPrivateJob;
use App\Models\JobAction;
use App\Models\JobCancelation;
use App\Models\JobInvitedUser;
use App\Models\JobOnDay;
use App\Models\JobPost;
use App\Models\JobPostTimeline;
use App\Models\PrivateUser;
use App\Models\PrivateUserJobAction;
use App\Models\User;
use App\Models\UserAnswer;
use App\Models\UserQuestion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class JobController extends Controller
{
    private AppNotificationHelper $notifyController;
    private JobSmsHelper $jobsmsController;

    public function __construct()
    {
        $this->notifyController = new AppNotificationHelper();
        $this->jobsmsController = new JobSmsHelper();
    }
    public function currentMonthBooking(Request $request)
    {
        $current_month_booking = array();
        $user = $request->user();
        if ($user) {
            if ($user->user_acl_role_id == 2) {
                $user_private_jobs = FreelancerPrivateJob::whereDate("job_date", ">=", today()->startOfMonth())->whereDate("job_date", "<", today()->endOfMonth())->where("freelancer_id", $user->id)->get();
                $user_live_jobs = JobPost::with(["job_store"])->whereDate("job_date", ">=", today()->startOfMonth())->whereDate("job_date", "<", today()->endOfMonth())->whereHas("job_actions", function ($query) use ($user) {
                    $query->where("freelancer_id", $user->id)->whereIn("action", [JobAction::ACTION_ACCEPT, JobAction::ACTION_DONE]);
                })->orderBy("job_date")->get();
                $current_month_booking['private_jobs'] = PrivateJobCurrentMonthResource::collection($user_private_jobs)->jsonSerialize();
                $current_month_booking['website_jobs'] = JobPostCurrentMonthResource::collection($user_live_jobs)->jsonSerialize();
            } else {
                $employer_jobs = JobPost::with(["job_store"])->whereDate("job_date", ">=", today()->startOfMonth())->whereDate("job_date", "<", today()->endOfMonth())
                    ->where("employer_id", $user->id)->get();
                $current_month_booking = EmployerJobPostResource::collection($employer_jobs)->jsonSerialize();
            }
        }
        return response()->success($current_month_booking);
    }

    public function postJob(Request $request)
    {
        $user = $request->user();
        $validator = Validator::make($request->all(), [
            'user_id' => ['required', 'integer', Rule::exists('users', 'id')->where(function ($query) {
                $query->where('user_acl_role_id', User::USER_ROLE_EMPLOYER);
            })],
            'job_info.store_id' => ['required', 'integer', Rule::exists('employer_store_lists', 'id')->where(function ($query) use ($user) {
                $query->where('employer_id', $user->id);
            })],
            'job_info.job_title' => 'required|string',
            "job_info.job_date" => ["required"],
            "job_info.job_rate" => ["required", "numeric", "min:1"]
        ]);

        if ($validator->fails()) {
            return response()->error("Wrong inputs are given", 400, $validator->messages()->toArray());
        }
        $store_id = $request->input('job_info.store_id');
        $is_timeline = $request->input('job_info.is_timeline', 0);
        $store_info = EmployerStoreList::findOrFail($store_id);
        $timeline_data = $request->input('job_info.timeline_data');
        $e_id = $request->input('user_id');
        $job_title = $request->input('job_info.job_title');
        $job_date = date('Y-m-d', strtotime($request->input('job_info.job_date')));
        $job_rate         = $request->input('job_info.job_rate');
        $job_post_desc    = $request->input('job_info.job_post_desc');
        $cat_id         = $request->input('cat_id');
        $job_region     = $store_info['store_region'];
        $job_zip         = $store_info['store_zip'];
        $job_address     = $store_info['store_address'];
        $job_type         = 1;
        $job_edit_id = $request->input('job_edit_id');
        if ($job_edit_id != 0) {
            JobPost::where("id", $job_edit_id)->update([
                "job_status" => JobPost::JOB_STATUS_DELETED
            ]);
        }
        $job_post = JobPost::create([
            "employer_id" => $e_id,
            "user_acl_profession_id" => $cat_id,
            "job_title" => $job_title,
            "job_date" => $job_date,
            "job_start_time" => '10',
            "job_post_desc" => $job_post_desc,
            "job_rate" => $job_rate,
            "job_type" => $job_type,
            "job_address" => $job_address,
            "job_region" => $job_region,
            "job_zip" => $job_zip,
            "employer_store_list_id" => $store_id,
            "job_status" => 1,
        ]);

        $job_id = $job_post->id;

        if ($is_timeline) {
            $result = array();
            foreach ($timeline_data['job_date_new'] as $key => $name) {
                $result[] = [
                    'job_post_id' => $job_id,
                    'job_date_new'        => date('Y-m-d', strtotime($name)),
                    'job_rate_new'        => $timeline_data['job_rate_new'][$key],
                    'job_timeline_hrs'    => isset($timeline_data['job_timeline_hrs'][$key]) ? $timeline_data['job_timeline_hrs'][$key] : 10,
                    "job_timeline_status" => 3,
                    "created_at" => now(),
                    "updated_at" => now()
                ];
            }
            JobPostTimeline::insert($result);
        }

        return response()->success([
            "job_id" => $job_id,
            "job_post" => (new JobPostExtendedResource($job_post))->jsonSerialize()
        ]);
    }

    public function searchFreelancer(Request $request)
    {
        $job_employer_id = $request->input('user_id');
        $job_id = $request->input('job_id');
        $job = JobPost::where("employer_id", $job_employer_id)->where("id", $job_id)->first();
        if ($job == null) {
            return response()->error("Not found");
        }
        $employer_answers = $job->employer->user_answers;
        $employer_answer_questions = [];
        if ($employer_answers) {
            foreach ($employer_answers as $user_answer) {
                $employer_answer_questions[$user_answer->user_question_id] = $user_answer;
            }
        }
        $freelancers = User::query()->with(["user_answers", "user_work_calender", "user_extra_info", "user_acl_package", "user_package_detail"])->where("user_acl_role_id", 2);
        

        $private_freelancers = PrivateUser::with("private_user_job_actions")->where("employer_id", $job_employer_id)->where("status", "!=", '2')->get();
        $private_freelancers = $private_freelancers->filter(function ($freelancer) {
            $count = $freelancer->private_user_job_actions->count();
            if ($count > 10) {
                return false;
            }
            return true;
        });

        $freelancers = $freelancers->get();

        //filter freelancers according to job
        // $freelancers = $freelancers->filter(function ($freelancer) use ($job, $employer_answer_questions, $job_employer_id) {
        //     $freelancer_answer_questions = [];
        //     foreach ($freelancer->user_answers as $user_answer) {
        //         $freelancer_answer_questions[$user_answer->user_question_id] = $user_answer;
        //     }
        //     $answer_match_count = 0;
        //     $total_answerable_question_count = 0;
        //     foreach ($employer_answer_questions as $question_id => $answer_questions) {
        //         if ($answer_questions->type_value && $answer_questions->type_value != "") {
        //             $question = $answer_questions->question;
        //             if ($question->employer_question && $question->employer_question != "" && $question->freelancer_question && $question->freelancer_question != "") {
        //                 $match = false;
        //                 if (key_exists($question_id, $freelancer_answer_questions)) {
        //                     if ($question->type == 2 || $question->type == 6) {
        //                         $match = $freelancer_answer_questions[$question_id]->type_value === $answer_questions->type_value;
        //                     } elseif ($question->type == 3 && json_decode($answer_questions->type_value) && json_decode($freelancer_answer_questions[$question_id]->type_value)) {
        //                         $match = count(array_diff(json_decode($answer_questions->type_value), json_decode($freelancer_answer_questions[$question_id]->type_value))) === 0;
        //                     } elseif ($question->type == 5 && key_exists($question->range_type_condition, array("1" => ">", "2" => ">=", "3" => "<", "4" => "<=", "5" => "="))) {
        //                         $range_type_condition = array("1" => ">", "2" => ">=", "3" => "<", "4" => "<=", "5" => "=")[$question->range_type_condition];
        //                         return true;
        //                         // $match = is_range_condition_succeed($answer_questions->type_value, $freelancer_answer_questions[$question_id]->type_value, $range_type_condition);
        //                     }
        //                 }
        //                 if ($match) {
        //                     $answer_match_count++;
        //                 }
        //                 $total_answerable_question_count++;
        //             }
        //         }
        //     }
        //     if ($total_answerable_question_count > 0) {
        //         $average_answers = $answer_match_count / $total_answerable_question_count * 100;
        //         if ($average_answers < 60) {
        //             return false;
        //         }
        //     }

        //     $is_block_by_employer = BlockUser::where("freelancer_id", $freelancer->id)->where("employer_id", $job_employer_id)->count() > 0 ? true : false;
        //     if ($is_block_by_employer) {
        //         return false;
        //     }
        //     // $distance = calculate_distance_for_job_search_freelancers($freelancer, $job);
        //     // if (strtolower($freelancer->user_extra_info->max_distance) != "over 500") {
        //     //     if (is_null($distance)) {
        //     //         return false;
        //     //     }
        //     //     if ($distance && $freelancer->user_extra_info->max_distance && $distance > intval($freelancer->user_extra_info->max_distance)) {
        //     //         return false;
        //     //     }
        //     // }


        //     // $freelancer_rate = $freelancer->get_freelancer_rate_on_date($job->job_date);
        //     // $job_rate = $job->job_rate;
        //     // if ($freelancer_rate > $job_rate) {
        //     //     return false;
        //     // }
        //     // if ($freelancer->can_freelancer_get_job_invitation() == false) {
        //     //     return false;
        //     // }
        //     // $user_package_detail = $freelancer->user_package_detail;
        //     // if (is_null($user_package_detail) || ($user_package_detail && Carbon::parse($user_package_detail->package_expire_date)->lessThan(today()))) {
        //     //     return false;
        //     // }
        //     // dd('not blocked' , $freelancer);

        //     $is_available_on_date = $freelancer->is_available_on_date($job->job_date);
        //     return true;
        //     return $is_available_on_date;
        // });

        /**
         * @var mixed $freelancer
         */
        foreach ($freelancers as $freelancer) {
            $freelancer->job_cancellation_rate = get_job_cancellation_rate_by_user($freelancer->id);
            $freelancer->overall_feedback_rating = get_overall_feedback_rating_by_user($freelancer->id);
        }
        $freelancers = $freelancers->sortByDesc("overall_feedback_rating"); 
        $freelancers = $freelancers->sortByDesc("job_cancellation_rate");

        return response(json_encode(["website_locum" => SearchFreelancerResource::collection($freelancers)->jsonSerialize(), "private_locum" => SearchPrivateFreelancerResource::collection($private_freelancers)->jsonSerialize()]));
    }

    public function sendJobInvitation(Request $request)
    {
        $emp_id         = $request->input('user_id');
        $job_id         = $request->input('job_id');
        $employer = User::findOrFail($emp_id);

        $web_freelancer_ids = array_keys(is_array($request->input('web_freelancer', [])) ? array_filter($request->input('web_freelancer', [])) : []);
        $pri_freelancer_ids = array_keys(is_array($request->input('pri_freelancer', [])) ? array_filter($request->input('pri_freelancer', [])) : []);
        return $this->sendInvitationMails($employer, $job_id, $web_freelancer_ids, $pri_freelancer_ids);
        return $web_freelancer_ids; 
    }

    public function sendInvitationMails(User $employer, mixed $id, array $live_freelancer_ids, array $private_freelancer_ids)
    {
        $employer_answers = $employer->user_answers;
        if (sizeof($live_freelancer_ids) == 0 && sizeof($private_freelancer_ids) == 0) {
            return response()->error('Please select a freelancer to send invitation to.');
        }
        $job = JobPost::findOrFail($id);
        if ($job->is_invitation_sent) {
            return response()->error('Invitation already sent for this job.');
        }
        $job_employer_details = $employer->user_extra_info;
        $store_contact_details = $job_employer_details->telephone ?? 0;
        if ($store_contact_details == "") {
            $store_contact_details = $job_employer_details->mobile ?? 0;
        }
        $total_freelancer_count = ($live_freelancer_ids && is_array($live_freelancer_ids) ? sizeof($live_freelancer_ids) : 0) + ($private_freelancer_ids && is_array($private_freelancer_ids) ? sizeof($private_freelancer_ids) : 0);
        $employer_cancellation_rate = get_job_cancellation_rate_by_user($employer->id, "employer");
        $employer_feedback_average = get_overall_feedback_rating_by_user($employer->id, "employer");

        $job_store_address = $job->job_address . ", " . $job->job_region . ", " . $job->job_zip;

        $job_timeline_data = "";
        foreach ($job->job_post_timelines as $timeline) {
            $job_timeline_data .= '<p><strong>Date:</strong> ' . get_date_with_default_format($timeline->job_date_new) . ' <strong>Rate:</strong> ' . set_amount_format($timeline->job_rate_new) . '</p>';
        }
        if ($job_timeline_data === "") {
            $job_timeline_data = "N/A";
        }

        $freelancer_email_section1 = '
            <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px;background-color:#2DC9FF;" colspan="2"> Locumkit Job invitation (Key Details)</th>
                </tr>
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . get_date_with_default_format($job->job_date) . '</td>
                </tr>
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Daily Rate</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . set_amount_format($job->job_rate) . '</td>
                </tr>
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Contact Details</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_contact_details . '</td>
                </tr>
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Address</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job_store_address . '</td>
                </tr>
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Additional Booking Info</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;color:red; font-weight:bold;">' . $job->job_post_desc . '</td>
                </tr>
			</table>
        ';

        $admin_email_section1 = '
            <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%">
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px;background-color:#2DC9FF;" colspan="2"> Locumkit job invitation (Key Details)</th>
                </tr>
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Employer</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $employer->firstname . ' ' .  $employer->lastname . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Employer ID</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $employer->id . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job ref</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->id . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . get_date_with_default_format($job->job_date) . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Daily Rate</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . set_amount_format($job->job_rate) . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Increase rate timeline</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job_timeline_data . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Contact Details</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_contact_details . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Address</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job_store_address . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Additional Booking Info</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;color:red; font-weight:bold;">' . $job->job_post_desc . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date posted</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . get_date_with_default_format($job->created_at) . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Number of people sent to</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $total_freelancer_count . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;"></th>
                <td style=" border: 1px solid black;  text-align:left;">
                    <table style="text-align:left;" width="100%">
                    <tr>
                    <td width="50%" style="border-right:1px solid black;">SMS SEND : 0 </td>
                    <td style="margin-left: 10px; display: block;">EMAIL SEND : ' . $total_freelancer_count . '</td>
                    </tr>
                    </table>
                </td>
                </tr>
			</table>
        ';

        $employer_email_section1 = '
            <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; background-color:#2DC9FF;" colspan="2"> Locumkit job invitation (Key Details)</th>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job ref</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->id . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . get_date_with_default_format($job->job_date) . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Daily Rate</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . set_amount_format($job->job_rate) . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Increase rate timeline</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job_timeline_data . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Contact Details</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $store_contact_details . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Address</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job_store_address . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Additional Booking Info</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;color:red; font-weight:bold;">' . $job->job_post_desc . '</td>
                </tr>
                <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Date posted</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . get_date_with_default_format($job->created_at) . '</td>
                </tr>
			</table>
        ';

        $email_data_employer = "";
        foreach ($employer_answers as $user_answer) {
            $answer_value = json_decode($user_answer->type_value) ? join(" / ", json_decode($user_answer->type_value)) : $user_answer->type_value;

            $email_data_employer .= '
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">' . $user_answer->question->freelancer_question . '</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $answer_value . '</td>
                </tr>
            ';
        }
        $email_data_employer .= '
            <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store cancellation percentage</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $employer_cancellation_rate . '</td>
            </tr>
            <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store feedback percentage</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $employer_feedback_average . '</td>
            </tr>
        ';


        if ($live_freelancer_ids && sizeof($live_freelancer_ids) > 0) {
            $job_invited_users_insert_data = [];
            $job_action_insert_data = [];

            $freelancers = User::with("user_answers", "user_acl_package")->whereIn("id", $live_freelancer_ids)->where("user_acl_role_id", 2)->get();
            foreach ($freelancers as $freelancer) {
                $email_freelancer_data = '';
                foreach ($freelancer->user_answers as $user_answer) {
                    $answer_value = json_decode($user_answer->type_value) ? join(" / ", json_decode($user_answer->type_value)) : $user_answer->type_value;

                    $email_freelancer_data .= '
                        <tr>
                            <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">' . $user_answer->question->freelancer_question . '</th>
                            <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $answer_value . '</td>
                        </tr>
                    ';
                }

                $freelancer_email_section2 = '
                    <tr>
					    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Start Time:</th>
					    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->get_store_start_time() . '</td>
                    </tr>
                    <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Finish Time:</th>
                        <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->get_store_finish_time() . '</td>
                    </tr>
                    <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Lunch Break (minutes):</th>
                        <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->get_store_lunch_time() . '</td>
                    </tr>
                ' . $email_data_employer;

                $freelancer_email_section3 = '
                    <tr>
					  <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">GOC Number:</th>
					  <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freelancer->user_extra_info?->goc . '</td>
				  	</tr>
				  	<tr>
					  <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Opthalmic number (OPL):</th>
					  <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freelancer->user_extra_info?->aoc_id . '</td>
				  	</tr>';
                if ($freelancer->user_extra_info?->aop != '') {
                    $freelancer_email_section3 .= '<tr>
						  <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Insurance (AOP):</th>
						  <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freelancer->user_extra_info?->aop . '</td>
					  </tr>';
                } elseif ($freelancer->user_extra_info?->inshurance_company != '' && $freelancer->user_extra_info?->inshurance_no != '') {
                    $freelancer_email_section3 .= '<tr>
						  <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Insurance:</th>
						  <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . ucfirst($freelancer->user_extra_info?->inshurance_company) . '-' . $freelancer->user_extra_info->inshurance_no . '</td>
					  	</tr>
						<tr>
							<th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Insurance expiry:</th>
							<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $freelancer->user_extra_info?->inshurance_renewal_date . '</td>
					  	</tr>';
                }

                $freelancer_email_section3 .= $email_freelancer_data;

                $job_action_insert_data[] = [
                    "job_post_id" => $job->id,
                    "freelancer_id" => $freelancer->id,
                    "created_at" => now(),
                    "updated_at" => now(),
                ];
                $job_invited_users_insert_data[] = [
                    "job_post_id" => $job->id,
                    "invited_user_id" => $freelancer->id,
                    "invited_user_type" => JobInvitedUser::USER_TYPE_LIVE,
                    "created_at" => now(),
                    "updated_at" => now(),
                ];

                $encrypted_job_id = encrypt($job->id);
                $encrypted_freelancer_id = encrypt($freelancer->id);
                $encrypted_freelancer_type = encrypt("live");
                $accept_href_link = url("/accept-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");

                $negotiate_href_link = url("/negotiate/freelancer-negotiate-on-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");

                $link = '<a href="' . $accept_href_link . '" style="float: left;  margin-bottom: 15px;  margin-top: -10px;outline: none !important;border-radius: 25px;float: left;margin-bottom: 15px;font-size: 18px;color: #fff;background-color: #2dc9ff;padding: 10px 35px;text-decoration: none;text-transform: uppercase;font-weight: 500;">Accept</a>';
                $negotiate_link = '<a href="' . $negotiate_href_link . '" style="float: left;  margin-bottom: 15px;  margin-top: -10px;outline: none !important;border-radius: 25px;float: left;margin-bottom: 15px;font-size: 18px;color: #fff;background-color: #2dc9ff;padding: 10px 35px;text-decoration: none;text-transform: uppercase;font-weight: 500;">Negotiate on Rate</a>';


                $can_user_freeze_job = can_user_package_has_privilege($freelancer, 'job_freeze');
                if (today()->addDays(2)->lessThan($job->job_date) && $can_user_freeze_job) {
                    $freeze_href_link = url("/freeze-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");
                    $link .= ' <p style="float: left; margin: 13px; font-size: 20px;"> OR &nbsp; </p> <a style="outline: none !important;border-radius: 25px;float: left;margin-bottom: 22px;font-size: 18px;color: #fff;background-color: #2dc9ff;padding: 10px 35px;text-decoration: none;text-transform: uppercase;font-weight: 500;" href="' . $freeze_href_link . '">Freeze</a>';
                }

                if ($freelancer_email_section3 != '') {
                    $freelancer_email_section3_data = '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%">
					       <tr>
							<th style=" border: 1px solid black;  text-align:left; padding:5px;background-color:#2DC9FF;" colspan="2"> Locumkit job invitation – information you provided us
							</th>
						  </tr>
						  <tr>
							<th style=" border: 1px solid black;  text-align:left; padding:5px;color:red; font-weight:bold;text-align:center;" colspan="2">
							Please check the details below and advise us immediately if this information is incorrect
							</th>
						  </tr>
						' . $freelancer_email_section3 . '
						</table>';
                }

                $mail_body = '
                    <div style="padding: 25px 50px 5px; text-align: left; ">
                    <p>Hi ' . $freelancer->firstname . ',</p>
                    <p>We would like to inform you that a new job that matches your requirements has been posted. You can see the job details below:</p>
                    <h3>Job Information</h3>
                    ' . $freelancer_email_section1 . '
                    <br/>
                    <p>' . $link . '<p>
                    <br/>
                    <p>' . $negotiate_link . '<p>
                    <br/>
                    <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%">
                        <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; background-color:#2DC9FF;" colspan="2">Locumkit job invitation (additional information)</th>
                        </tr>
                    ' . $freelancer_email_section2 . '
                    </table>
                    <br/>
                    ' . $freelancer_email_section3_data . '
                    <br/>
                    ' . get_locum_email_terms() . '
                    </div>
                ';

                $mail_subject = 'Locumkit job notification: Date : ' . get_date_with_default_format($job->job_date) . ' / Location : ' . $job_store_address . ' / Rate : ' . set_amount_format($job->job_rate);

                Mail::to($freelancer->email)->send(new FreelancerJobInvitationMail($mail_subject, $mail_body));

                $this->notifyController->notification($job->id, $message = "Job Ref:" . $job->id . ', Date:' . get_date_with_default_format($job->job_date) . ', Location:' . $job_store_address . ', Rate:' . set_amount_format($job->job_rate) . ', Open this message to view full details.', $title = 'Job invitation', $freelancer->id, $types = "acceptJob");
                $this->jobsmsController->jobInvitationFreeSms($freelancer, $job, $accept_href_link);
            }

            JobAction::insert($job_action_insert_data);
            JobInvitedUser::insert($job_invited_users_insert_data);
        }

        if ($private_freelancer_ids && sizeof($private_freelancer_ids) > 0) {
            $job_invited_users_insert_data = [];
            $job_action_insert_data = [];

            $freelancers = PrivateUser::whereIn("id", $private_freelancer_ids)->where("employer_id", $employer->id)->get();
            foreach ($freelancers as $freelancer) {
                $job_action_insert_data[] = [
                    "employer_id" => $employer->id,
                    "private_user_id" => $freelancer->id,
                    "job_post_id" => $job->id,
                    "status" => PrivateUserJobAction::ACTION_WAITING,
                    "created_at" => now(),
                    "updated_at" => now(),
                ];
                $job_invited_users_insert_data[] = [
                    "job_post_id" => $job->id,
                    "invited_user_id" => $freelancer->id,
                    "invited_user_type" => JobInvitedUser::USER_TYPE_PRIVATE,
                    "created_at" => now(),
                    "updated_at" => now(),
                ];
                $encrypted_job_id = encrypt($job->id);
                $encrypted_freelancer_id = encrypt($freelancer->id);
                $encrypted_freelancer_type = encrypt("private");
                $accept_href_link = url("/accept-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");

                $link = '<a style="outline: none !important;float: left;font-size: 18px;background-color: #2dc9ff;padding: 7px 30px;color: #fff;text-transform: uppercase;text-decoration: none;border-radius: 25px;margin-bottom: 0px;" href="' . $accept_href_link . '">Accept</a>';

                $private_freelancer_email_section2 = $email_data_employer;

                $mail_body = '
                    <div style="padding: 25px 50px 5px; text-align: left; ">
                        <p>Hello ' . $freelancer->name . ',</p>
                        <p>Locumkit is a platform that matches employers with locums, with no middleman involved.</p>
                        <p>To find out more about Locumkit, please <a href="https://www.youtube.com/watch?v=uM4Og3BxQm0" target="_blank">click here</a> </p>
                        <p>Our client is looking for a locum - please find below details for the day in question. To accept the job, please click on accept and we shall notify the employer, who can then close the job. </p>

                        <h3>Job Information</h3>
                        ' . $freelancer_email_section1 . '
                        <br/>
                        <p style="float:left;width:100%;">' . $link . '<p>
                        <p>To continue receiving job notifications like these please <a href="' . url('/private-invitation') . '" target="_blank">click here</a></p>
                        <br/>
                        <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%">
                        <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; background-color:#2DC9FF;" colspan="2"> Locumkit job invitation (additional information)</th>
                        </tr>
                        <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Start Time:</th>
                        <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->get_store_start_time() . '</td>
                        </tr>
                        <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Finish Time:</th>
                        <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->get_store_finish_time() . '</td>
                        </tr>
                        <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Lunch Break (minutes):</th>
                        <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->get_store_lunch_time() . '</td>
                        </tr>
                        ' . $private_freelancer_email_section2 . '
                        </table>
                        <br/>
                        ' . get_locum_email_terms() . '
                        <p>About Locumkit:</p>
                        <p>Locumkit is designed to connect employers with locums. Locumkit offers plenty of benefits, functions, and services that you will certainly find very useful. From a single location, you will be able to monitor your bookings, work history, financials, new job opportunities, and much more. </p>
                        <p>Locumkit not only puts you at the center of our focus, we field highly cable teams, with depth and experience of Optometry and Accounting, on every job. Locumkit is a  bespoke & innovative platform created and run by experienced optometrists over 25 years of first hand experience of which 15 years has been as locums with a range of employers from multiples, independents, to eye casualties and domiciliary. </p>
                        <p>In addition to that there are many other benefits of Locumkit such as: </p>
                        <ul>
                            <li><p>Get many more job bookings like this</p></li>
                            <li><p>Get job bookings tailored to your requirements; day rate, distance willing to travel</p></li>
                            <li><p>Get job reminders irrespective if from our website or "off website"</p></li>
                            <li><p>Upto date accounting - accessed from anywhere, anytime</p></li>
                            <li><p>Automated book keeping and all your statutory financial compliance taken care of</p></li>
                        </ul>

                        <p>Why not visit Locumkit and join the platform where you can have that many significant benefits and dramatically boost your job opportunities?</p>

                        <p>Please visit our website for more information <a href="' . url('/') . '">www.locumkit.com</a></p>

                    </div>
                ';

                $mail_subject = 'Locumkit job notification: Date : ' . get_date_with_default_format($job->job_date) . ' / Location : ' . $job_store_address . ' / Rate : ' . set_amount_format($job->job_rate);

                Mail::to($freelancer->email)->send(new FreelancerJobInvitationMail($mail_subject, $mail_body));

                $this->jobsmsController->jobInvitationFreeSms($freelancer, $job, $accept_href_link);
            }

            PrivateUserJobAction::insert($job_action_insert_data);
            JobInvitedUser::insert($job_invited_users_insert_data);
        }

        $job->is_invitation_sent = true;
        $job->save();

        //Sending emails to admin and employer

        $freelancer_email_section2 = '
            <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Start Time:</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->get_store_start_time() . '</td>
            </tr>
            <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Finish Time:</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->get_store_finish_time() . '</td>
            </tr>
            <tr>
                <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Lunch Break (minutes):</th>
                <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->get_store_lunch_time() . '</td>
            </tr>
        ';
        $freelancer_email_section2 .= $email_data_employer;

        $admin_mail_body = '
            <div style="padding: 25px 50px 5px; text-align: left;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
                <p>Hello <b>Admin</b>,</p>
                <p>A new job has just been posted by: <b>' . $employer->firstname . '</b></p>
                <h3>Job Information</h3>
                ' . $admin_email_section1 . '
                <br/>
                <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%">
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; background-color:#2DC9FF;" colspan="2"> Locumkit Job invitation (additional information)</th>
                    </tr>
                ' . $freelancer_email_section2 . '
                </table>
                <br/>
            </div>
        ';

        $employer_mail_subject = 'Locumkit: New job posting (#' . $job->id . ')';
        $employer_mail_body = '
            <div style="padding: 25px 50px 5px; text-align: left;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
                <p>Hello ' . $employer->firstname . ',</p>
                <p>We would like to inform you that your job post has been confirmed and is now active. The selected locums have been notified.</p><p>You will be notified once a locum has accepted your booking.</p>
                <h3>Job Information</h3>
                ' . $employer_email_section1 . '
                <br/>
                <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%">
                <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; background-color:#2DC9FF;" colspan="2"> Locumkit job invitation (additional information)</th>
                    </tr>
                ' . $freelancer_email_section2 . '
                </table>
                <br/>
                <p>Should you need to cancel this job, please <a href="' . url('/cancel-job/' . $job->id) . '">click here</a>.</p>
                <p>Should you need to edit this job, please <a href="' . url('/managejob/' . $job->id) . '">click here</a>.</p>
            </div>
        ';
        $admin_mail_subject = 'Locumkit job notification: New job posting : #' . $job->id;

        Mail::to(config('app.admin_mail'))->send(new FreelancerJobInvitationMail($admin_mail_subject, $admin_mail_body));

        Mail::to($employer->email)->send(new FreelancerJobInvitationMail($employer_mail_subject, $employer_mail_body));

        $this->jobsmsController->jobInvitationemployerSms($job->employer, $job->id, null);

        return response()->success([], 'Inviation sent successfully');
    }

    public function jobList(Request $request)
    {
        // dd($request->all());
        $user_id = $request['user_id'];
        $role_id = $request['user_role'];
        $filter = isset($request['filter']) && in_array(intval($request['filter']), range(1, 8)) ? intval($request['filter']) : null;
        $limit = isset($request['offset']) ? ($request['offset'] * 45) : 45;
        $limit = 1000;
        $offset = isset($request['offset']) ? $request['offset'] : 1;
        $sortby = isset($request['sortby']) && in_array(strtolower($request['sortby']), ["job_title", "id", "job_date"]) ? strtolower($request['sortby']) : 'job_date';
        $sortele = isset($request['sortele']) && strtolower($request['sortele']) == 'asc' ? 'ASC' : 'DESC';
        $job_list = array();

        /* Employer Job List */
        if ($role_id == 3) {
            $job_posts = JobPost::with(["job_cancellation", "job_actions", "private_user_job_actions"])->where("employer_id", $user_id)->orderBy('created_at', 'Desc');
            $job_posts = $filter ? $job_posts->where("job_status", $filter) : $job_posts->where("job_status", "!=", JobPost::JOB_STATUS_DELETED);
            $job_posts = $job_posts->orderBy($sortby, $sortele)->offset(($offset - 1) * 50)->limit($limit)->get();

                    // 'job_title' => strlen($job['job_title']) > 10 ? substr($job['job_title'], 0, 10) . "..." : $job['job_title'],
            foreach ($job_posts as $job) {
                $job_list[]    = array(
                    'job_id' => $job['id'], 
                    'job_title' => $job['job_title'] ?? '',
                    'job_rate' => set_amount_format($job['job_rate']),
                    'job_date' => get_date_with_default_format($job['job_date']),
                    'job_status' => $this->getStatusByJob($job),
                    'job_status_id' => $job['job_status'],
                    'job_locum' => $job->getAcceptedFreelancerData()["name"]
                );
            }
            
        }
        /* Freelancer Job List */
        if ($role_id == 2) {
            
            $job_posts = JobPost::with(["job_cancellation", "job_actions", "private_user_job_actions", "job_store"]);
            
            
            $job_posts = $filter ? $job_posts->where("job_status", $filter) : $job_posts->whereIn("job_status", [JobPost::JOB_STATUS_ACCEPTED, JobPost::JOB_STATUS_DONE_COMPLETED, JobPost::JOB_STATUS_CANCELED, JobPost::JOB_STATUS_OPEN_WAITING]);

            $job_posts = $job_posts->whereHas("job_actions", function ($query) use ($user_id) {
                $query->where("freelancer_id", $user_id)->whereIn("action", [JobAction::ACTION_FREEZE, JobAction::ACTION_ACCEPT, JobAction::ACTION_DONE, JobAction::ACTION_CANCEL_JOB_BY_FREELANCER, JobAction::ACTION_APPLY, JobAction::ACTION_CANCEL_ACCEPTED_JOB_BY_EMPLOYER]);
            });
            $job_posts = $job_posts->orderBy($sortby, $sortele)->offset(($offset - 1) * 15)->limit($limit)->get();
            
            foreach ($job_posts as $job) {
                $job_list[]    = array(
                    'job_id'         => $job['id'],
                    'job_rate'         => set_amount_format($job['job_rate']),
                    'job_date'         => get_date_with_default_format($job['job_date']),
                    'job_status'     => $this->getStatusByJob($job),
                    'job_status_id' => $job['job_status'],
                    'job_store_name' => $job->job_store->store_name
                );
            }
        }
        
        return response()->success($job_list);
    }

    public function getStatusByJob(JobPost $job): string
    {
        $status = '';
        switch ($job->job_status) {
            case 1:
                $status = 'Waiting';
                break;
            case 2:
                $status = 'Expired';
                break;
            case 3:
                $status = 'Disabled';
                break;
            case 4:
                $status = 'Accepted';
                break;
            case 5:
                $status = 'Completed';
                break;
            case 6:
                $status = 'Frozen';
                break;
            case 8:
                if ($job->job_cancellation && $job->job_cancellation->cancel_by_user_type == JobCancelation::CANCEL_BY_EMPLOYER) {
                    $status = '(By employer) Cancelled';
                } else {
                    $status = '(By locum) Cancelled';
                }
                break;
        }
        return $status;
    }

    public function jobView(Request $request)
    {
        $user_id = $request['user_id'];
        $role_id = $request['user_role'];
        $cat_id = $request['user_profession'];
        $job_id = $request['job_id'];
        $job_details = array();
        $emp_details = array();
        $fre_details = array();
        if ($role_id == 2) {
            $job = JobPost::where("id", $job_id)->whereHas("job_actions", function ($query) use ($user_id) {
                $query->where("freelancer_id", $user_id);
            })->first();
            if (is_null($job) || is_null($job->employer)) {
                return response()->error("Not found");
            }
            $job_date = $job->job_date;
            $job_day =  date('l', strtotime($job_date));

            $job['store_start_time'] = $job->get_store_start_time();
            $job['store_end_time']   = $job->get_store_finish_time();
            $job['store_lunch_time'] = $job->get_store_lunch_time() . ' (Min)';

            $employer = $job->employer->user_extra_info;
            $store_contact_details = $employer['mobile'];
            if ($employer['telephone']  != '') {
                $store_contact_details = $employer['telephone'];
            }
            $job['store_contact_details'] = $store_contact_details;
            $job['store_address'] = $job['job_address'] . ', ' . $job['job_region'] . ', ' . $job['job_zip'];

            /* View job Key Details */
            $job_details['job_post_date']  = get_date_with_default_format($job->created_at);
            $job_details['job_date']  = get_date_with_default_format($job['job_date']);
            $job_details['job_title'] = $job['job_title'];
            $job_details['job_rate']  = set_amount_format($job['job_rate']);
            $job_details['store_contact_details']  = $store_contact_details;
            $job_details['store_address']   = $job['job_address'] . ', ' . $job['job_region'] . ', ' . $job['job_zip'];
            $job_details['job_details']     = $job['job_post_desc'];
            $job_details['job_status']      = $job['job_status'];


            $user_qus_ans['start_time']   = $job['store_start_time'];
            $user_qus_ans['end_time']     = $job['store_end_time'];
            $user_qus_ans['lunch_time']   = $job['store_lunch_time'];
            /* Booking Confirmation – Details of Employer */
            if ($job['job_status'] == JobPost::JOB_STATUS_OPEN_WAITING) {
                $user_qus_ans['qus_ans'] = $this->getQusAnsByUid($job['employer_id'], 3, $cat_id);
            } else {
                $user_qus_ans['qus_ans'] = $this->getQusAnsByUid($user_id, $role_id, $cat_id);
            }

            /* Booking Confirmation – Details of Employer */
            $emp_details['id']         = $employer->id;
            $emp_details['name']     = $employer->firstname . ' ' . $employer->lastname;

            /*Booking confirmation (additional information)*/
            $user_extra_info = User::findOrFail($user_id)->user_extra_info;
            $fre_details['stores']         = [$user_extra_info->store_type_name];
            $fre_details['stores_exp']     = [$user_extra_info->store_type_name];
            $fre_details['goc']         = $user_extra_info['goc'] ?? 0;
            $fre_details['opl']         = $user_extra_info['aoc_id'];
            $fre_details['aop']         = $user_extra_info['aop'];
            $fre_details['insurance']         = $user_extra_info['inshurance_company'];
            $fre_details['insurance_exp']     = $user_extra_info['inshurance_renewal_date'];
            $fre_details['min_rate']     = $this->getFormatedMinRate(json_decode($user_extra_info['minimum_rate'], true));
            $fre_details['max_dist']     = $user_extra_info['max_distance'] . ' Miles';
            //echo $store_id;
            if ($job_details['job_status'] == JobPost::JOB_STATUS_OPEN_WAITING) {
                $fre_details['fre_qus']    = $this->getQusAnsByUid($user_id, 2, $cat_id);
            }
            $job_final_details["job_details"]    =  $job_details;
            $job_final_details["user_qus_ans"]    =  $user_qus_ans;
            $job_final_details["emp_details"]    =  $emp_details;
            $job_final_details["fre_details"]    =  $fre_details;
        }

        if ($role_id == 3) {
            $job = JobPost::where("id", $job_id)->where("employer_id", $user_id)->first();

            $job_date         = $job->job_date;
            $job_day         =  date('l', strtotime($job_date));

            $job['store_start_time'] = $job->get_store_start_time();
            $job['store_end_time']   = $job->get_store_finish_time();
            $job['store_lunch_time'] = $job->get_store_lunch_time() . ' (Min)';

            $employer = $job->employer->user_extra_info;
            $store_contact_details = $employer['mobile'];
            if ($employer['telephone']  != '') {
                $store_contact_details = $employer['telephone'];
            }
            $job['store_contact_details'] = $store_contact_details;
            $job['store_address'] = $job['job_address'] . ', ' . $job['job_region'] . ', ' . $job['job_zip'];

            /* View job Key Details */
            $job_details['job_post_date']  =  get_date_with_default_format($job->created_at);
            $job_details['job_date']  = get_date_with_default_format($job['job_date']);
            $job_details['job_title'] = $job['job_title'];
            $job_details['job_rate']  = set_amount_format($job['job_rate']);
            $job_details['store_contact_details']  = $store_contact_details;
            $job_details['store_address']   = $job['job_address'] . ', ' . $job['job_region'] . ', ' . $job['job_zip'];
            $job_details['job_details']     = $job['job_post_desc'];
            $job_details['job_status']      = $job['job_status'];

            /*Booking confirmation (additional information)*/
            $user_qus_ans['start_time']     = $job['store_start_time'];
            $user_qus_ans['end_time']       = $job['store_end_time'];
            $user_qus_ans['lunch_time']     = $job['store_lunch_time'];

            /*ques details*/
            $user_qus_ans['qus_ans']         = $this->getQusAnsByUid($user_id, $role_id, $cat_id);

            $freelancer_for_job_info = $job->getAcceptedFreelancerData();
            $fre_details['id'] = $freelancer_for_job_info["id"];
            $fre_details['name'] = $freelancer_for_job_info["name"];
            if ($freelancer_for_job_info["id"]) {
                if ($freelancer_for_job_info["type"] == "private") {
                    $fre_qus_details['fre_qus'] = null;
                } else {
                    $fre_qus_details['fre_qus'] = $this->getQusAnsByUid($freelancer_for_job_info["id"], 2, $cat_id);
                }
            }

            $job_final_details["job_details"]    =  $job_details;
            $job_final_details["user_qus_ans"]    =  $user_qus_ans;
            $job_final_details["fre_details"]    =  $fre_details;
            $job_final_details["fre_qus_details"]    =  $fre_qus_details;

            $nego_job_action = $job->job_actions()->where("is_negotiated", true)->orderBy("updated_at", "DESC")->first();
            if ($nego_job_action && $nego_job_action->freelancer) {
                $nego_job_action->freelancer->job_cancellation_rate = get_job_cancellation_rate_by_user($nego_job_action->freelancer->id);
                $nego_job_action->freelancer->overall_feedback_rating = get_overall_feedback_rating_by_user($nego_job_action->freelancer->id);
                $job_final_details["nego_job_action"] = $nego_job_action;
            }
        }

        return response()->json($job_final_details);
    }

    public function getQusAnsByUid($uid, $role_id, $cat_id)
    {
        $qus_data = UserQuestion::select(DB::raw("freelancer_question as fq, employer_question as eq, type as tk, id as qid, is_required as required_status, range_type_unit, range_type_condition"))
            ->where("user_acl_profession_id", $cat_id)->get();
        $user_answers = UserAnswer::where("user_id", $uid)->get();

        $txt_ans        = "";
        $ques_details     = array();
        foreach ($qus_data as $resultset) {
            $result_data_ans = $user_answers->first(function ($value) use ($resultset) {
                return $value->user_question_id === $resultset->qid;
            });
            if ($role_id == 2) {
                $question = $resultset['fq'];
            } else {
                $question = $resultset['eq'];
            }
            $txt_ans = "";
            if ($result_data_ans) {
                $txt_ans = $result_data_ans['type_value'];
            }
            if ($resultset['tk'] == 1 && $question != '') { // text field
                $ques_details[] = array('qus' => $question, 'ans' => $txt_ans);
            }
            if ($resultset['tk'] == 2 && $question != '') { // select option
                $ques_details[] = array('qus' => $question, 'ans' => $txt_ans);
            }
            if ($resultset['tk'] == 3 && $question != '') { // multiselect option
                $txt_ans = json_decode($txt_ans) ? implode(" / ", json_decode($txt_ans)) : $txt_ans;
                $ques_details[] = array('qus' => $question, 'ans' => $txt_ans);
            }
            if ($resultset['tk'] == 4 && $question != '') { // select option for range
                $range_type_unit = $resultset['range_type_unit'];
                $ques_details[] = array('qus' => $question, 'ans' => $txt_ans . $range_type_unit);
            }
            if ($resultset['tk'] == 5 && $question != '') { // select option
                $txt_ans = json_decode($txt_ans) ? implode(" / ", json_decode($txt_ans)) : $txt_ans;
                $ques_details[] = array('qus' => $question, 'ans' => $txt_ans . ' ' . $resultset['range_type_unit']);
            }
            if ($resultset['tk'] == 6 && $question != '') { // select option
                $ques_details[] = array('qus' => $question, 'ans' => $txt_ans . ' ' . $resultset['range_type_unit']);
            }
        }

        return $ques_details;
    }

    public function getFormatedMinRate($rates)
    {
        $min_rate = array();
        if ($rates && sizeof($rates) > 0) {
            $min_rate['Monday']     = set_amount_format($rates['Monday']);
            $min_rate['Tuesday']     = set_amount_format($rates['Tuesday']);
            $min_rate['Wednesday']     = set_amount_format($rates['Wednesday']);
            $min_rate['Thursday']     = set_amount_format($rates['Thursday']);
            $min_rate['Friday']     = set_amount_format($rates['Friday']);
            $min_rate['Saturday']     = set_amount_format($rates['Saturday']);
            $min_rate['Sunday']     = set_amount_format($rates['Sunday']);
        }
        return $min_rate;
    }

    public function jobAction(Request $request)
    {
        $job_id = $request['job_id'];
        $user_id = $request['user_id'];
        $action = $request['job_action'];

        $job = JobPost::where("id", $job_id)->first();
        if (is_null($job)) {
            return response()->error("Not found");
        }

        if ($action == 'duplicate') {
            return response()->success((new JobPostExtendedResource($job))->jsonSerialize());
        }

        if ($action == 'delete') {
            $job->job_status = JobPost::JOB_STATUS_DELETED;
            $job->save();
            return response()->success([], "Job deleted successfully");
        }
        if ($action == 'disable') {
            $job->job_status = JobPost::JOB_STATUS_DISABLED;
            $job->save();
            return response()->success([], "Job disabled successfully");
        }
        if ($action == 'enable') {
            $job->job_status = JobPost::JOB_STATUS_OPEN_WAITING;
            $job->save();
            return response()->success([], "Job enabled successfully");
        }
        if ($action == 'cancel') {
            $this->cancelJob($request->all());
            return response()->success([], "Job canceled successfully");
        }

        return response()->error("Invalid request");
    }

    private function cancelJob($job_data)
    {
        $cjid                 = $job_data['job_id'];
        $uid                 = $job_data['user_id'];
        $uType                 = $job_data['user_role'];
        $cancel_reason        = $job_data['cancel_reason'];
        $mailController = new JobMailHelper();

        if ($uType == 3) {
            $job = JobPost::where("id", $cjid)->where("employer_id", $uid)->first();
            if ($job == null) {
                return response("Not found", 404);
            }
            JobCancelation::create([
                "job_id" => $job->id,
                "user_id" => $uid,
                "reason" => $cancel_reason,
                "cancel_by_user_type" => JobCancelation::CANCEL_BY_EMPLOYER,
            ]);
            $job_action = JobAction::where("job_post_id", $job->id)->where("action", JobAction::ACTION_ACCEPT)->first();
            if ($job_action) {
                $job_action->action = JobAction::ACTION_CANCEL_ACCEPTED_JOB_BY_EMPLOYER;
                $job_action->save();
            } else {
                JobAction::where("job_post_id", $job->id)->update([
                    "action" => JobAction::ACTION_CANCEL_OPEN_JOB_BY_EMPLOYER
                ]);
            }
            $job_private_action = PrivateUserJobAction::where("job_post_id", $job->id)->where("status", PrivateUserJobAction::ACTION_ACCEPT)->first();
            if ($job_private_action) {
                $job_private_action->status = PrivateUserJobAction::ACTION_CANCEL;
                $job_private_action->save();
            }
            $job->job_status = JobPost::JOB_STATUS_CANCELED;
            $job->save();
            if ($job_action) {
                $mailController->cancelJobByEmpNotificationToFreelancer($job_action->freelancer, $job, $cancel_reason);
                $mailController->cancelJobByEmpNotificationToEmployer($job->employer, $job_action->freelancer, $job, $cancel_reason);
                FinanceEmployer::where("employer_id", $job->employer_id)->where("job_id", $job->id)->where("freelancer_id", $job_action->freelancer_id)->delete();
            } else if ($job_private_action) {
                $mailController->cancelJobByEmpNotificationToPrivateFreelancer($job_private_action->private_user, $job, $cancel_reason);
                $mailController->cancelJobByEmpNotifyToEmployerIFPrivatefreelancer($job->employer, $job_private_action->private_user, $job, $cancel_reason);
            }
            $mailController->cancelJobByEmpNotificationToAdmin($job->employer, $job, $cancel_reason);
        } elseif ($uType == 2) {

            $job = JobPost::where("id", $cjid)->whereHas("job_actions", function ($query) use ($uid) {
                $query->where("freelancer_id", $uid)->where("action", JobAction::ACTION_ACCEPT);
            })->first();
            if ($job == null) {
                return response("Not found", 404);
            }
            if ($job->job_status != JobPost::JOB_STATUS_ACCEPTED) {
                return response("Job must ne accepted to cancel", 400);
            }

            JobCancelation::create([
                "job_id" => $job->id,
                "user_id" => $uid,
                "reason" => $cancel_reason,
                "cancel_by_user_type" => JobCancelation::CANCEL_BY_LIVE_FREELANCER,
            ]);
            $job->job_status = JobPost::JOB_STATUS_CANCELED;
            //Freelancer Cancel job action update
            $job_action = JobAction::where("job_post_id", $job->id)->where("freelancer_id", $uid)->where("action", JobAction::ACTION_ACCEPT)->first();
            if ($job_action) {
                $job_action->action = JobAction::ACTION_CANCEL_JOB_BY_FREELANCER;
                $job_action->save();
            }
            $job->save();

            $mailController->cancelJobByFreNotificationToFreelancer($job->employer, $job_action->freelancer, $job, $cancel_reason);
            $mailController->cancelJobByFreNotificationToEmployer($job_action->freelancer, $job->employer, $job, $cancel_reason, $job->job_relist);
            $mailController->cancelJobByFreNotificationToAdmin($job_action->freelancer, $job, $cancel_reason);
            FinanceEmployer::where("employer_id", $job->employer_id)->where("job_id", $job->id)->where("freelancer_id", $uid)->delete();
        }
    }

    public function jobActionHandler(Request $request)
    {
        $user_id = $request['user_id'];
        $page_id = $request['page_id'];
        $user_data = $request->all();
        $jobActionResponse = response()->error('Invalid request');
        switch ($page_id) {
            case 'interested-job-list':
                $jobActionResponse = $this->get_invite_job_list($user_id);
                break;
            case 'accept-job':
                $job_id = isset($user_data['job_id']) ? $user_data['job_id'] : null;
                $jobActionResponse = $this->job_accept($user_id, $job_id);
                break;
            case 'negotiate-job':
                $job_id = isset($user_data['job_id']) ? $user_data['job_id'] : null;
                $formdata = isset($user_data['formdata']) ? $user_data['formdata'] : null;
                $jobActionResponse = $this->job_negotiate($user_id, $job_id, $formdata);
                break;
            case 'freeze-job':
                $user = User::findOrFail($user_id);
                $is_user_pkg_allow_job_freeze = can_user_package_has_privilege($user, 'job_freeze');
                if ($is_user_pkg_allow_job_freeze) {
                    $job_id = isset($user_data['job_id']) ? $user_data['job_id'] : null;
                    $jobActionResponse = $this->job_freeze($user_id, $job_id);
                } else {
                    $jobActionResponse = response()->error('You cannot freeze job due to your current package limit');
                }
                break;
            case 'attend-job':
                $job_id = isset($user_data['job_id']) ? $user_data['job_id'] : null;
                $attend = isset($user_data['is_attend']) ? $user_data['is_attend'] : null;
                $user_role = isset($user_data['user_role']) ? $user_data['user_role'] : null;
                $job_type = isset($user_data['job_type']) ? $user_data['job_type'] : null;
                $jobActionResponse = $this->job_attendance($user_id, $user_role, $job_id, $attend, $job_type);
                break;
            case 'job-expense':
                $job_id     = isset($user_data['job_id']) ? $user_data['job_id'] : null;
                $job_type     = isset($user_data['job_type']) ? $user_data['job_type'] : null;
                $request     = isset($user_data['request']) ? $user_data['request'] : null;
                $data         = isset($user_data['data']) ? $user_data['data'] : null;
                $jobActionResponse = $this->job_expense($user_id, $job_id, $job_type, $request, $data);
                
                // Sending Notification
                $notificationHelper = new AppNotificationHelper();
                $job_id = $job_id;
                $message = 'Job Expanse Saved Successfully.';
                $title = 'Job Expanse';
                $user_id = $user_data['user_id'];
                $types = 'jobExpense';
                $token_id = $this->getTokenByID($user_data['user_id']);
        
                $notificationHelper->notification($job_id, $message, $title, $user_id, $types, $token_id);
                break;
        }
        return $jobActionResponse;
    }
    
    public function getTokenByID($user_id)
    {
        $tokenID = MobileNotification::where("user_id", $user_id)->latest()->first();
        if ($tokenID) {
            return $tokenID->token_id;
        }
        return null;
    }

    public function get_invite_job_list($user_id)
    {
        $user = User::findOrFail($user_id);
        $allLiveJobs = JobPost::select("job_date", "job_post_desc", "job_rate", "employer_store_list_id")
            ->whereIn("job_status", [JobPost::JOB_STATUS_ACCEPTED, JobPost::JOB_STATUS_DONE_COMPLETED])
            ->whereHas("job_actions", function ($query) use ($user_id) {
                $query->where("freelancer_id", $user_id);
                $query->whereIn("action", [JobAction::ACTION_ACCEPT, JobAction::ACTION_DONE]);
            })->orderBy("job_date")->get();

        $liveBookDates = $allLiveJobs->map(function ($job) {
            return $job->job_date->format("Y-m-d");
        })->toArray();
        //Private jobs
        $privateJobs =  $user->private_jobs()->orderBy("job_date")->get();
        $privateBookDates = $privateJobs->map(function ($job) {
            return $job->job_date->format("Y-m-d");;
        })->toArray();
        $bookedDates = array_merge($liveBookDates, $privateBookDates);

        $intersetedJobs = JobPost::with("job_actions")->whereIn("job_status", [JobPost::JOB_STATUS_OPEN_WAITING, JobPost::JOB_STATUS_FREEZED])
            ->whereHas("job_actions", function ($query) use ($user_id) {
                $query->where("freelancer_id", $user_id);
                $query->whereIn("action", [JobAction::ACTION_NONE, JobAction::ACTION_FREEZE]);
            })
            ->whereNotIn("job_date", $bookedDates)->orderBy("job_date")->get();
        $aviJobArray = array();
        foreach ($intersetedJobs as $key => $jobRecords) {
            $aviJobArray[$key] = (new JobPostExtendedResource($jobRecords))->jsonSerialize();
            $aviJobArray[$key]['job_rate'] = set_amount_format($jobRecords['job_rate']);
            $aviJobArray[$key]['store_id'] = $jobRecords->job_store->store_name;
            $aviJobArray[$key]['allow_freeze'] = $this->is_job_allow_to_freeze($user_id, $jobRecords['job_id'], $jobRecords['job_date'], $jobRecords['job_status']);
            $aviJobArray[$key]['allow_negotiate'] = $jobRecords->job_actions->first()->is_negotiated ? false : true;
        }

        return response()->success($aviJobArray);
    }

    public function is_job_allow_to_freeze($user_id, $job_id, $job_date, $status)
    {
        $freezeJobId = JobAction::where("job_post_id", $job_id)->where("freelancer_id", $user_id)->where("freeze_notification_count", 1)->count();
        $jobAllowFreezeDate = strtotime('+2 days');
        $jobWorkDate = strtotime($job_date);
        if ($status != JobPost::JOB_STATUS_FREEZED && $freezeJobId == 0 && $jobAllowFreezeDate < $jobWorkDate) {
            return 1;
        } else {
            return 0;
        }
    }

    public function job_accept($freelancer_id, $job_id)
    {
        $jobManagementController = new JobManagementController();
        $job = JobPost::findOrFail($job_id);
        $employer = $job->employer;
        $success = null;
        $error = "Invalid request";
        $freelancer = User::findOrFail($freelancer_id);

        $is_available_on_date = $freelancer->is_available_on_date($job->job_date);
        $user_job_action = JobAction::where("freelancer_id", $freelancer_id)->where("job_post_id", $job_id)->first();
        if ($is_available_on_date && $user_job_action) {
            switch (intval($job->job_status)) {
                case JobPost::JOB_STATUS_OPEN_WAITING:
                    switch (intval($user_job_action->action)) {
                        case JobAction::ACTION_NONE:
                            $jobManagementController->updateJobToaccepted($job, $user_job_action, $employer, $freelancer);
                            $success = "Job accepted successfully.";
                            break;
                        case JobAction::ACTION_APPLY:
                            $error = 'You have already apply for this job.';
                            break;
                        case JobAction::ACTION_ACCEPT:
                            $error = 'You have already accepted this job.';
                            break;
                        case JobAction::ACTION_DONE:
                            $error = 'This job is done.';
                            break;
                        default:
                            $error = 'Invalid action.';
                            break;
                    }
                    break;
                case JobPost::JOB_STATUS_CLOSE_EXPIRED:
                    $error = "Job is closed.";
                    break;
                case JobPost::JOB_STATUS_DISABLED:
                    $error = "Employer no longer needs a locum for this day and hence has removed the posting.";
                    break;
                case JobPost::JOB_STATUS_ACCEPTED:
                    if ($user_job_action->action == JobAction::ACTION_ACCEPT) {
                        $success = "You have already accepted this job.";
                    } else {
                        $error = "Sorry - this job is no longer available.";
                    }
                    break;
                case JobPost::JOB_STATUS_FREEZED:
                    if ($user_job_action->action == JobAction::ACTION_FREEZE && $user_job_action->freeze_notification_count == 1) {
                        $jobManagementController->updateJobToaccepted($job, $user_job_action, $employer, $freelancer);
                        $success = "Job accepted successfully.";
                    } else {
                        $error = "Thank you for your interest however this job is curently held by another locum - If it goes live again we shall notify you.";
                    }
                    break;
                case JobPost::JOB_STATUS_DELETED:
                    $error = "Employer no longer needs a locum for this day and hence has removed the posting.";
                    break;
                case JobPost::JOB_STATUS_CANCELED:
                    $error = "Employer no longer needs a locum for this day and hence has removed the posting.";
                    break;
                default:
                    $error = "Sorry - this job is no longer available.";
            }
        } else {
            $error = "Sorry - this job is no longer available.";
        }
        if ($success) {
            return response()->success([], $success);
        }
        return response()->error($error);
    }

    public function job_negotiate($freelancer_id, $job_id, $formdata)
    {
        $freelancer = User::find($freelancer_id);

        $job = JobPost::where("id", $job_id)->where("job_status", JobPost::JOB_STATUS_OPEN_WAITING)->whereDate("job_date", ">=", today())->whereHas("job_actions", function ($query) use ($freelancer) {
            $query->where("freelancer_id", $freelancer->id);
        })->first();
        if (is_null($job)) {
            return response()->error("This Job is not available");
        }

        $job_action_for_freelancer = JobAction::where("freelancer_id", $freelancer->id)->where("job_post_id", $job->id)->first();
        if ($job_action_for_freelancer->is_negotiated) {
            return response()->error("You already negotiated for this job");
        }

        $expected_rate = $formdata["expected_rate"];
        $freelancer_message = $formdata["employer_message"];
        $employer = $job->employer;

        $sent = Mail::to($employer->email)->send(new JobNegotiateMail($job, $freelancer, $employer, $expected_rate, $freelancer_message));
        if ($sent) {
            $job_action_for_freelancer->is_negotiated = true;
            $job_action_for_freelancer->negotiation_rate = $expected_rate;
            $job_action_for_freelancer->negotiation_message = $freelancer_message;
            $job_action_for_freelancer->save();
            $this->notifyController->notification($job->id, "Freelancer want to negotiate on job.", "Locumkit Job Negotiation", $employer->id, "negotiateJob");
            return response()->success([], "We notify the employer about your expected rate. If employer accept the offer we notify you.");
        }

        return response()->error("Some error occured during notification to employer. Please try again.");
    }

    public function job_freeze($freelancer_id, $job_id)
    {
        $job = JobPost::findOrFail($job_id);
        $job_invited_user = JobInvitedUser::where("job_post_id", $job_id)->where("invited_user_id", $freelancer_id)->where("invited_user_type", JobInvitedUser::USER_TYPE_LIVE)->first();
        $user_job_action = JobAction::where("freelancer_id", $freelancer_id)->where("job_post_id", $job_id)->first();

        if (is_null($job_invited_user) || is_null($user_job_action)) {
            return response()->error('You are not invited for this job.');
        }
        $action_status = response()->error('You are not invited for this job.');

        //freezing job
        $freelancer = User::findOrFail($freelancer_id);
        switch ($job->job_status) {
            case JobPost::JOB_STATUS_OPEN_WAITING:
                if ($user_job_action->freeze_notification_count < 1) {
                    if (today()->lessThan($job->job_date)) {
                        JobAction::where("job_post_id", $job->id)->where("id", "!=", $user_job_action->id)->update([
                            "action" => JobAction::ACTION_WAITING_FOR_UNFREEZE,
                            "updated_at" => now()
                        ]);
                        JobAction::where("id", $user_job_action->id)->update([
                            "action" => JobAction::ACTION_FREEZE,
                            "freeze_notification_count" => 1,
                            "updated_at" => now()
                        ]);
                        $job->job_status = JobPost::JOB_STATUS_FREEZED;
                        $job->save();
                        $action_status = response()->success([], 'Job freezed for 15 minutes.');
                    } else {
                        $action_status = response()->error('Job date is very close. You cannot freeze the job.');
                    }
                } else {
                    $action_status = response()->error('You already freezed this job once. You cannot freeze it again.');
                }
                break;
            case JobPost::JOB_STATUS_CLOSE_EXPIRED:
                $action_status = response()->error('Job already expired.');
                break;
            case JobPost::JOB_STATUS_DISABLED:
                $action_status = response()->error('Job already disabled.');
                break;
            case JobPost::JOB_STATUS_ACCEPTED:
                $action_status = response()->error('Job already accepted.');
                break;
            case JobPost::JOB_STATUS_FREEZED:
                if ($user_job_action->action == JobAction::ACTION_FREEZE && $user_job_action->freeze_notification_count >= 1) {
                    $action_status = response()->error('You already freezed the job and job is in freezed state now. You can accept it by clicking {Accept Job} button.');
                } else {
                    $action_status = response()->error('Job is freezed by some other freelancer. If it goes out we will notify you.');
                }
                break;
            case JobPost::JOB_STATUS_DELETED:
                $action_status = response()->error('Job already deleted by employer.');;
                break;
            case JobPost::JOB_STATUS_CANCELED:
                $action_status = response()->error('Job is no longer available.');;
                break;
            default:
                $action_status = response()->error('Job is no longer available.');;
        }

        return $action_status;
    }

    public function job_attendance($user_id, $user_role, $job_id, $attend, $job_type)
    {
        // $mailController = new JobMailHelper();
        $presentStatus = ($attend == 0) ? 'no' : 'yes';
        $check_job_status = response()->success([], 'Attendance is already done.');

        if ($user_role == 2) {
            if (is_numeric($job_id) && $job_id > 0 && $presentStatus == 'yes') {
                if ($job_type == 1) {
                    $jobOnDay = JobOnDay::where("job_post_id", $job_id)->where("freelancer_id", $user_id)->whereDate("job_date", now())->where("status", JobOnDay::STATUS_NOT_ATTEND)->first();
                    $job = JobPost::findOrFail($job_id);
                    if ($jobOnDay) {
                        $jobOnDay->status = JobOnDay::STATUS_EMPLOYER_VERIFIED_ATTENDANCE;
                        $jobOnDay->save();
                        $encrypted_job_id = encrypt($job_id);
                        $encrypted_employer_id = encrypt($jobOnDay->employer_id);
                        $encrypted_yes = encrypt("yes");
                        $encrypted_no = encrypt("no");
                        $job_type_encrypted = encrypt("website");

                        $yesBtnLinkHref = url("/attendance?job_id={$encrypted_job_id}&user_id={$encrypted_employer_id}&action={$encrypted_yes}&job_type={$job_type_encrypted}");
                        $noBtnLinkHref = url("/attendance?job_id={$encrypted_job_id}&user_id={$encrypted_employer_id}&action={$encrypted_no}&job_type={$job_type_encrypted}");

                        $btnLinks = '<a href="' . $yesBtnLinkHref . '" style="padding: 8px 30px; font-size: 16px; font-weight: 700; background: #00A9E0; color: #fff; ">Yes</a> <a href="' . $noBtnLinkHref . '" style="padding: 8px 30px; font-size: 16px; font-weight: 700; background: #ff0000; color: #fff; ">No</a> ';
                        $mailController->sendOnDayNotificationToEmployer($job, $jobOnDay->freelancer, $job->employer, $btnLinks);

                        $job = JobPost::where('id', $job_id)->first();
       
                        // Sending Notification
                        $notificationHelper = new AppNotificationHelper();
                        $job_id = ['job_id'=> $job_id, 'job_title' => $job->job_title ?? '' ,'employer_name' => $job->employer->firstname ?? '' . $job->employer->lastname ?? '' , 'job_rate' => $job->job_rate ?? '' , 'location' => $job->job_address ?? '', 'job_type' => $job->job_type ?? '' ];
                        $message = 'Job Attendance';
                        $title = 'Attendance';
                        $user_id = $user_id;
                        $types = 'privateJobAttendance';
                        $token_id = $this->getTokenByID($user_id); 
                        $notificationHelper->notification($job_id, $message, $title, $user_id, $types, $token_id);

                        FinanceIncome::create([
                            "job_id" => $job_id,
                            "job_type" => 1,
                            "freelancer_id" => $user_id,
                            "employer_id" => $jobOnDay->employer_id,
                            "job_rate" => $job->job_rate,
                            "job_date" => $job->job_date,
                            "income_type" => 1,
                            "is_bank_transaction_completed" => false,
                            "bank_transaction_date" => null,
                            "store" => $job->job_store->store_name,
                            "location" => $job->job_region,
                            "supplier" => $job->employer->first_name . ' ' . $job->employer->last_name,
                            "status" => 1,
                        ]);
                        
                        $check_job_status = response()->json([
                                'job_id' => $job_id,
                                'status' => 'success',
                                'message' => 'Attendance confirmed.'
                            ]);
                    } else {
                        $check_job_status = response()->error('Attendance is already done.');
                    }
                } elseif ($job_type == 2) {
                    $private_job = FreelancerPrivateJob::findOrFail($job_id);
                    if ($private_job->status == FreelancerPrivateJob::STATUS_NOTIFIED_ON_JOB_DAY) {
                        FreelancerPrivateFinance::create([
                            "freelancer_id" => $user_id,
                            "freelancer_private_job_id" => $job_id,
                            "job_rate" => $private_job->job_rate,
                            "job_date" => $private_job->job_date,
                            "employer_name" => $private_job->emp_name,
                        ]);
                        FinanceIncome::create([
                            "job_id" => $job_id,
                            "job_type" => 2,
                            "freelancer_id" => $user_id,
                            "employer_id" => null,
                            "job_rate" => $private_job->job_rate,
                            "job_date" => $private_job->job_date,
                            "income_type" => 1,
                            "is_bank_transaction_completed" => false,
                            "bank_transaction_date" => null,
                            "store" => $private_job->emp_name,
                            "location" => $private_job->job_location,
                            "supplier" => $private_job->emp_name,
                            "status" => 1,
                        ]);

                        $private_job->status = FreelancerPrivateJob::STATUS_JOB_ATTENDED;
                        $private_job->save();
                        $check_job_status = response()->success([], 'Attendance confirmed.');
                    } else {
                        $check_job_status = response()->error('Attendance already confirmed.');
                    }
                }
            } else {
                $check_job_status = response()->success([], 'Offfsss...! Please inform employer about the reason.');
            }
        } elseif ($user_role == 3) {
            if (is_numeric($job_id) && $job_id > 0 && $presentStatus == 'yes') {
                $job = JobPost::findOrFail($job_id);
                $job_on_day = JobOnDay::where("job_post_id", $job_id)->where("employer_id", $user_id)->whereDate("job_date", today())->where("status", JobOnDay::STATUS_FREELANCER_ATTEND)->first();
                if ($job_on_day) {
                    $job_on_day->status = JobOnDay::STATUS_EMPLOYER_VERIFIED_ATTENDANCE;
                    $job_on_day->save();
                    $check_job_status = response()->success([], 'Thanks...Have a nice time...');
                } else {
                    $check_job_status = response()->error('Offfsss...! Please ask locum about the reason.');
                }
            } else {
                $check_job_status = response()->error('Offfsss...! Please ask locum about the reason.');
            }
        }

        return $check_job_status;
    }

    public function job_expense($freelancer_id, $job_id, $job_type, $request, $data)
    {
        if ($request == 1) {
            return response()->success(ExpenseType::select("id", "expense as cat", "expense_colour")->get()->toArray());
        }

        if ($request == 2) {
            $freelancer = User::findOrFail($freelancer_id);
            $cats = $data['cats'];
            $cost = $data['cost'];
            if ($job_type == 2) {
                $job = FreelancerPrivateJob::findOrFail($job_id);
                $job_date = $job->job_date;
            } else {
                $job = JobPost::findOrFail($job_id);
                $job_date = $job->job_date;
            }
            $expense_array = [];
            foreach ($cats as $key => $cat) {
                $expense_array[] = [
                    "job_id" => $job->id,
                    "job_type" => $job_type,
                    "freelancer_id" => $freelancer->id,
                    "job_rate" => $cost[$key],
                    "job_date" => $job_date,
                    "expense_type_id" => $cat,
                    "description" => "",
                    "is_bank_transaction_completed" => 1,
                    "bank_transaction_date" => today(),
                    "created_at" => now(),
                    "updated_at" => now(),
                ];
            }
            if (sizeof($expense_array) > 0) {
                FinanceExpense::insert($expense_array);
            }
            // // dd('ere');
            // $notificationHelper = new AppNotificationHelper();
            // $job_id = $job_id;
            // $message = 'Job Expenses are Save Successfully';
            // $title = 'Job Expense';
            // $user_id = '35';
            // $types = 'Expense';
            // $token_id = 'token';
    
            // $notificationHelper->notification($job_id, $message, $title, $user_id, $types, $token_id);


            return response()->success([], 'You have successfully submited the expenses.');
        }
        return response()->error('Some error occured.');
    }
}
