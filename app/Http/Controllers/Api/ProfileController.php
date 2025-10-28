<?php

namespace App\Http\Controllers\Api;

use App\Helpers\JobMailHelper;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\FinancialYear;
use App\Models\UserAclProfession;
use App\Models\UserAclRole;
use App\Models\UserAnswer;
use App\Models\UserExtraInfo;
use App\Models\UserQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function editProfile(Request $request)
    {
        $user = $request->user();
        //$towns = get_available_tags();
        $user_acl_role = UserAclRole::findOrFail($user->user_acl_role_id);
        $user_acl_profession = UserAclProfession::findOrFail($user->user_acl_profession_id);
        $user_extra_data = $user->user_extra_info;
        $user_acl_package = $user->user_acl_package;
        if(auth()->user()->user_acl_role_id == 2) {
            $user_financial_year = $user->financial_year;
            $personal_info = array(
                'firstname'       => isset($user['firstname']) ? $user['firstname'] : '',
                'lastname'        => isset($user['lastname']) ? $user['lastname'] : '',
                'email'           => isset($user['email']) ? $user['email'] : '',
                'role'            => isset($user_acl_role['name']) ? $user_acl_role['name'] : '',
                'profession'      => isset($user_acl_profession['name']) ? $user_acl_profession['name'] : '',
                'package'         => isset($user_acl_package['name']) ? $user_acl_package['name'] . ' (£' . $user_acl_package['price'] . ')' : '',
                'username'        => isset($user['login']) ? $user['login'] : '',
                'company_name'    => isset($user_extra_data['company']) ? $user_extra_data['company'] : '',
                'store_name'      => isset($user_extra_data['company']) ? $user_extra_data['company'] : '',
                'address'         => isset($user_extra_data['address']) ? $user_extra_data['address'] : '',
                'town'            => isset($user_extra_data['city']) ? $user_extra_data['city'] : '',
                'postcode'        => isset($user_extra_data['zip']) ? $user_extra_data['zip'] : '',
                'telephone'       => isset($user_extra_data['telephone']) ? $user_extra_data['telephone'] : '',
                'mobile_number'   => isset($user_extra_data['mobile']) ?  $user_extra_data['mobile'] : '',
                'financial_year'  => isset($user->financial_year) ? $user->financial_year : '',
            );
        }
        elseif(auth()->user()->user_acl_role_id == 3) {
            $personal_info = array(
                'firstname'       => isset($user['firstname']) ? $user['firstname'] : '',
                'lastname'        => isset($user['lastname']) ? $user['lastname'] : '',
                'email'           => isset($user['email']) ? $user['email'] : '',
                'role'            => isset($user_acl_role['name']) ? $user_acl_role['name'] : '',
                'profession'      => isset($user_acl_profession['name']) ? $user_acl_profession['name'] : '',
                'package'         => isset($user_acl_package['name']) ? $user_acl_package['name'] . ' (£' . $user_acl_package['price'] . ')' : '',
                'username'        => isset($user['login']) ? $user['login'] : '',
                'company_name'    => isset($user_extra_data['company']) ? $user_extra_data['company'] : '',
                'store_name'      => isset($user_extra_data['company']) ? $user_extra_data['company'] : '',
                'address'         => isset($user_extra_data['address']) ? $user_extra_data['address'] : '',
                'town'            => isset($user_extra_data['city']) ? $user_extra_data['city'] : '',
                'postcode'        => isset($user_extra_data['zip']) ? $user_extra_data['zip'] : '',
                'telephone'       => isset($user_extra_data['telephone']) ? $user_extra_data['telephone'] : '',
                'mobile_number'   => isset($user_extra_data['mobile']) ?  $user_extra_data['mobile'] : '',
            );
            
        }

        return response()->success($personal_info);
    }

    public function updateProfile(Request $request)
    {
        $mailHelper = new JobMailHelper();
        $user = $request->user();
        $uid = $request->user()->id;
        $fname = $request->input('user_info.firstname', '');
        $lname = $request->input('user_info.lastname', '');
        $email = $request->input('user_info.email', '');
        if ($user->user_acl_role_id == User::USER_ROLE_LOCUM) {
            $company = $request->input('user_info.company_name', '');
        } else {
            $company = $request->input('user_info.store_name', '');
        }

        $address = $request->input('user_info.address', '');
        $city = $request->input('user_info.town', '');
        $zip = $request->input('user_info.postcode', '');
        $telephone = $request->input('user_info.telephone', '');
        $mobile = $request->input('user_info.mobile_number', '');
        $user_update_array = [
            "lastname" => $lname,
            "firstname" => $fname,
        ];
        if ($request->has('user_info.password')) {
            $user_update_array["password"] = Hash::make($request->input('user_info.password', ''));
        }
        User::where("id", $uid)->update($user_update_array);

        UserExtraInfo::where("user_id", $uid)->update([
            "mobile" => $mobile,
            "address" => $address,
            "city" => $city,
            "zip" => $zip,
            "telephone" => $telephone,
            "company" => $company,
        ]);
        $job_data_mail['firstname'] = $fname;
        $job_data_mail['lastname'] = $lname;
        $job_data_mail['email'] = $email;

        $mailHelper->updateProfileMails($job_data_mail);
        if(auth()->user()->user_acl_role_id == '2')
        {
            $type = $request['user_info']['employment_status'];
    
            $financial_year = $request->user()->financial_year ?? new FinancialYear();
            $financial_year->user_id = $request->user()->id;
            $financial_year->user_type = $type;
            
            if ($type == 'limitedcompany') {
                $financial_year->month_start = ($request['user_info']['start_month'] + 1) % 12 ?: 12;
                $financial_year->month_end = $request['user_info']['start_month'] % 12 ?: 12;
            } else {
                $financial_year->month_start = 4;
                $financial_year->month_end = 3;
            }
            $financial_year->save(); 
        }

        return response()->success($request->all());
    }

    public function editQuestions(Request $request)
    {
        $user = $request->user();
        $role = $user->user_acl_role_id;
        $profession = $user->user_acl_profession_id;

        $quesData = [
            "address" => $user->user_extra_info?->address,
            "city" => $user->user_extra_info?->city,
            "zip" => $user->user_extra_info?->zip,
            "minimum_rate" => json_decode($user->user_extra_info?->minimum_rate) ? json_decode($user->user_extra_info?->minimum_rate) : "",
            "max_distance" => $user->user_extra_info?->max_distance,
            "store_week_time" => "",
            "store_unique_time" => "",
            "cet" => $user->user_extra_info?->cet,
            "goc" => $user->user_extra_info?->goc,
            "aop" => $user->user_extra_info?->aop,
            "inshurance_company" => $user->user_extra_info?->inshurance_company,
            "inshurance_no" => $user->user_extra_info?->inshurance_no,
            "store_data" => "",
            "store_id" => [$user->user_extra_info?->store_type_name],
            "form_id" => "",
            "aoc_id" => $user->user_extra_info?->aoc_id,
            "inshurance_renewal_date" => $user->user_extra_info?->inshurance_renewal_date,
        ];
        if ($role == 3) {
            $quesData['questions'] = UserQuestion::where("employer_question", "!=", "")->where("user_acl_profession_id", $profession)->orderBy("sort_order")
                ->select("id", "employer_question as q", "type as type_key", "values as type_value", "is_required as required_status", "range_type_unit", "range_type_condition")->get()->toArray();
            foreach ($quesData['questions'] as $key => $que) {
                $que["range_type_unit"] = is_null($que["range_type_unit"]) ? "" : $que["range_type_unit"];
                $que["range_type_condition"] = is_null($que["range_type_condition"]) ? "" : $que["range_type_condition"];
                if ($que['type_value'] != '') {
                    $quesData['questions'][$key]['type_value'] = json_decode($que['type_value']);
                }
                if ($que['type_key'] == 5) {
                    $quesData['questions'][$key]['type_key'] = 3;
                }
                $answer = UserAnswer::where("user_id", $user->id)->where("user_question_id", $que["id"])->first();
                if ($answer) {
                    $type_value = json_decode($answer->type_value) ? json_decode($answer->type_value) : [$answer->type_value];
                    array_push($quesData['questions'][$key], $type_value);
                }
            }
        } elseif ($role == 2) {
            $quesData['questions'] = UserQuestion::where("freelancer_question", "!=", "")->where("user_acl_profession_id", $profession)->orderBy("sort_order")
                ->select("id", "employer_question as q", "type as type_key", "values as type_value", "is_required as required_status", "range_type_unit", "range_type_condition")->get()->toArray();
            foreach ($quesData['questions'] as $key => $que) {
                $que["range_type_unit"] = is_null($que["range_type_unit"]) ? "" : $que["range_type_unit"];
                $que["range_type_condition"] = is_null($que["range_type_condition"]) ? "" : $que["range_type_condition"];
                if ($que['type_value'] != '') {
                    $quesData['questions'][$key]['type_value'] = json_decode($que['type_value']);
                }
                $answer = UserAnswer::where("user_id", $user->id)->where("user_question_id", $que["id"])->first();
                if ($answer) {
                    $type_value = json_decode($answer->type_value) ? json_decode($answer->type_value) : [$answer->type_value];
                    array_push($quesData['questions'][$key], $type_value);
                }
            }
        }
        return response()->success($quesData);
    }

    public function updateQuestions(Request $request)
    {
        $user_data = $request->all();
        $user = $request->user();
        $aoc_id = isset($user_data['personal_info']['opl']) ? $user_data['personal_info']['opl'] : '';
        $minimum_rate = isset($user_data['personal_info']['min_rate']) ? json_encode($user_data['personal_info']['min_rate']) : '';
        $max_distance = isset($user_data['personal_info']['max_distance']) ? $user_data['personal_info']['max_distance'] : '';
        $goc = isset($user_data['personal_info']['goc']) ? $user_data['personal_info']['goc'] : '';
        $cet = isset($user_data['personal_info']['cet']) ? $user_data['personal_info']['cet'] : '';
        $aop = isset($user_data['personal_info']['aop']) ? $user_data['personal_info']['aop'] : '';
        $inshurance_company = isset($user_data['personal_info']['inshurance_company']) ? $user_data['personal_info']['inshurance_company'] : '';
        $inshurance_no = isset($user_data['personal_info']['inshurance_no']) ? $user_data['personal_info']['inshurance_no'] : '';
        $inshurance_renewal_date = isset($user_data['personal_info']['inshurance_renewal_date']) ? $user_data['personal_info']['inshurance_renewal_date'] : '';
        $store_id = isset($user_data['personal_info']['store_info']) ? $user_data['personal_info']['store_info'] : '';
        $store_data = isset($user_data['personal_info']['store_data']) ? $user_data['personal_info']['store_data'] : '';
        $store_info = array();
        $store_info_data = array();
        //If Role is locum then add store id in array
        if ($user_data['personal_info']['user_acl_role_id'] == '2') {
            if (!empty($store_data)) {
                foreach ($store_data as $key => $value) {
                    $store_info_data[] = $value;
                }
                $store_info_data = implode(",", $store_info_data);
            }
            if (!empty($store_id)) {
                foreach ($store_id as $key => $value) {
                    $value_replaced = str_replace("_", " ", $key);
                    $store_info[] = $value_replaced;
                }
                $store_info = implode(",", $store_info);
            }
        } else {
            $store_info = $store_id;
            $store_info = implode(",", $store_info);
            $store_info_data = '';
        }

        $ans_val = isset($user_data['ans_val']) ? $user_data['ans_val'] : '';
        if (!empty($ans_val)) {
            foreach ($ans_val as $key => $value) {
                $question = UserQuestion::find($key);
                if (is_null($question)) {
                    continue;
                }
                if ($question->type == 3 && is_array($value)) {
                    $value = json_encode($value);
                } elseif (is_array($value) && sizeof($value) > 0) {
                    $value = $value[0];
                } elseif (is_array($value)) {
                    $value = "";
                }
                UserAnswer::updateOrCreate([
                    "user_id" => $user->id,
                    "user_question_id" => $key
                ], [
                    "type_value" => $value
                ]);
            }
        }
        //Update /Insert store list
        $storelist = isset($user_data['storelist']) ? $user_data['storelist'] : '';
        $user_extra_info = $user->user_extra_info;
        if (!empty($storelist) && json_encode($storelist)) {
            $encoded_store_list = json_encode($storelist);
            $user_extra_info->site_town_ids = $encoded_store_list;
        }
        $user_extra_info->aoc_id = $aoc_id;
        $user_extra_info->minimum_rate = $minimum_rate;
        $user_extra_info->max_distance = $max_distance;
        $user_extra_info->cet = $cet;
        $user_extra_info->goc = $goc;
        $user_extra_info->aop = $aop;
        $user_extra_info->inshurance_company = $inshurance_company;
        $user_extra_info->inshurance_no = $inshurance_no;
        $user_extra_info->inshurance_renewal_date = $inshurance_renewal_date;
        $user_extra_info->store_type_name = $store_info;
        $user_extra_info->save();

        return response()->success($user_data);
    }
}
