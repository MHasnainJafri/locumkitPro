<?php

namespace App\Http\Controllers\admin;

use App\Models\JobFeedback;
use Illuminate\Http\Request;
use App\Models\UserAclProfession;
use App\Http\Controllers\Controller;
use App\Models\FeedbackQuestion;

class FeedbackController extends Controller
{
    public $role,$profession,$professionslist;

    public function __construct(Request $request)
    {
        $this->role = $request->input('q','Locum');
        $this->profession = $request->input('c',null);
        $this->professionslist=UserAclProfession::where('is_active',1)->get();
    }

    public function index()
    {
        $allfeedback=JobFeedback::latest()->get();
      
        return view('admin.feedback.index',compact('allfeedback'));
    }

    public function create(){
        $categories = UserAclProfession::all();
        return view('admin.feedback.create', compact('categories'));
    }

    public function store(Request $request){
        
    }

    public function FeedbackEdit($id)
    {
        $feedback=JobFeedback::find($id);
        return view('admin.feedback.edit',compact('feedback'));

    }
    // public function FeedbackUpdate(Request $request,$id)
    // {
    //     // $feedback = JobFeedback::find($id);

    //     // if ($feedback) {
    //     //     // The feedback record exists, so you can update its properties
    //     //     $feedback->employer_id = $request->feedbackFrom;
    //     //     $feedback->freelancer_id = $request->feedbackTo;
    //     //     $feedback->save();
    //     // } else {
    //     //     // Handle the case where the feedback record does not exist
    //     //     // You can return a response, redirect, or perform any other desired action
    //     //     return redirect()->back()->with('error', 'Feedback not found.');
    //     // }
    // }

    public function FeedbackUpdate(Request $request,$id)
   {

    $feedback = JobFeedback::find($id);
    if (!$feedback) {
        return back()->with('error', 'Feedback not found.');
    }

    $feedback->status = $request->input('status');
    $feedback->save();

    return redirect()->route('admin.feedback.index');
    return back()->with('message', 'Feedback status updated successfully.');
   }

   public function feedbackDel($id)
   {
    $del=JobFeedback::find($id);
    $del->delete();

    return redirect()->back()->with('message','deleted successfully');



   }
}
