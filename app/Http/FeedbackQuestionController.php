<?php

namespace App\Http\Controllers\admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\FeedbackQuestion;
use App\Models\UserAclProfession;
use App\Http\Controllers\Controller;

class FeedbackQuestionController extends Controller
{

    public $role, $profession, $professionslist;

    public function __construct(Request $request)
    {
        $this->role = $request->input('q', 'Locum');
        $this->profession = $request->input('c', null);
        $this->professionslist = UserAclProfession::where('is_active', 0)->get();
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $usersQuery = FeedbackQuestion::query();
        if ($this->profession != null) {
            $usersQuery->where('question_cat_id', $this->profession);
        }

        $allfeedback =  $usersQuery->get();
        $categories = UserAclProfession::all();
        return view('admin.feedbackQuestion.index', compact('allfeedback', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = UserAclProfession::all();
        return view('admin.feedbackQuestion.create', compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => 'required',
            'status' => 'required',
        ]);

        $UserAcl = new FeedbackQuestion();
        $UserAcl->question_freelancer = $request->input('fre_question');
        $UserAcl->question_employer = $request->input('emp_question');
        $UserAcl->question_cat_id = $data['category'];
        $UserAcl->question_status = $data['status'];
        $UserAcl->question_sort_order = $request->input('sort_order');
        $UserAcl->save();

        return redirect()->route('admin.feedbackquestion.index');
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
        $feedback = FeedbackQuestion::find($id);

        $categories = UserAclProfession::all();

        return view('admin.feedbackquestion.edit', compact('feedback','categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // return $id;
        $request->validate([
            'question_freelancer' => 'required',
            'question_employer' => 'required',
        ]);

        $data = [
            'question_freelancer' => $request->input('question_freelancer'),
            'question_employer' => $request->input('question_employer'),
            'question_cat_id' => $request->input('category'),
            'question_status' => $request->input('question_status'),
            'question_sort_order' => $request->input('sort_order'),
        ];
        $fQuestion = FeedbackQuestion::find($id);
        $fQuestion->update($data);
        return redirect()->route('admin.feedbackquestion.index')->with('success', 'Question updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $fQuestion = FeedbackQuestion::find($id);
        $fQuestion->delete();
        return redirect()->route('admin.feedbackquestion.index')->with('success', 'Question deleted successfully');
    }
}
