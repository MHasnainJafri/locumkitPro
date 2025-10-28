<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MobileNotification;
use App\Helpers\AppNotificationHelper;
use App\Http\Resources\FreelancerPrivateJobExtendedResource;
use App\Models\FinanceIncome;
use App\Models\FreelancerPrivateFinance;
use App\Models\FreelancerPrivateJob;
use App\Models\User;
use App\Models\JobPost;
use Illuminate\Http\Request;

class PrivateJobController extends Controller
{
    public function manage_private_job(Request $request)
    {
        $user_data = $request->all();
        $uid = isset($user_data['uid']) ? $user_data['uid'] : '';
        if (!$uid) {
            $uid = $request->input("job_info.uid");
        }
        $results = array();
        $user = User::find($uid);
        if ($user_data['type'] == 'addNew') {
            $results['getdata'] = $this->insert_private_job($user_data);
        }
        if ($user_data['type'] == 'edit') {
            $results['getdata'] = $this->update_private_job($user_data);
        }
        if ($user_data['type'] == 'delete') {
            $results['getdata'] = $this->delete_private_job($user_data);
        }
        if ($user_data['type'] == 'get') {
            $results['getdata'] = $this->get_private_job($user_data);
        }
        $private_jobs = FreelancerPrivateJob::where("freelancer_id", $uid)->orderBy("job_date")->get();

        $results['results'] = FreelancerPrivateJobExtendedResource::collection($private_jobs)->jsonSerialize();
        $results['results_check_previliage'] = $user && can_user_package_has_privilege($user, 'add_private_job') ? 1 : 0;

        return response()->json($results);
    }
    public function insert_private_job($job_data)
    {
        $uid = isset($job_data['job_info']['uid']) ? $job_data['job_info']['uid'] : '';
        $name = isset($job_data['job_info']['name']) ? $job_data['job_info']['name'] : '';
        $rate = isset($job_data['job_info']['rate']) ? $job_data['job_info']['rate'] : '';
        $title = isset($job_data['job_info']['title']) ? $job_data['job_info']['title'] : '';
        $location = isset($job_data['job_info']['location']) ? $job_data['job_info']['location'] : '';
        $date = isset($job_data['job_info']['date']) ? $job_data['job_info']['date'] : '';
        $start_date_new = date('Y-m-d', strtotime($date));
        $private_job  = new FreelancerPrivateJob();
        $private_job->freelancer_id = $uid;
        $private_job->emp_name = $name;
        $private_job->job_title = $title;
        $private_job->job_rate = $rate;
        $private_job->job_location = $location;
        $private_job->job_date = $start_date_new;
        $private_job->save();
        return $job_data;
    }

    public function update_private_job($job_data)
    {
        $pid = isset($job_data['job_info']['pid']) ? $job_data['job_info']['pid'] : '';
        $name = isset($job_data['job_info']['name']) ? $job_data['job_info']['name'] : '';
        $rate = isset($job_data['job_info']['rate']) ? $job_data['job_info']['rate'] : '';
        $title = isset($job_data['job_info']['title']) ? $job_data['job_info']['title'] : '';
        $location = isset($job_data['job_info']['location']) ? $job_data['job_info']['location'] : '';
        $date = isset($job_data['job_info']['date']) ? $job_data['job_info']['date'] : '';
        $start_date_new = date('Y-m-d', strtotime($date));

        $private_job = FreelancerPrivateJob::findOrFail($pid);
        $private_job->emp_name = $name;
        $private_job->job_title = $title;
        $private_job->job_rate = $rate;
        $private_job->job_location = $location;
        $private_job->job_date = $start_date_new;
        $private_job->save();

        return $private_job->toArray();
    }

    public function delete_private_job($job_data)
    {
        $pid = isset($job_data['pid']) ? $job_data['pid'] : '';
        FreelancerPrivateFinance::where("freelancer_private_job_id", $pid)->delete();
        $job_obj = FreelancerPrivateJob::where("id", $pid)->delete();
        return $job_obj;
    }

