<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\LocumlogbookFollowupProcedure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Matcher\Any;

class LocumlogbookFollowupProcedureController extends Controller
{
    private array $fields;
    private $model;
    private string $route;
    private string $page_title = 'FOLLOW UP PROCEDURES';
    private string $heading = 'Use this section to record any follow up procedures and set reminders';
    private string $add_heading = 'INTERNAL REFERRALS AND INVESITGATION REQUESTS';


    public function __construct()
    {
        $this->fields = [
            ["title" => "Practice Name", "name" => "practice_name", "type" => "text", "validation_rules" => "required|string|max:255|regex:/^[a-zA-Z]+$/"],
            ["title" => "Date", "name" => "date", "type" => "date", "validation_rules" => "required|date"],
            ["title" => "Patient ID", "name" => "patient_id", "type" => "number", "validation_rules" => "nullable|string|max:255"],
            ["title" => "Scenario", "name" => "issue_hand", "type" => "text", "validation_rules" => "required|string|max:255"],
            ["title" => "Action Required", "name" => "action_required", "type" => "text", "validation_rules" => "required|string|max:255"],
            ["title" => "Reminder Needed Date", "name" => "reminder_datetime", "type" => "datetime-local", "validation_rules" => "nullable|date"],
            ["title" => "Notes", "name" => "notes", "type" => "text", "validation_rules" => "required|string|max:255"],
            ["title" => "Completed", "name" => "is_compeleted", "type" => "checkbox", "validation_rules" => "nullable|in:on"],
        ];
        $this->model = LocumlogbookFollowupProcedure::class;

        $this->route = "freelancer.locumlogbook.follow-up-procedures";
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $records = $this->model::where("user_id", Auth::user()->id)->latest()->get();
        return view('freelancer.questionnaire_crud.index', ["fields" => $this->fields, 'records' => $records, 'route' => $this->route, 'page_title' => $this->page_title, 'heading' => $this->heading]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('freelancer.questionnaire_crud.create', ["fields" => $this->fields, 'route' => $this->route, 'add_heading' => $this->add_heading]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            "practice_name" => ["required", "max:255", "regex:/^[a-zA-Z]+$/"],
            "issue_hand" => ["required", "max:255"],
            "action_required" => ["required", "max:255"],
            "notes" => ["required", "max:255"],
        ]);
        $rules = array_map(function ($field) {
            return [$field['name'] => isset($field['validation_rules']) ? $field['validation_rules'] : ''];
        }, $this->fields);

        $request->validate($rules);

        $record = new $this->model;
        $record->user_id = $request->user()->id;

        $this->save_record($request, $record);
        session()->flash('success', 'Record has been successfully added.');

        return redirect(route("{$this->route}.index"));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return redirect(route($this->route . '.index'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $record = $this->model::where("user_id", Auth::user()->id)->where("id", $id)->first();
        if (!$record) {
            return abort(404);
        }

        return view('freelancer.questionnaire_crud.edit', ["fields" => $this->fields, 'record' => $record, 'route' => $this->route, 'add_heading' => $this->add_heading]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = array_map(function ($field) {
            return [$field['name'] => isset($field['validation_rules']) ? $field['validation_rules'] : ''];
        }, $this->fields);

        $request->validate($rules);

        $record = $this->model::where("user_id", Auth::user()->id)->where("id", $id)->first();
        if (!$record) {
            return abort(404);
        }

        $this->save_record($request, $record);
        session()->flash('success', 'Record has been Updated.');

        return redirect(route("{$this->route}.index"));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $record = $this->model::where("user_id", Auth::user()->id)->where("id", $id)->first();
        if (!$record) {
            return abort(404);
        }
        $record->delete();

        return redirect(route("{$this->route}.index"))->with("success", "Data is Deleted Successfully.");
    }

    private function save_record(Request $request, Model &$record)
    {
        foreach ($this->fields as $field) {
            if (isset($field['type']) && $field['type'] == 'checkbox') {
                $record->{$field['name']} = $request->input($field['name']) == 'on' ? true : false;
            } else {
                $record->{$field['name']} = $request->input($field['name']);
            }
        }
        $record->save();
    }
}