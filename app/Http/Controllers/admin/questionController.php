<?php

namespace App\Http\Controllers\admin;

use App\Models\UserQuestion;
use Illuminate\Http\Request;
use App\Models\UserAclProfession;
use App\Http\Controllers\Controller;
use Database\Seeders\UserQuestionSeeder;

class questionController extends Controller
{
    public $role,$profession,$professionslist;

    public function __construct(Request $request)
    {
        $this->role = $request->input('q','Locum');
        $this->profession = $request->input('c',null);
        $this->professionslist=UserAclProfession::where('is_active',0)->get();
    }
    // public function index()
    // {

    //   $UserQuestion=UserQuestion::latest()->get();
    //    return view('admin.questions.index',compact('UserQuestion'));

    // }


    public function index(Request $request)
    {
        $role = $this->role;
        $data = UserQuestion::all();
        // dd($role,$data);
        if($request->q || $request-> c)
        {
            if($request->q == "Locum" && $request->c)
            {
                $UserQuestion=UserQuestion::where('user_acl_profession_id' , $request->c)->orderBy('sort_order')->get();
                return view('admin.questions.index', compact('UserQuestion','data','role'));
            }
            else if($request->q == "Employer" && $request->c)
            {
                $UserQuestion=UserQuestion::where('user_acl_profession_id' , $request->c)->get();
                return view('admin.questions.index', compact('UserQuestion','data','role'));
            }
            
            if($request->q == "Locum" || $request->q == "Employer")
            {
                $UserQuestion=UserQuestion::orderBy('sort_order')->get();
                return view('admin.questions.index', compact('UserQuestion','data','role'));
            }
        }
        // $userQuestionsQuery = UserQuestion::query();
        $UserQuestion=UserQuestion::orderBy('sort_order')->get();
    
        $data = UserQuestion::orderBy('sort_order')->get();
        $role=$this->role;
    
        // Paginate the results
        // $userQuestions = $userQuestionsQuery->paginate(10); // Adjust the number of items per page as needed
        //dd($UserQuestion);
        return view('admin.questions.index', compact('UserQuestion','data','role'));
    }




    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $get_category = UserAclProfession::where('is_active', 1)->get();