    public function get_private_job($job_data)
    {
        $pid = isset($job_data['pid']) ? $job_data['pid'] : '';
        $private_job = FreelancerPrivateJob::findOrFail($pid);
        $resource = (new FreelancerPrivateJobExtendedResource($private_job))->jsonSerialize();
        $resource["priv_job_start_date"] = $private_job->job_date->format("Y-m-d");
        return $resource;
    }

    public function view_private_job(Request $request)
    {
        $f_id = $request->input('user_id');
        $pj_id = $request->input('job_id');

        $private_job = FreelancerPrivateJob::where("id", $pj_id)->where("freelancer_id", $f_id)->first();
        if ($private_job) {
            $private_job->job_rate = set_amount_format($private_job->job_rate);
            return response()->success((new FreelancerPrivateJobExtendedResource($private_job))->jsonSerialize());
        }
        return response()->error("Not found");
    }
    
    public function getTokenByID($user_id)
    {
        $tokenID = MobileNotification::where("user_id", $user_id)->latest()->first();
        if ($tokenID) {
            return $tokenID->token_id;
        }
        return null;
    }

    public function attend_private_job(Request $request)
    {
        $f_id = $request->input('user_id');
        $pj_id = $request->input('job_id');

        $job = JobPost::where('id', $pj_id)->first();
        $employer = User::where('id', $job->employer_id)->first();

        // Sending Notification
        $notificationHelper = new AppNotificationHelper();
        $job_id = $pj_id;
        $message = 'Private Job Attendance';
        $title = 'Attendance';
        $user_id = $f_id;
        
        $job_title = $job->job_title ?? '';
        $employer_name = $employer->firstname ?? '' . ' ' . $employer->lastname ?? '';
        $job_rate = $job->job_rate ?? '';
        $location = $job->job_address ?? '';
        $job_type = $job->job_type ?? '';
        
        
        
        // $user_id['user_id'] = $f_id;
        // $user_id['job_id'] = $pj_id;
        // $user_id['job_title'] = $job->job_title ?? '';
        // $user_id['employer_name'] = $employer->firstname ?? '' . $employer->lastname ?? '';
        // $user_id['job_rate'] = $job->job_rate ?? '';
        // $user_id['location'] = $job->job_address ?? '' ;
        // $user_id['job_type'] = $job->job_type;
        
        
        
        $types = 'privateJobAttendance';
        $token_id = $this->getTokenByID($f_id); 
    
        $notificationHelper->Privatenotification($job_id, $message, $title, $user_id, $types, $token_id, $job_title, $employer_name, $job_rate, $location, $job_type);

            
        $private_job = FreelancerPrivateJob::where("id", $pj_id)->where("freelancer_id", $f_id)->where("status", FreelancerPrivateJob::STATUS_NOTIFIED_ON_JOB_DAY)->first();
        $response = [
            'response' => 'Job not found'
        ];
        if ($private_job) {
            $private_job->status = FreelancerPrivateJob::STATUS_JOB_ATTENDED;
            $private_job->save();

            FreelancerPrivateFinance::create([
                "freelancer_id" => $f_id,
                "freelancer_private_job_id" => $pj_id,
                "job_rate" => $private_job->job_rate,
                "job_date" => $private_job->job_date,
                "employer_name" => $private_job->emp_name,
            ]);
            FinanceIncome::create([
                "job_id" => $pj_id,
                "job_type" => 2,
                "freelancer_id" => $f_id,
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
            $response['response'] = "Attendance confirmed.";
        } else {
            $response['response'] = "Attendance is already done.";
        }
        $job = JobPost::where('id', $job_id)->first();
        $response['job'] = $job->employer;
        $response['employer'] = $job->employer;
        return response()->json($response);
    }
}
