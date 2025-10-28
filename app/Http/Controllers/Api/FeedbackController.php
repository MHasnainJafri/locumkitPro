<?php

namespace App\Http\Controllers\Api;

use App\Helpers\JobMailHelper;
use App\Helpers\AppNotificationHelper;
use App\Models\MobileNotification;
use App\Http\Controllers\Controller;
use App\Http\Resources\JobPostExtendedResource;
use App\Models\FeedbackQuestion;
use App\Models\JobFeedback;
use App\Models\JobPost;
use App\Models\User;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function getFeedbackSummary(Request $request)
    {
        $uid = $request->input('user_id');
        $user_role = $request->input('user_role');
        $feedbackSummaryData = '';
        $feedbackSummary = '';

        if ($user_role == 2) {
            $currentFeedbackData = JobFeedback::with('employer')->where("freelancer_id", $uid)->where("user_type", JobFeedback::FEEDBACK_BY_EMPLOYER)->where("status", 1)->whereDate("created_at", ">=", today()->subMonths(120)->startOfMonth())->get();
        } else {
            $currentFeedbackData = JobFeedback::with('freelancer')->where("employer_id", $uid)->where("user_type", JobFeedback::FEEDBACK_BY_FREELANCER)->where("status", 1)->whereDate("created_at", ">=", today()->subMonths(120)->startOfMonth())->get();
        }

        if (sizeof($currentFeedbackData) <= 0) {
            return response()->error('Not found');
        }
        $feedbackSummaryData = array();
        $qusdata = $qus = $quscount =  array();
        foreach ($currentFeedbackData as $currentFeedback) {
            foreach (json_decode($currentFeedback['feedback'], true) as $feedback) {
                $queid = $feedback['qusId'];
                $qusdata[$queid] = isset($qusdata[$queid]) ? $qusdata[$queid] + $feedback['qusRate'] : $feedback['qusRate'];
                $quscount[$queid] = isset($quscount[$queid]) ? $quscount[$queid] + 1 : 1;
                $qus[$queid] = $feedback['qus'];
            }
        }
        $i = 1;
        $c = count($qusdata);
        if ($c >= 4) {
            $j = 4;
        } elseif ($c == 2) {
            $j = 1;
        } else {
            $j = $c;
        }
        $qus_ave_rate = array();
        $qus_ave = array();
        $qus_ave_rate_background = array();
        $qus_ave_rate_border = array();
        $feedbackSummary = array();
        foreach ($qusdata as $key => $qusdata) {
            $qus_ave[] =  "Q" . $i;
            $qus_ave_rate[] =  round(($qusdata / ($quscount[$key] * 5)) * 100, 2);
            $qus_ave_rate_background[] =  'rgba(8, 169, 226, 0.9)';
            $qus_ave_rate_border[] = 'rgb(8, 169, 226, 0.9)';
            $feedbackSummary[] = array(
                'qus' => "Q" . $i . " : " . $qus[$key],
                'qusRate' => round(($qusdata / ($quscount[$key] * 5)) * 100, 2),
                'dataX' => "Q" . $i,
                'dataY' => round(($qusdata / ($quscount[$key] * 5)) * 100, 2),
                'j' => $j
            );
            $i++;
        }
        $feedbackSummaryData['feedback'] = $feedbackSummary;
        $feedbackSummaryData['graph_chart']['qus_label'] = $qus_ave;
        $feedbackSummaryData['graph_chart']['qus_ave_rate'] = $qus_ave_rate;
        $feedbackSummaryData['graph_chart']['qus_ave_rate_background'] = $qus_ave_rate_background;
        $feedbackSummaryData['graph_chart']['qus_ave_rate_border'] = $qus_ave_rate_border;
        return response()->success($feedbackSummaryData);
    }
    public function getTokenByID($user_id)
    {
        $tokenID = MobileNotification::where("user_id", $user_id)->latest()->first();
        if ($tokenID) {
            return $tokenID->token_id;
        }
        return null;
    }

    public function userFeedbackAction(Request $request)
    {
        $user_id        = $request['user_id'];
        $user_role      = $request['user_role'];
        $page_id        = $request['page_id'];
        $job_id         = $request['job_id'];
        $user_profession = $request['user_profession'];
        $feedback_response = response()->error('Invalid request');
        switch ($page_id) {
            case 'form-info':
                $feedback_response = $this->get_feedback_form($user_id, $user_role, $user_profession, $job_id);
                $notificationHelper = new AppNotificationHelper();
                $job_id = $job_id;
                $message = 'Give Your Feedback';
                $title = 'Job Feedback';
                $user_id = $user_id;
                $types = 'feedbackRequest';
                $token_id = $this->getTokenByID($user_id);
        
                $notificationHelper->notification($job_id, $message, $title, $user_id, $types, $token_id);
                break;
            case 'save-feedback':
                $feedback_data = isset($request['data']) ? $request['data'] : null;
                $feedback_response = $this->save_feedback($user_id, $user_role, $user_profession, $job_id, $feedback_data);
                break;
            case 'feedback-list':
                $feedback_response = $this->feedback_list($user_id, $user_role);
                break;
        }
        return $feedback_response;
    }

    public function get_feedback_form($user_id, $user_role, $user_profession, $job_id)
    {
        if ($user_role == 2) {
            $job_info = JobPost::findOrFail($job_id);
            $employer = $job_info->employer;
            $empName    = $employer->firstname . ' ' . $employer->lastname;
            $empEmail   = $employer->email;
    
            $job_info['job_rate'] = set_amount_format($job_info['job_rate']);
            $allFeedbackQusArray = FeedbackQuestion::where("question_cat_id", $user_profession)->where("question_status", 1)->where("question_freelancer", "!=", "")->orderBy("question_sort_order")->get();
            $feedbackQusArray     = array();
            foreach ($allFeedbackQusArray as $feedbackQus) {
                $feedbackQusArray[] = array(
                    'qus_id' => $feedbackQus->id,
                    'qus'    => $feedbackQus->question_freelancer
                );
            }

            $feedback_form_info = array(
                'u_id'      => $job_info->employer_id,
                'types'   => 'feedbackRequest',
                'u_name'    => $empName,
                'u_email'   => $empEmail,
                'job_date'  => get_date_with_default_format($job_info['job_date']),
                'job_rate'  => $job_info['job_rate'],
                'feed_qus'  => $feedbackQusArray
            );

            return response()->success($feedback_form_info);
        }
        if ($user_role == 3) {
            $job_info = JobPost::findOrFail($job_id);
            $freelancer_id = $job_info->getAcceptedFreelancerData()["id"];
            $freelancer = User::findOrFail($freelancer_id);
            $freName = $freelancer->firstname . ' ' . $freelancer->lastname;
            $freEmail = $freelancer->email;

            $job_info['job_rate'] = set_amount_format($job_info['job_rate']);
            $allFeedbackQusArray = FeedbackQuestion::where("question_cat_id", $user_profession)->where("question_status", 1)->where("question_employer", "!=", "")->orderBy("question_sort_order")->get();

            foreach ($allFeedbackQusArray as $feedbackQus) {
                $feedbackQusArray[] = array(
                    'qus_id' => $feedbackQus->id,
                    'qus'    => $feedbackQus->question_employer
                );
            }

            $feedback_form_info = array(
                'u_id'      => $freelancer_id,
                'u_name'    => $freName,
                'u_email'   => $freEmail,
                'job_date'  => $job_info['job_date'],
                'job_rate'  => $job_info['job_rate'],
                'feed_qus'  => $feedbackQusArray
            );
            return response()->success($feedback_form_info);
        }

        return response()->error('Invalid request');
    }

    public function save_feedback($user_id, $user_role, $user_profession, $job_id, $feedback_data)
    {
        $mailController = new JobMailHelper();
        $feedback_status = response()->error('Something is wrong, please restart app and try again');

        if (!empty($feedback_data['feedback'])) {
            $feedback           = $feedback_data['feedback'];
            $rating             = 0;
            $feedbackQusId      = array();
            $feedbackAns        = array();
            $feedbackQusArray  = array();
            $feedbackQus        = $feedback_data['feedbackQus'];
            $i = 0;
            foreach ($feedback as $key => $feedback_rate) {
                $rating             += $feedback_rate;
                $feedbackQusId[]    = $key;
                $feedbackQusArray[] = $feedbackQus[$i]['qus'];
                $feedbackAns[]      = $feedback_rate;
                $i++;
            }
            $rating = $rating / $i;
            $feedback_user_id   = $user_id;
            $feedback_job_id    = $job_id;
            $feedback_comment   = "";
            $user_role          = $feedback_data['user_role'];
            $user_cat           = $feedback_data['user_cat'];

            $feedback_to_user_id = $feedback_data['feedback_to_user_id'];

            /* merge rate vale with qus ids */
            $feedbackArray = array();
            foreach ($feedbackQus as $key => $feedbackQus) {

                $feedbackArray[] = array(
                    'qusId'     => $feedbackQusId[$key],
                    'qus'       => $feedbackQusArray[$key],
                    'qusRate'   => $feedbackAns[$key]
                );
            }
            $feedback = json_encode($feedbackArray);
            $feedback_user_role = $user_role == 2 ? JobFeedback::FEEDBACK_BY_FREELANCER : JobFeedback::FEEDBACK_BY_EMPLOYER;
            $checkFeedbackData = JobFeedback::where("job_id", $feedback_job_id)->where($feedback_user_role . "_id", $user_id)->count();


            if ($checkFeedbackData > 0) {
                $feedback_status = response()->error('You have already submitted feedback');
            } else {
                if ($user_role == 2) {
                    $job_feedback = JobFeedback::create([
                        "employer_id" => $feedback_to_user_id,
                        "freelancer_id" => $feedback_user_id,
                        "job_id" => $feedback_job_id,
                        "rating" => $rating,
                        "feedback" => $feedback,
                        "comments" => $feedback_comment,
                        "user_type" => $feedback_user_role,
                        "cat_id" => $user_cat,
                    ]);
                    $mailController->recievedFeedbackEmployerNotification($job_feedback, $job_feedback->job, $job_feedback->freelancer, $job_feedback->employer);
                } elseif ($user_role == 3) {

                    #Employer feedback
                    $job_feedback = JobFeedback::create([
                        "employer_id" => $feedback_user_id,
                        "freelancer_id" => $feedback_to_user_id,
                        "job_id" => $feedback_job_id,
                        "rating" => $rating,
                        "feedback" => $feedback,
                        "comments" => $feedback_comment,
                        "user_type" => $feedback_user_role,
                        "cat_id" => $user_cat,
                    ]);
                    $mailController->recievedFeedbackFreelancerNotification($job_feedback, $job_feedback->job, $job_feedback->freelancer);
                }

                $feedback_status = response()->success([], 'Feedback submitted successfully');
            }
        }
        return $feedback_status;
    }

    public function feedback_list($user_id, $user_role)
    {
        $userId = $user_id;
        $userRoleId = $user_role;
        $userInfo = User::findOrFail($userId);
        $currentFeedbackData = [];
        if ($userRoleId == 2) {
            $currentFeedbackData = JobFeedback::with('employer')->where("freelancer_id", $user_id)->where("user_type", JobFeedback::FEEDBACK_BY_EMPLOYER)
                ->where("status", 1)->whereDate("created_at", ">=", today()->subMonths(120)->startOfMonth())->get();
        } elseif ($userRoleId == 3) {
            $currentFeedbackData = JobFeedback::with('freelancer')->where("employer_id", $user_id)->where("user_type", JobFeedback::FEEDBACK_BY_FREELANCER)
                ->where("status", 1)->whereDate("created_at", ">=", today()->subMonths(120)->startOfMonth())->get();
        }
        $perRating_new = get_overall_feedback_rating($currentFeedbackData);
        $totalFeedback  = count($currentFeedbackData);
        $perRating_star = round(($perRating_new / 100) * 5, 1);

        foreach ($currentFeedbackData as $key => $feedbackData) {
            $qus_data = json_decode($feedbackData['feedback'], true);
            $job_info = JobPost::findOrFail($feedbackData->job_id);
            if ($user_role == 2) {
                $userDataObj = $feedbackData->employer;
            } elseif ($user_role == 3) {
                $userDataObj = $feedbackData->freelancer;
            }
            $userName = $userDataObj->firstname . ' ' . $userDataObj->lastname;

            $currentFeedbackData[$key]['feedback']  = $qus_data;
            $currentFeedbackData[$key]['j_rate']    = set_amount_format($job_info['job_rate']);
            $currentFeedbackData[$key]['j_date']    = get_date_with_default_format($job_info['job_date']);
            $currentFeedbackData[$key]['user_name'] = $userName;
            $currentFeedbackData[$key]['created_date'] = get_date_with_default_format($feedbackData->created_at);
        }

        $feedback_list_array = array(
            'feedback_data' => $currentFeedbackData,
            'total_count'   => $totalFeedback,
            'average_rating' => $perRating_new,
            'average_rating_star' => $perRating_star
        );
        return response()->success($feedback_list_array);
    }

    public function getFeedbackById(Request $request)
    {
        $feedback_id    = $request['feedback_id'];
        $user        = $request->user();
        $feedback = JobFeedback::findOrFail($feedback_id);
        $feedback['feedback'] = json_decode($feedback['feedback'], true);

        if ($user->user_acl_role_id == User::USER_ROLE_LOCUM) {
            $userDataObj   = $feedback->employer;
        } elseif ($user->user_acl_role_id == User::USER_ROLE_EMPLOYER) {
            $userDataObj   = $feedback->freelancer;
        }

        $userName = $userDataObj->firstname . ' ' . $userDataObj->lastname;
        $userEmail = $userDataObj->email;

        $job_post = $feedback->job;
        $job_post->job_rate = set_amount_format($job_post->job_rate);

        $feedback['job_info'] = (new JobPostExtendedResource($job_post))->jsonSerialize();
        $feedback['created_date'] = date('d/m/Y', strtotime($feedback['created_at']));
        $feedback['opposition_name'] = $userName;
        $feedback['opposition_email'] = $userEmail;

        return response()->success($feedback->toArray());
    }
}