        return view('admin.questions.create', compact('get_category'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        
        $request->validate([
    'freelancer_question' => [
        'required',
        'string',
        'min:15',
        'max:300',
        'regex:/^[A-Za-z0-9 ,.\'"!?()\-\s]+$/',
        function ($attribute, $value, $fail) {
            if (trim($value) === '') {
                $fail('The freelancer question cannot be empty or only whitespace.');
            }
        },
    ],
    'employer_question' => [
        'required',
        'string',
        'min:15',
        'max:300',
        'regex:/^[A-Za-z0-9 ,.\'"!?()\-\s]+$/',
        function ($attribute, $value, $fail) {
            if (trim($value) === '') {
                $fail('The employer question cannot be empty or only whitespace.');
            }
        },
    ],
], [
    'freelancer_question.required' => 'Freelancer question is required.',
    'freelancer_question.min' => 'Freelancer question must be at least 15 characters.',
    'freelancer_question.max' => 'Freelancer question must not exceed 300 characters.',
    'freelancer_question.regex' => 'Freelancer question contains invalid characters.',

    'employer_question.required' => 'Employer question is required.',
    'employer_question.min' => 'Employer question must be at least 15 characters.',
    'employer_question.max' => 'Employer question must not exceed 300 characters.',
    'employer_question.regex' => 'Employer question contains invalid characters.',
]);


        if($request -> type == 1){

            $newquestion = new UserQuestion;
            $newquestion -> user_acl_profession_id = $request -> category;
            $newquestion -> employer_question = $request -> employer_question;
            $newquestion -> freelancer_question = $request -> freelancer_question;
            $newquestion -> type = $request -> type;
            $newquestion -> values = '[""]';
            $newquestion -> sort_order = $request -> sort_order??0;
            $newquestion -> is_required = $request -> is_required;
            $newquestion -> is_active = $request -> is_activated;
            $newquestion -> range_type_unit = null;
            $newquestion -> range_type_condition = null;

            $newquestion -> save();
        }
        else if($request -> type == 2 || $request -> type == 3){
            $arr_value = [];
            foreach ($request->values as $key => $value) {
                if ($value != null) {
                    array_push($arr_value, $value);
                }
            }
            $arr_value = json_encode($arr_value);
            $newquestion = new UserQuestion;
            $newquestion -> user_acl_profession_id = $request -> category;
            $newquestion -> employer_question = $request -> employer_question;
            $newquestion -> freelancer_question = $request -> freelancer_question;
            $newquestion -> type = $request -> type;
            $newquestion -> values =  $arr_value;
            $newquestion -> sort_order = $request -> sort_order??0;
            $newquestion -> is_required = $request -> is_required;
            $newquestion -> is_active = $request -> is_activated;
            $newquestion -> range_type_unit = null;
            $newquestion -> range_type_condition = null;
            $newquestion -> save();
        }
        else if($request -> type == 4){
            $arr_value = [];
            foreach ($request->values as $key => $value) {
                if ($value != null) {
                    array_push($arr_value, $value);
                }
            }
            $arr_value = json_encode($arr_value);
            $newquestion = new UserQuestion;
            $newquestion -> user_acl_profession_id = $request -> category;
            $newquestion -> employer_question = $request -> employer_question;
            $newquestion -> freelancer_question = $request -> freelancer_question;
            $newquestion -> type = $request -> type;
            $newquestion -> values =  $arr_value;
            $newquestion -> sort_order = $request -> sort_order??0;
            $newquestion -> is_required = $request -> is_required;
            $newquestion -> is_active = $request -> is_activated;
            $newquestion -> range_type_unit = $request -> range_type_unit;
            $newquestion -> range_type_condition = $request -> range_type_condition;
            $newquestion -> save();
            
        }
        else if($request -> type == 5){
            $arr_value = [];
            foreach ($request->values as $key => $value) {
                if($key >= 8){
                    if ($value != null) {
                        array_push($arr_value, $value);
                    }
                }
            }
            $arr_value = json_encode($arr_value);
            $newquestion = new UserQuestion;
            $newquestion -> user_acl_profession_id = $request -> category;
            $newquestion -> employer_question = $request -> employer_question;
            $newquestion -> freelancer_question = $request -> freelancer_question;
            $newquestion -> type = $request -> type;
            $newquestion -> values =  $arr_value;
            $newquestion -> sort_order = $request -> sort_order??0;
            $newquestion -> is_required = $request -> is_required;
            $newquestion -> is_active = $request -> is_activated;
            $newquestion -> range_type_unit = $request -> range_type_unit;
            $newquestion -> range_type_condition = $request -> range_type_condition;
            $newquestion -> save();
        }
        else if($request -> type == 6){
            
            $newquestion = new UserQuestion;
            $newquestion -> user_acl_profession_id = $request -> category;
            $newquestion -> employer_question = $request -> employer_question;
            $newquestion -> freelancer_question = $request -> freelancer_question;
            $newquestion -> type = $request -> type;
            $newquestion -> values = '["Yes","No"]';
            $newquestion -> sort_order = $request -> sort_order??0;
            $newquestion -> is_required = $request -> is_required;
            $newquestion -> is_active = $request -> is_activated;
            $newquestion -> range_type_unit = null;
            $newquestion -> range_type_condition = null;

            $newquestion -> save();
        }
            
        if($request -> submit == 'Save' ){
            return redirect()->route('viewQuestionindex')->with("success", "Question have been Added.");
        }
        else if($request -> submit == 'Save & add new'){
            return redirect()->route('admin.question.create')->with("success","Question have been Added.");
        }
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
     public function edit(int $id)
    {
        $data = UserQuestion::findOrFail($id);
        $userAclProfessions = UserAclProfession::where('is_active','1')->get();
        $values = json_decode($data->values);
        // dd($values);
        return view('admin.questions.edit', compact('data', 'userAclProfessions','values'));
    }


    /**
     * Update the specified resource in storage. 
     */
     public function update(Request $request, string $id)
    {
        $request->validate([
    'freelancer_question' => [
        'required',
        'string',
        'min:15',
        'max:300',
        'regex:/^[A-Za-z0-9 ,.\'"!?()\-\s]+$/',
        function ($attribute, $value, $fail) {
            if (trim($value) === '') {
                $fail('The freelancer question cannot be empty or only whitespace.');
            }
        },
    ],
    'employer_question' => [
        'required',
        'string',
        'min:15',
        'max:300',
        'regex:/^[A-Za-z0-9 ,.\'"!?()\-\s]+$/',
        function ($attribute, $value, $fail) {
            if (trim($value) === '') {
                $fail('The employer question cannot be empty or only whitespace.');
            }
        },
    ],
], [
    'freelancer_question.required' => 'Freelancer question is required.',
    'freelancer_question.min' => 'Freelancer question must be at least 15 characters.',
    'freelancer_question.max' => 'Freelancer question must not exceed 300 characters.',
    'freelancer_question.regex' => 'Freelancer question contains invalid characters.',

    'employer_question.required' => 'Employer question is required.',
    'employer_question.min' => 'Employer question must be at least 15 characters.',
    'employer_question.max' => 'Employer question must not exceed 300 characters.',
    'employer_question.regex' => 'Employer question contains invalid characters.',
]);

    
        $newquestion = UserQuestion::find($id); // Use find() instead of first() after where()

        if (!$newquestion) {
            return redirect()->route('viewQuestionindex')->with("Error", "Question not found.");
        }

        $newquestion->user_acl_profession_id = $request->user_acl_profession_id;
        $newquestion->employer_question = $request->employer_question;
        $newquestion->freelancer_question = $request->freelancer_question;
        $newquestion->sort_order = $request->sort_order;
        $newquestion->is_required = $request->is_required;
        $newquestion->is_active = $request->is_activated;
        $newquestion->range_type_unit = $request->range_type_unit;
        $newquestion->range_type_condition = $request->range_type_condition;

        if ($request->type == 1) {
            $newquestion->type = $request->type;
            $newquestion->values = '[""]';
        } elseif ($request->type == 2 || $request->type == 3 || $request->type == 4 || $request->type == 5) {
            $arr_value = [];
            foreach ($request->values as $value) {
                if ($value !== null) {
                    $arr_value[] = $value;
                }
            }

            if($request->type == 5){
                $arr_value = array_slice($arr_value,8);
                $newquestion->user_acl_profession_id = $request->category;
            }
            $newquestion->type = $request->type;
            $newquestion->values = json_encode($arr_value);
        } elseif ($request->type == 6) {
            $newquestion->type = $request->type;
            $newquestion->values = '["Yes","No"]';
            $newquestion->user_acl_profession_id = $request->category;
        }

        $newquestion->save();

        if ($request->submit == 'Save') {
            return redirect()->route('viewQuestionindex')->with("success", "Question have been Updated.");
        } elseif ($request->submit == 'Save & add new') {
            return redirect()->route('admin.question.create')->with("success", "Question have been Updated.");
        }
    }
    // public function update(Request $request, string $id)
    // {
    //     $newquestion = UserQuestion::where('id', $id)->delete(); 

    //     if($request -> type == 1){

    //         $newquestion -> user_acl_profession_id = $request -> user_acl_profession_id;
    //         $newquestion -> employer_question = $request -> employer_question;
    //         $newquestion -> freelancer_question = $request -> freelancer_question;
    //         $newquestion -> type = $request -> type;
    //         $newquestion -> values = '[""]';
    //         $newquestion -> sort_order = $request -> sort_order; 
    //         $newquestion -> is_required = $request -> is_required;
    //         $newquestion -> is_active = $request -> is_activated;
    //         $newquestion -> range_type_unit = null;
    //         $newquestion -> range_type_condition = null;

    //         $newquestion -> save();
    //     }
    //     else if($request -> type == 2 || $request -> type == 3){
    //         $arr_value = [];
    //         foreach ($request->values as $key => $value) {
    //             if ($value != null) {
    //                 array_push($arr_value, $value);
    //             }
    //         }
    //         $arr_value = json_encode($arr_value);
    //         $newquestion = new UserQuestion;
    //         $newquestion -> user_acl_profession_id = $request -> user_acl_profession_id;
    //         $newquestion -> employer_question = $request -> employer_question;
    //         $newquestion -> freelancer_question = $request -> freelancer_question;
    //         $newquestion -> type = $request -> type;
    //         $newquestion -> values =  $arr_value;
    //         $newquestion -> sort_order = $request -> sort_order;
    //         $newquestion -> is_required = $request -> is_required;
    //         $newquestion -> is_active = $request -> is_activated;
    //         $newquestion -> range_type_unit = null;
    //         $newquestion -> range_type_condition = null;
    //         $newquestion -> save();
    //     }
    //     else if($request -> type == 4){
    //         $arr_value = [];
    //         foreach ($request->values as $key => $value) {
    //             if ($value != null) {
    //                 array_push($arr_value, $value);
    //             }
    //         }
    //         $arr_value = json_encode($arr_value);
    //         $newquestion -> user_acl_profession_id = $request -> user_acl_profession_id;
    //         $newquestion -> employer_question = $request -> employer_question;
    //         $newquestion -> freelancer_question = $request -> freelancer_question;
    //         $newquestion -> type = $request -> type;
    //         $newquestion -> values =  $arr_value;
    //         $newquestion -> sort_order = $request -> sort_order;
    //         $newquestion -> is_required = $request -> is_required;
    //         $newquestion -> is_active = $request -> is_activated;
    //         $newquestion -> range_type_unit = $request -> range_type_unit;
    //         $newquestion -> range_type_condition = $request -> range_type_condition;
    //         $newquestion -> save();
            
    //     }
    //     else if($request -> type == 5){
    //         // dd($request->all());
    //         $arr_value = [];
    //         foreach ($request->values as $key => $value) {
    //             if($key >= 8){
    //                 if ($value != null) {
    //                     array_push($arr_value, $value);
    //                 }
    //             }
    //         }
    //         $arr_value = json_encode($arr_value);
    //         $newquestion -> user_acl_profession_id = $request -> category;
    //         $newquestion -> employer_question = $request -> employer_question;
    //         $newquestion -> freelancer_question = $request -> freelancer_question;
    //         $newquestion -> type = $request -> type;
    //         $newquestion -> values =  $arr_value;
    //         $newquestion -> sort_order = $request -> sort_order;
    //         $newquestion -> is_required = $request -> is_required;
    //         $newquestion -> is_active = $request -> is_activated;
    //         $newquestion -> range_type_unit = $request -> range_type_unit;
    //         $newquestion -> range_type_condition = $request -> range_type_condition;
    //         $newquestion -> save();
    //     }
    //     else if($request -> type == 6){
            
    //         $newquestion -> user_acl_profession_id = $request -> category;
    //         $newquestion -> employer_question = $request -> employer_question;
    //         $newquestion -> freelancer_question = $request -> freelancer_question;
    //         $newquestion -> type = $request -> type;
    //         $newquestion -> values = '["Yes","No"]';
    //         $newquestion -> sort_order = $request -> sort_order;
    //         $newquestion -> is_required = $request -> is_required;
    //         $newquestion -> is_active = $request -> is_activated;
    //         $newquestion -> range_type_unit = null;
    //         $newquestion -> range_type_condition = null;

    //         $newquestion -> save();
    //     }
            
    //     if($request -> submit == 'Save' ){
    //         return redirect()->route('viewQuestionindex')->with("Success", "Question have been Added.");
    //     }
    //     else if($request -> submit == 'Save & add new'){
    //         return redirect()->route('admin.question.create')->with("Success","Question have been Added.");
    //     }
    // }
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        UserQuestion::where('id', $id)->delete();
        return  redirect()->back()->with("success", "Successfully Deleted");
    }
}
