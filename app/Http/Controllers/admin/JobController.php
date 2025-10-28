<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\JobPost;
use Illuminate\Http\Request;

class JobController extends Controller
{




    // SELECT freelancer_id as id FROM job_actions WHERE job_post_id = '$jid' AND (action = '3' OR action = '4' OR action = '6' OR action = '7')  ORDER BY updated_at DESC



    public function index(){
        $jobs=JobPost::where('job_status','!=',JobPost::JOB_STATUS_DELETED)->latest()->get();
        return view('admin.job.index',compact('jobs'));
    }
}
