<?php

namespace App\Http\Controllers\admin;

use App\Models\JobFeedback;
use Illuminate\Http\Request;
use App\Models\UserAclProfession;
use App\Http\Controllers\Controller;
use App\Models\JobFeedbackDispute;
use App\Models\UserFeedback;

class UserFeedbackController extends Controller
{

    public $role, $profession, $professionslist;

    public function __construct(Request $request)
    {
        $this->role = $request->input('q', 'freelancer');
        $this->profession = $request->input('c', null);
        $this->professionslist = UserAclProfession::where('is_active', 1)->get();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $jobFeedbackQuery = JobFeedback::query();
        if ($this->profession != null) {
            $jobFeedbackQuery->where('cat_id', $this->profession);
        }
        if ($this->role != null) {
            $jobFeedbackQuery->where('user_type', $this->role);
        }

        $userFeedbacks =  $jobFeedbackQuery->with('employer', 'freelancer')->get();
        // return $userFeedbacks;

        return view('admin.userfeedback.index', compact('userFeedbacks'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $userFeedback = JobFeedback::with('employer', 'freelancer')->find($id);
        $userFeedback = JobFeedbackDispute::where('id', $id)->get()->first();
        return view('admin.userfeedback.edit', compact('userFeedback'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = [
            'status' => $request->input('status'),
        ];

        $jobFeedback = JobFeedback::findOrFail($id);
        if ($jobFeedback) {
            $jobFeedback->update($data);
        }
        
        
        return redirect()->route('admin.userfeedback.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $jobFeedback = JobFeedback::findOrFail($id);
        $jobFeedback->delete();
        return redirect()->route('admin.userfeedback.index');
    }

    public function disputeFeedback()
    { 



        $jobFeedbackDisputeQuery = JobFeedbackDispute::get();

        if ($this->role != null) {
            $jobFeedbackDisputeQuery->where('user_type', $this->role);
        }

        $jobFeedbackDispute = $jobFeedbackDisputeQuery;
        return view('admin.userfeedback.disputfeedback', compact('jobFeedbackDispute'));
    }
    public function disputeFeedbackEdit($id)
    {
        return view('admin.userfeedback.disputfeedbackedit');
    }
}