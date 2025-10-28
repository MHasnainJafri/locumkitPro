<?php

namespace App\Http\Controllers;

use App\Helpers\DistanceCalculateHelper;
use App\Models\BlockUser;
use App\Models\FreelancerPrivateJob;
use App\Models\JobAction;
use App\Models\JobFeedback;
use App\Models\JobPost;
use App\Models\SiteTown;
use App\Models\User;
use App\Models\UserAclPackage;
use App\Models\UserAclPackageResource;
use App\Models\UserQuestion;
use App\Models\UsersWorkCalender;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AjaxController extends Controller
{
    public function registrationInfoCheck(Request $request)
    {
        if ($request->has('check_type') && $request->input('check_type')) {
            $checkType = $request->input('check_type');
            if ($checkType == "user_email") {
                $email = $request->input("email");
                $present_email_count = User::where("email", $email)->count();
                return new JsonResponse(['email_exists' => $present_email_count > 0]);
            }
            if ($checkType == "login_check") {
                $login = $request->input("login");
                $present_login_count = User::where("login", $login)->count();
                return new JsonResponse(['login_exists' => $present_login_count > 0]);
            }
        }
        return new JsonResponse(['success' => false, 'message' => 'Not found'], 404);
    }

    public function questionByRole(Request $request)
    {
        $profession_id = $request->input('cat_id');
        $role_id = $request->input('role_id');

        $rows = UserQuestion::where("user_acl_profession_id", $profession_id)->get();
        $total_questions = sizeof($rows);
        $html = "<input type='hidden' id='total_qus' value='{$total_questions}'>";

        $i = 1;
        $tip = "";
        foreach ($rows as $row) {
            $id_name = "ro{$role_id}_cat{$profession_id}";
            $questionsarr = "";
            $hidden_field = "<input type='hidden' id='question_id_{$i}' name='question_id[]' value='{$row['id']}'>";

            if ($role_id == 2) {
                $question_string = $row["freelancer_question"];
            } else {
                $question_string = $row["employer_question"];
            }
            $qus_required = 0;
            $req_class = "";
            $req_atribute = "";
            if ($row['is_required']) {
                $qus_required = 1;
                $req_class = "req-qus-{$row->id}";
                $req_atribute = "required";
            }

            $ans_method = "";
            if ($row['type'] == 1) {
                $ans_method .= "<input type='text' id='ans_text_{$i}' name='ans_val_for_question_id_{$row->id}' class='width-100 {$req_class}'>";
                if ($qus_required == 1) {
                    $ans_method .= "<div id='required-qus-{$row->id}' style='clear: both;color:red;'></div>";
                }
            }

            if ($row['type'] == 2) {
                $ans_method .= "<select name='ans_val_for_question_id_{$row->id}' id='ans_option_{$i}' class='width-100 form-control {$req_class}' {$req_atribute}><option value=''>Please select</option>";
                $available_values = json_decode($row['values']);
                foreach ($available_values as $value) {
                    $ans_method .= "<option value='{$value}' >{$value}</option>";
                }
                $ans_method .= "</select>";
                if ($qus_required == 1) {
                    $ans_method .= "<div id='required-qus-{$row->id}' style='clear: both;color:red;'></div>";
                }
            }

            if ($row['type'] == 3) {
                $ans_method .= "<div class='multi_select' style='max-height: 300px;overflow: scroll;'>";
                $available_values = json_decode($row['values']);
                foreach ($available_values as $value) {
                    $checked = in_array($row->id, [23, 24, 31]) && in_array($value, ["English", "Basic Eye Test", "Basic IT usage"]) ? "checked" : "";
                    $ans_method .= "<div style='float:left;width:50%;'>
                                        <input type='checkbox' name='ans_val_for_question_id_{$row->id}[]' class='{$req_class}' value='{$value}' data-question-id={$row->id} {$req_atribute} {$checked}/>
                                        <span class='margin-left'> {$value} </span>
                                    </div>";
                }
                $ans_method .= "</div>";
                if ($qus_required == 1) {
                    $ans_method .= "<div id='required-qus-{$row->id}' style='clear: both;color:red;'></div>";
                }
            }
            if ($row['type'] == 4) {
                $range_type_unit = $row['range_type_unit'];
                $range_type_condition = $row['range_type_condition'];
                $condition_arr = array("1" => "Greater than", "2" => "Greater than OR equal to", "3" => "Less than", "4" => "Less than OR equal", "5" => "Equal to");
                $range_val = $condition_arr[$range_type_condition];

                $ans_method .= "<select name='ans_val_for_question_id_{$row->id}' id='ans_option_{$i}' class='width-100 form-control {$req_class}' {$req_atribute} ><option value=''>Please select</option>";

                $available_values = json_decode($row['values']);
                foreach ($available_values as $value) {
                    $ans_method .= "<option value='{$value}' > {$range_val} {$value} {$range_type_unit} </option>";
                }
                $ans_method .= "</select>";
                if ($qus_required == 1) {
                    $ans_method .= "<div id='required-qus-{$row->id}' style='clear: both;color:red;'></div>";
                }
            }
            if ($row['type'] == 5) {
                $range_type_unit = $row['range_type_unit'];
                $range_type_condition = $row['range_type_condition'];
                $condition_arr = array("1" => "Greater than", "2" => "Greater than OR equal to", "3" => "Less than", "4" => "Less than OR equal", "5" => "Equal to");
                $range_val = $condition_arr[$range_type_condition];

                $ans_method .= "<select name='ans_val_for_question_id_{$row->id}' id='ans_option_{$i}' class='width-100 form-control {$req_class}' {$req_atribute} ><option value=''>Please select</option>";

                $available_values = json_decode($row['values']);
                foreach ($available_values as $value) {
                    $ans_method .= "<option value='{$value}' > {$value} {$range_type_unit} </option>";
                }
                $ans_method .= "</select>";
                if ($qus_required == 1) {
                    $ans_method .= "<div id='required-qus-{$row->id}' style='clear: both;color:red;'></div>";
                }
            }

            if ($row['type'] == 6) {
                if ($qus_required && $qus_required == 1) {
                    $ans_method .= "<select name='ans_val_for_question_id_{$row->id}' id='ans_option_{$i}' required class='width-100 form-control'><option value=''>Please select</option>";
                    if ($role_id == 3) {
                        $ans_method .= "<option value='Yes'>Yes</option>";
                        $ans_method .= "<option value='Yes'>No</option>";
                    } else {
                        $ans_method .= "<option value='Yes'>Yes</option>";
                        $ans_method .= "<option value='No'>No</option>";
                    }
                    $ans_method .= "</select>";
                    $ans_method .= "<div id='required-qus-" . $row['id'] . "'></div>";
                } else {
                    $ans_method = "<select name='ans_val_for_question_id_{$row->id}' id='ans_option_{$i}' class='width-100 form-control'><option value=''>Please select</option>";
                    if ($role_id == 3) {
                        $ans_method .= "<option value='Yes'>Yes</option>";
                        $ans_method .= "<option value='No'>No</option>";
                    } else {
                        $ans_method .= "<option value='Yes'>Yes</option>";
                        $ans_method .= "<option value='No'>No</option>";
                    }
                    $ans_method .= "</select>";
                }
            }

            if ($role_id == 2) {
                if ($qus_required && $qus_required == 1) {
                    $questionsarr = "<div id='" . $id_name . "' class='col-md-11' >
                            <div class='col-md-6 margin-bottom text-right'><p>" . $question_string . "<i class='fa fa-asterisk required-stars' aria-hidden='true'  ></i>" . $tip . "</p></div>
                            <div class='col-md-6 margin-bottom'>" . $ans_method . "</div></div>";
                } else {
                    $questionsarr = "<div id='" . $id_name . "' class='col-md-11' >
                            <div class='col-md-6 margin-bottom text-right'><p>" . $question_string . $tip . "</p></div>
                            <div class='col-md-6 margin-bottom'>" . $ans_method . "</div></div>";
                }
            } else {
                if ($qus_required && $qus_required == 1) {
                    $questionsarr = "<div id='" . $id_name . "' class='col-md-11' >
                            <div class='col-md-6 margin-bottom text-right'><p>" . $question_string . "<i class='fa fa-asterisk required-stars' aria-hidden='true' ></i>" . $tip . "</p></div>
                            <div class='col-md-6 margin-bottom'>" . $ans_method . "</div></div>";
                } else {
                    $questionsarr = "<div id='" . $id_name . "' class='col-md-11' >
                            <div class='col-md-6 margin-bottom text-right'><p>" . $question_string . $tip . "</p></div>
                            <div class='col-md-6 margin-bottom'>" . $ans_method . "</div></div>";
                }
            }

            $html .= $questionsarr . $hidden_field;

            $i++;
        }

        return new JsonResponse(['html' => $html]);
    }

    public function saveQuestionByRole(Request $request)
    {
        return new JsonResponse($request->all());
        $ans_val = $request->input("ans_val");
        $question_id = $request->input("question_id");
        $newData = array();
        if ($ans_val && is_array($ans_val) && $question_id && is_array($question_id)) {
            foreach ($question_id as $qKey => $qValue) {
                foreach ($ans_val as $akey => $aValue) {
                    if ($qKey == $akey) {
                        $newData[$qValue] = $aValue;
                    }
                }
            }
            if (!empty($newData)) {
                $checkVal = 1;
                $html = "";
                foreach ($newData as $key => $value) {
                    if ($value) {
                        $checkVal = 1;
                    } else {
                        $checkVal = 1;
                        //break;
                    }
                }
                foreach ($newData as $key => $value) {
                    if ($checkVal) {
                        $html = "<input type='hidden' name='ans_text[]' value='{$value}'>
					        <input type='hidden' name='ques_id[]' value='{$key}'>";
                    }
                }
                return new JsonResponse(['html' => $html]);
            }
        }

        return new JsonResponse(['html' => ""]);
    }

    public function multiStoreTime()
    {

        $arr_week = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');
        $timeToSelect = '0:00';
        $arr_start_hours = '';
        $arr_end_hours = '';
        $arr_lunch = '';

        for ($th = 0; $th < 96; $th++) {
            $hrs = date('H:i', strtotime($timeToSelect));
            $endTime = strtotime('+15 minutes', strtotime($hrs));
            $timeToSelect = date('H:i', $endTime);
            //if($th<=9){$th='0'.$th;}
            if ($timeToSelect == '09:00') {
                $defaultSatrtTime = 'selected';
            } else {
                $defaultSatrtTime = '';
            }
            if ($timeToSelect == '17:30') {
                $defaultEndTime = 'selected';
            } else {
                $defaultEndTime = '';
            }
            $arr_start_hours .= "<option value='{$timeToSelect}' {$defaultSatrtTime}> {$timeToSelect} </option>";
            $arr_end_hours .= "<option value='{$timeToSelect}' {$defaultEndTime}> {$timeToSelect} </option>";
        }

        for ($tl = 0; $tl <= 12; $tl++) {
            if ($tl == 0) {
                $lunch = '00';
            } else {
                $lunch = $tl * 5;
            }
            $arr_lunch .= "<option value='{$lunch}'> {$lunch} </option>";
        }

        $forInnerHtml = "";
        $unique_val = uniqid() . time();
        foreach ($arr_week as $weekdays) {
            $forInnerHtml .= "
                    <div class='col-md-12'>
                        <div class='col-xs-3 col-sm-3 col-md-3'>
                        </div>
                        <div class='col-xs-3 col-sm-3 col-md-3'>
                            <select name='job_start_time_{$unique_val}[{$weekdays}]' class='input-text width-100 '>{$arr_start_hours}</select>
                        </div>
                        <div class='col-xs-3 col-sm-3 col-md-3' align='center'>
                            <select name='job_end_time_{$unique_val}[{$weekdays}]' class='input-text width-100 '>{$arr_end_hours}</select>
                        </div>
                        <div class='col-xs-3 col-sm-3 col-md-3'>
                            <select name='job_lunch_time_{$unique_val}[{$weekdays}]' class='input-text width-100'>{$arr_lunch}</select>
                        </div>
                    </div>";
        }

        $html = "<div class='col-md-11 oppning-time-append-div'>
                    <input type='hidden' name='total_emp_stores[]' value='{$unique_val}' />
                    <div class='col-md-4 text-right'><p>What is your opening time(s)?</p></div>
                        <div class='col-md-8'>
                            <div class='col-md-12'>
                                <div class='col-xs-3 col-sm-3 col-md-3'></div>
                                <div class='col-xs-3 col-sm-3 col-md-3'>Start Time</div>
                                <div class='col-xs-3 col-sm-3 col-md-3' align='center'>End Time</div>
                                <div class='col-xs-3 col-sm-3 col-md-3 '>Lunch break (min)</div>
                            </div>
                            {$forInnerHtml}
                        </div>
                    </div>
                </div>";
        return new JsonResponse(["html" => $html, "key" => $unique_val]);
    }

    public function openBenefitsForm(Request $request)
    {
        $resource_html = "";
        $package_id = $request->input("pack_id");
        $resource_package = UserAclPackage::findOrFail($package_id);
        $resource_package_name = strtolower($resource_package->name);
        $res_pack_ids = json_decode($resource_package->user_acl_package_resources_ids_list);
        if ($res_pack_ids && is_array($res_pack_ids) && sizeof($res_pack_ids) > 0) {
            $package_resources = UserAclPackageResource::whereIn("id", $res_pack_ids)->get();
            foreach ($package_resources as $res) {
                $resource_html .= "<i class='fa fa-check-square-o margin-right square-font'></i> {$res->resource_value} <br>";
            }
        }
        $html = "<h2>Benefits</h2><p id='{$resource_package_name}-benifits'> {$resource_html} </p>";

        return new JsonResponse(["html" => $html]);
    }

    public function getTownList(Request $request)
    {
        $distanceHelper = new DistanceCalculateHelper();
        $zipCode = str_replace(' ', '', $request->input('zip'));
        $address = str_replace(' ', '+', $request->input('full_addr'));
        $maxDistance = $request->input('max_dis') === 'Over 50' ? 6371 : intval($request->input('max_dis')); // earth's mean radius, km
        $lat = $distanceHelper->getLatitude($zipCode, $address); // latitude of center of bounding circle in degrees
        $lon = $distanceHelper->getLongitude($zipCode, $address); // longitude of center of bounding circle in degrees

        $maxLat = $lat + rad2deg($maxDistance / 6371);
        $minLat = $lat - rad2deg($maxDistance / 6371);
        $maxLon = $lon + rad2deg($maxDistance / 6371 / cos(deg2rad($lat)));
        $minLon = $lon - rad2deg($maxDistance / 6371 / cos(deg2rad($lat)));

        $results = SiteTown::select('id', 'town')
            ->selectRaw('(ST_Distance_Sphere(point(lon, lat), point(?, ?)) / 1609.34) as distance', [$lon, $lat])
            ->whereBetween('lat', [$minLat, $maxLat])
            ->whereBetween('lon', [$minLon, $maxLon])
            ->orderBy('distance')
            ->get();

        $resultsCount = $results->count();

        $html = '<div class="col-md-12">';
        if ($resultsCount > 0) {
            $html .= '<div class="store_list margin-top"><p class="col-md-8 list_count">' . $resultsCount . ' record(s) found.</p><p class="col-md-4 list_count_save_btn"><input type="button" class="btn btn-small btn-warning" name="save_list_data" value="Save & Continue" onClick="save_list();"></p></div>';
            foreach ($results as $key => $value) {
                $html .= '<div class="store_list"><div class="col-md-1 no-padding-left"><span class="margin-right"><input type="checkbox" class="st_data margin-right" name="store_list[]" value="' . $value->id . '" checked></span></div><div class="col-md-11 no-padding-right">
                <p><span class="town-name text-right">' . $value->town . '</span><span class="distance text-right"> ' . number_format((float)$value->distance, 2, '.', '') . ' Miles</span></p></div></div>';
            }
        } else {
            $html .= '<div class="store_list"><strong>No record found. Please check the post code Or try with higher range.</strong></div>';
        }
        $html .= '</div>';

        return new JsonResponse(["html" => $html]);
    }

    public function prepareTownListToSave(Request $request)
    {
        $values = array();
        $values_store_id = array();
        $values_store_data = array();
        $store_id_list = "";
        $store_data_list = "";
        parse_str($request->input('data_all'), $values);
        $var_list = implode(',', $values['store_list']); //Outputs 'store'
        if ($request->input('store_id')) {
            parse_str($request->input('store_id'), $values_store_id);
            if ($values_store_id && isset($values_store_id['store_id'])) {
                $store_id_list = implode(',', $values_store_id['store_id']); //Outputs 'store_id'
            }
        }
        if ($request->input('store_data')) {
            parse_str($request->input('store_data'), $values_store_data);
            if ($values_store_data && isset($values_store_data['store_data'])) {
                $store_data_list = implode(',', $values_store_data['store_data']); //Outputs 'store_data'
            }
        }
        $var = "";
        $data_all = $request->input('data_all');
        $store_id = $store_id_list;
        $max_distance = $request->input('max_distance');
        $store_data = $store_data_list;
        $store_list = $var_list;
        $strArray = explode("&", $request->input('data_all'));
        $goc = $request->input('goc');
        $aop = $request->input('aop');
        $inshurance_company = $request->input('inshurance_company');
        $inshurance_no = $request->input('inshurance_no');
        $inshurance_renewal_date = $request->input('inshurance_renewal_date');
        if (!empty($data_all) || $max_distance) {
            $var = '<input type="hidden" name="max_distance" value="' . $max_distance . '">
                    <input type="hidden" name="store_id" value="' . $store_id . '">
                    <input type="hidden" name="store_data" value="' . $store_data . '">
                    <input type="hidden" name="store_list" value="' . $store_list . '">
            ';
            return new JsonResponse(["html" => $var, "status" => 1]);
        } else {
            return new JsonResponse(["status" => 0]);
        }
    }

    public function getJobInfo(Request $request)
    {
        $job_id = $request->input("job_no");
        $job_type = $request->input("job_type");

        if (is_null($job_id) || $job_id == "" || $job_id <= 0) {
            return new JsonResponse();
        }

        if ($job_type == '1') {
            $job = JobPost::where("id", $job_id)->whereIn("job_status", [JobPost::JOB_STATUS_ACCEPTED, JobPost::JOB_STATUS_DONE_COMPLETED])
                ->whereHas("job_actions", function ($query) use ($request) {
                    $query->where("action", [JobAction::ACTION_ACCEPT, JobAction::ACTION_DONE])->where("freelancer_id", $request->user()->id);
                })->first();
            if ($job) {
                $job_data = [
                    'emp_id' => $job->employer_id,
                    'rate' => $job->job_rate,
                    'store_nm' => $job->job_store->store_name,
                    'location' => $job->job_address,
                    'supplier' => $job->employer->firstname . ' ' . $job->employer->lastname,
                    'job_date' => $job->job_date,
                ];
                return new JsonResponse($job_data);
            }
        } else if ($job_type == '2') {
            $job = FreelancerPrivateJob::where("id", $job_id)->where("freelancer_id", $request->user()->id)->first();
            if ($job) {
                $job_data = [
                    'emp_id' => '',
                    'rate' => $job->job_rate,
                    'store_nm' => $job->emp_name,
                    'location' => $job->job_location,
                    'supplier' => $job->emp_name,
                    'job_date' => $job->job_date,
                ];
                return new JsonResponse($job_data);
            }
        }

        return new JsonResponse();
    }

    public function getInvoiceTemplate(Request $request)
    {

        $validator = Validator::make($request->all(), [
            "job_id" => ["required"],
            "job_date" => ["required", "date"],
            "job_rate" => ["required"],
            'your_email' => ['required', 'email'],
            'your_name' => ['required', 'string'],
            'your_address' => ['required', 'string'],
            'your_contact' => ['required', 'string'],
            'supplier_store' => ['required', 'string'],
            'supplier_name' => ['required', 'string'],
            'supplier_email' => ['required', 'string'],
            'supplier_address' => ['required', 'string'],
            'supplier_town' => ['required', 'string'],
            'supplier_country' => ['required', 'string'],
            'supplier_postcode' => ['required', 'string'],
            'acc_name' => ["required", "string", "max:255"],
            'acc_number' => ["required", "numeric"],
            'acc_sort_code' => ["required", "max:8"],
            'template-choice' => ['required', 'in:invoice1,invoice2'],
        ]);

        if ($validator->fails()) {
            return new JsonResponse(["success" => false, "message" => "Please fill all fields", "messages-bags" => $validator->getMessageBag()->all()]);
        }
        $data = $request->all(["job_id", "job_date", "job_rate", "your_email", "your_name", "your_address", "your_contact", "supplier_store", "supplier_id", "supplier_name", "supplier_email", "supplier_address", "supplier_town", "supplier_country", "supplier_postcode", "acc_name", "acc_number", "acc_sort_code"]);
        $template = $request->input("template-choice");
        $html = view("components.invoice-templates.{$template}", compact('data'))->render();

        return new JsonResponse(["success" => true, "html" => $html]);
    }

    public function getDateInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "date" => ["required", "date"],
        ]);
        if ($validator->fails()) {
            return new JsonResponse(["success" => false, "message" => "Date is required"]);
        }
        $date = Carbon::parse($request->input("date"));

        //Employer
        if ($request->user()->user_acl_role_id == 3) {
            $job = JobPost::query()->where("employer_id", $request->user()->id)->whereDate("job_date", $date)->first();
            if ($job) {
                $freelancer_name = $job->getAcceptedFreelancerData()['name'];
                $result_html = "
                    <p>This date is booked.</p>
                    <p><b>Job Title:</b> {$job->job_title} </p>
                    <p><b>Freelancer Name:</b> {$freelancer_name} </p>'
                ";
                return new JsonResponse(["success" => true, "html" => $result_html]);
            }
        } else {

            $datesRecord = UsersWorkCalender::select("available_dates")->where("user_id", $request->user()->id)->first();
            if ($datesRecord && $datesRecord->available_dates) {
                $recordArray = json_decode($datesRecord->available_dates, true);
                if ($recordArray && sizeof($recordArray) > 0) {
                    foreach ($recordArray as $value) {
                        if (Carbon::parse($value['date'])->equalTo($date)) {
                            return new JsonResponse(["success" => true, "rate" => number_format($value['min_rate'], 2)]);
                        }
                    }
                }
            }
            $minimum_rate = json_decode($request->user()->user_extra_info->minimum_rate, true);
            if ($minimum_rate) {
                $day = $date->format("l");
                $rate = $minimum_rate[$day];
                return new JsonResponse(["success" => true, "rate" => number_format($rate, 2)]);
            }
        }
        return new JsonResponse(["success" => false, "message" => "No data found"]);
    }

    public function getBookedDateInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "date" => ["required", "date"],
        ]);
        if ($validator->fails()) {
            return new JsonResponse(["success" => false, "message" => "Date is required"]);
        }
        $date = Carbon::parse($request->input("date"));

        if ($request->user()->user_acl_role_id == 3) {
            $job = JobPost::query()->where("employer_id", $request->user()->id)->whereDate("job_date", $date)->first();
            if ($job) {
                $freelancer_name = $job->getAcceptedFreelancerData()['name'];
                $result_html = "
                    <p>This date is booked.</p>
                    <p><b>Job Title:</b> {$job->job_title} </p>
                    <p><b>Freelancer Name:</b> {$freelancer_name} </p>'
                ";
                return new JsonResponse(["success" => true, "html" => $result_html]);
            }
        } else {
            $freelancer_user_id = $request->user()->id;
            $live_job = JobPost::whereDate("job_date", $date)->whereIn("job_status", [JobPost::JOB_STATUS_ACCEPTED, JobPost::JOB_STATUS_DONE_COMPLETED])->whereHas("job_actions", function ($q) use ($freelancer_user_id) {
                $q->where("freelancer_id", $freelancer_user_id)->whereIn("action", [JobAction::ACTION_ACCEPT, JobAction::ACTION_DONE]);
            })->first();
            if ($live_job) {
                $address = $live_job->job_address;
                $rate = set_amount_format($live_job->job_rate);
                $html = "
                    <p><b>Location: </b> {$address} </p>
                    <p><b>Job Rate: </b> {$rate} </p>
                ";
                if ($live_job->job_store) {
                    $html .= "<p><b>Company: </b> {$live_job->job_store->store_name} </p>";
                }
                return new JsonResponse(["success" => true, "html" => $html]);
            }

            $private_job = FreelancerPrivateJob::select("job_rate", "job_location")->whereDate("job_date", $date)->first();
            if ($private_job) {
                $address = $private_job->job_location;
                $rate = set_amount_format($private_job->job_rate);
                $html = "
                    <p><b>This is private job</b></p>
                    <p><b>Location: </b> {$address} </p>
                    <p><b>Job Rate: </b> {$rate} </p>
                ";
                return new JsonResponse(["success" => true, "html" => $html]);
            }
        }

        return new JsonResponse(["success" => false, "message" => "No job found"]);
    }

    public function updateUserCalender(Request $request)
    {
        if (Auth::guest()) {
            return new JsonResponse(["success" => false, 'message' => 'Unauthenticated'], 400);
        }
        $availability = $request->input("availability"); //1 available, 2 not available
        $selected_date = Carbon::parse($request->input("selected_date"));
        $user_works = UsersWorkCalender::where("user_id", $request->user()->id)->first();
        $block_dates = [];
        $available_dates = [];
        if ($user_works) {
            $block_dates = json_decode($user_works->block_dates, true) ?? [];
            $available_dates  = json_decode($user_works->available_dates, true) ?? [];
        }

        if ($availability == 1) {
            $min_rate_date = floatval($request->input("min_rate_date"));
            //remove date from block date if present
            $block_dates = array_filter($block_dates, function ($date) use ($selected_date) {
                return Carbon::parse($date)->notEqualTo($selected_date);
            });
            //update or add into available_dates
            $available_dates = array_filter($available_dates, function ($value) use ($selected_date) {
                return Carbon::parse($value['date'])->notEqualTo($selected_date);
            });
            $available_dates[] = ["date" => $selected_date->format("Y-m-d"), "min_rate" => $min_rate_date];
        } else if ($availability == 2) {
            //update, add date into block_dates
            $block_dates = array_filter($block_dates, function ($date) use ($selected_date) {
                return Carbon::parse($date)->notEqualTo($selected_date);
            });
            array_push($block_dates, $selected_date->format("Y-m-d"));
            //remove date from available_dates
            $available_dates = array_filter($available_dates, function ($value) use ($selected_date) {
                return Carbon::parse($value['date'])->notEqualTo($selected_date);
            });
        }
        UsersWorkCalender::updateOrCreate(["user_id" => $request->user()->id], [
            "block_dates" => json_encode(array_values($block_dates)),
            "available_dates" => json_encode($available_dates)
        ]);
        return new JsonResponse(["availability" => $availability, "available_dates" => $available_dates]);
    }

    public function deletePrivateJob(Request $request, $id)
    {
        $job = FreelancerPrivateJob::where("id", $id)->where("freelancer_id", $request->user()->id)->first();
        if ($job) {
            $job->delete();
        }
        return new JsonResponse();
    }

    public function getApplicantInformation($id)
    {

        $user = User::find($id);
        if (is_null($user)) {
            return new JsonResponse(["message" => "No user found"], 500);
        }
        $feedbacks = JobFeedback::with("freelancer")->where("freelancer_id", $id)->where("user_type", "employer")->where("status", 1)->whereDate("created_at", ">=", today()->subMonths(120)->startOfMonth())->get();
        $totalFeedback = count($feedbacks);
        $perRating = get_overall_feedback_rating($feedbacks);

        if ($totalFeedback > 0) {
            $totalStar = 5;
            $ratingStar = $perRating;
            $currentStar = 1;
            $ratingStarsHtml = "";
            while ($totalStar > 0) {
                if ($ratingStar >= $currentStar) {
                    $starClass = 'fa-star';
                } else {
                    $starClass = 'fa-star-o';
                }
                $ratingStarsHtml .= "<i class='fa {$starClass}' aria-hidden='true'></i>";
                $totalStar--;
                $currentStar++;
            }
            $feedbackAvergaeHtml = "
                <div id='stars-rating' class='user-rating'>
                    <span> By {$totalFeedback} Employers </span>
                </div>
            ";
        } else {
            $feedbackAvergaeHtml = "<span style='margin-left:0px;color: red;'>No feedback.</span>";
        }

        if ($totalFeedback > 0) {

            $carousalInnerHtml = "";
            $i = 0;
            foreach ($feedbacks as $feedback) {
                $carousalClass = $i == 0 ? "active" : "";
                $comment_string = get_cleaned_html_content($feedback->comments);
                $feedback_freelancer = $feedback->freelancer;
                $totalStar = 5;
                $ratingStar = $feedback->rating;
                $currentStar = 1;
                $feedbackFreelancerAvergaeHtml = "";
                while ($totalStar > 0) {
                    if ($ratingStar >= $currentStar) {
                        $starClass = 'fa-star';
                    } else {
                        $starClass = 'fa-star-o';
                    }
                    $feedbackFreelancerAvergaeHtml .= "<i class='fa {$starClass}' aria-hidden='true'></i>";
                    $totalStar--;
                    $currentStar++;
                }
                $freelancer_full_name = $feedback_freelancer->firstname . " " . $feedback_freelancer->lastname;

                $freelancerHtml = "";
                if ($feedback_freelancer) {
                    $freelancerHtml = "
                        <ul>
                            <li>
                                <div id='stars-rating' class='user-rating'>
                                    {$feedbackFreelancerAvergaeHtml}
                                </div>
                            </li>
                            <li>
                                <h6><i class='fa fa-user' aria-hidden='true'></i> {$freelancer_full_name} </h6>
                            </li>
                        </ul>
                    ";
                }

                $carousalInnerHtml .= "
                    <div class='item {$carousalClass}'>
                        <div class='row'>
                            <div class='col-md-12 feeback-comment-section'>
                                <p> {$comment_string} </p>
                                <div class='user-info'>
                                    {$freelancerHtml}
                                </div>
                            </div>
                        </div>
                    </div>
                ";
                $i++;
            }

            $individualRatingHtml = "
                <div class='individual-rating'>
                    <a href='#fre_individual_feedback' data-toggle='collapse' aria-expanded='false' class='collapsed fre_individual_feedback'><h4><i class='fa fa-caret-right' aria-hidden='true'></i> &nbsp; Individual Ratings</h4></a>
                    <div id='fre_individual_feedback' aria-expanded='false' class='collapse col-md-12'>
                    <div id='myCarousel' class='carousel slide' data-ride='carousel'>
                        <div class='carousel-inner' role='listbox'>
                            {$carousalInnerHtml}
                        </div>
                        <a class='left carousel-control' href='#myCarousel' role='button' data-slide='prev'>
                            <span class='glyphicon glyphicon-chevron-left' aria-hidden='true'></span>
                            <span class='sr-only'>Previous</span>
                        </a>
                        <a class='right carousel-control' href='#myCarousel' role='button' data-slide='next'>
                            <span class='glyphicon glyphicon-chevron-right' aria-hidden='true'></span>
                            <span class='sr-only'>Next</span>
                        </a>
                    </div>
                </div>
            ";
        } else {
            $individualRatingHtml = "";
        }

        $html = "
            <table class='table table-hover view_applicant_record table-bordered'>
                <tr>
                    <th>Freelancer ID  </th>
                    <td> {$id} </td>
                </tr>
                <tr>
                    <th>Locum Name  </th>
                    <td> {$user->firstname} {$user->lastname} </td>
                </tr>
                <tr>
                    <th>Average Rating  </th>
                    <td>
                        {$feedbackAvergaeHtml}
                    </td>
                </tr>
            </table>
            {$individualRatingHtml}
        ";

        return new JsonResponse(["success" => true, "html" => $html]);
    }

    public function unblockBlockedFreelancer(Request $request)
    {
        $un_block_id = $request->input("un_block_id");
        BlockUser::where("id", $un_block_id)->delete();
        return new JsonResponse();
    }
}
