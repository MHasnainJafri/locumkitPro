<?php

namespace App\Http\Controllers\Freelancer;

use App\Http\Controllers\Controller;
use App\Models\LocumlogbookFollowupProcedure;
use App\Models\LocumlogbookReferralPathways;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Mockery\Matcher\Any;

class LocumlogbookReferralPathwayController extends Controller
{
    private array $fields;
    private $model;
    private string $route;
    private string $page_title = 'REFERRAL PATHWAYS';
    private string $heading = 'Use this section to record any refrences and set reminders';
    private string $add_heading = 'LOCAL AREA PROTOCOLS';


    public function __construct()
    {
        $this->fields = [
            ["title" => "Area", "name" => "area", "placeholder" => "Please enter area name", "type" => "text", "validation_rules" => "required|string|max:255"],
            ["title" => "Pathway for", "name" => "extended_services", "placeholder" => "ie cataracts referrals", "type" => "text", "validation_rules" => "required|string|max:255"],
            ["title" => "Pathway Protocol", "name" => "emergency_department", "placeholder" => "ie direct fax to HES", "type" => "text", "validation_rules" => "required|string|max:255"],
            ["title" => "Contact infor", "name" => "routine_referrals", "type" => "text", "placeholder" => "ie Tel/fax no or email to send referral to", "validation_rules" => "required|string|max:255"],
        ];
        $this->model = LocumlogbookReferralPathways::class;

        $this->route = "freelancer.locumlogbook.referral-pathways";
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
       $rules = [];

foreach ($this->fields as $field) {
    $rules[$field['name']] = $field['validation_rules'] ?? '';
}
 
// Override the 'routine_referrals' field with the custom email or phone rule
$rules['routine_referrals'] = [
    'required',
    'string',
    'max:255',
    'regex:/^(\+?[0-9]{7,15}|[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/'
];

// Optional: see the rule structure
// dd($rules);

// Run the validation
$request->validate($rules,[    'routine_referrals.regex' => 'Please enter a valid phone number or email address.']);

        $record = new $this->model;
        $record->user_id = $request->user()->id;

        $this->save_record($request, $record);

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
 
        $rules['routine_referrals'] = [
    'required',
    'string',
    'max:255',
    'regex:/^(\+?[0-9]{7,15}|[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/'
];

        $request->validate($rules);

        $record = $this->model::where("user_id", Auth::user()->id)->where("id", $id)->first();
        if (!$record) {
            return abort(404);
        }

        $this->save_record($request, $record);

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
        session()->flash('success', 'Record has been deleted successfully.');

        return redirect(route("{$this->route}.index"));
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