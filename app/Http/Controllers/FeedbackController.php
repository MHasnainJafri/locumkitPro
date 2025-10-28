<?php

namespace App\Http\Controllers;

use App\Helpers\JobMailHelper;
use App\Models\JobFeedback;
use App\Models\JobFeedbackDispute;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function feedbackDispute(Request $request)
    {
        $mailController = new JobMailHelper();
        try {
            $feedback_id = decrypt($request->query("feedback_id"));
            $user_id = decrypt($request->query("user_id"));
            $user_type = decrypt($request->query("user_type"));
        } catch (DecryptException $e) {
            return abort(404);
        }
        if (!in_array($user_type, [JobFeedback::FEEDBACK_BY_FREELANCER, JobFeedback::FEEDBACK_BY_EMPLOYER])) {
            return abort(404);
        }
        $feedback = JobFeedback::findOrFail($feedback_id);
        $current_user_type = Auth::user()->user_acl_role_id == 2 ? JobFeedbackDispute::FEEDBACK_DISPUTE_BY_FREELANCER :  JobFeedbackDispute::FEEDBACK_DISPUTE_BY_FREELANCER;

        if ($request->isMethod("GET")) {
            if ($feedback->freelancer_id == $user_id) {
                $feedback_from = $feedback->employer->firstname . " " . $feedback->employer->lastname;
            } else {
                $feedback_from = $feedback->freelancer->firstname . " " . $feedback->freelancer->lastname;
            }
            return view('shared.feedback-dispute', compact("feedback_from", "feedback"));
        } else if ($request->isMethod("POST")) {

            $dispute_comment     = $request->input("dispute-comment");
            $count = JobFeedbackDispute::where("feedback_id", $feedback_id)->where("user_type", $current_user_type)->count();
            if ($count > 0) {
                return back()->with("error", "Feedback dispute already submitted by you");
            }
            JobFeedbackDispute::create([
                "feedback_id" => $feedback->id,
                "user_type" => $current_user_type,
                "comment" => $dispute_comment,
                "status" => 0
            ]);
            $feedback->status = 2;
            $feedback->save();
            $mailController->sendDisputeSubmitNotification($feedback_id, $feedback->freelancer, $feedback->employer, $feedback->job_id, $current_user_type);
            return back()->with("success", "Feedback dispute send successfully");
        }
    }
}
