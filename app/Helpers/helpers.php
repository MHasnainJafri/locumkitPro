<?php

use App\Helpers\DistanceCalculateHelper;
use App\Models\JobAction;
use App\Models\JobCancelation;
use App\Models\JobFeedback;
use App\Models\JobPost;
use App\Models\PrivateUser;
use App\Models\PrivateUserJobAction;
use App\Models\SiteTown;
use App\Models\User;
use App\Models\UserAclPackageResource;
use App\Models\UserAnswer;
use App\Models\UserQuestion;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use \Illuminate\Support\Str;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Output\ConsoleOutput;

if (!function_exists("get_user_database_questions")) {
    function get_user_database_questions(int $role_id, int $profession_id, bool $with_answers = false): string
    {
        if ($with_answers) {
            if (Auth::check()) {
                $user_answers = UserAnswer::where("user_id", Auth::user()->id)->get();
            } else {
                return "";
            }
        }
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
                $value = "";
                if ($with_answers) {
                    $user_answer = $user_answers->first(function ($value) use ($row) {
                        return $value->user_question_id === $row->id;
                    });
                    if ($user_answer) {
                        $value = $user_answer->type_value;
                    }
                }
                $ans_method .= "<input type='text' id='ans_text_{$i}' value='{$value}' name='ans_val_for_question_id_{$row->id}' class='width-100 {$req_class}'>";
                if ($qus_required == 1) {
                    $ans_method .= "<div id='required-qus-{$row->id}' style='clear: both;color: red;'></div>";
                }
            }

            if ($row['type'] == 2) {
                $answer_value = "";
                if ($with_answers) {
                    $user_answer = $user_answers->first(function ($value) use ($row) {
                        return $value->user_question_id === $row->id;
                    });
                    if ($user_answer) {
                        $answer_value = $user_answer->type_value;
                    }
                }
                $ans_method .= "<select name='ans_val_for_question_id_{$row->id}' id='ans_option_{$i}' class='width-100 form-control {$req_class}' {$req_atribute}><option value=''>Please select</option>";
                $available_values = json_decode($row['values']);
                foreach ($available_values as $value) {
                    if ($answer_value == $value) {
                        $ans_method .= "<option value='{$value}' selected>{$value}</option>";
                    } else {
                        $ans_method .= "<option value='{$value}' >{$value}</option>";
                    }
                }
                $ans_method .= "</select>";
                if ($qus_required == 1) {
                    $ans_method .= "<div id='required-qus-{$row->id}' style='clear: both;color: red;'></div>";
                }
            }

            if ($row['type'] == 3) {
                $answer_value = [];
                if ($with_answers) {
                    $user_answer = $user_answers->first(function ($value) use ($row) {
                        return $value->user_question_id === $row->id;
                    });
                    if ($user_answer) {
                        $answer_value = json_decode($user_answer->type_value) ?? [];
                    }
                }
                $ans_method .= "<div class='multi_select'>";
                $available_values = json_decode($row['values']);
                foreach ($available_values as $value) {
                    $val_checked_attr = "";
                    if (in_array($value, $answer_value)) {
                        $val_checked_attr = "checked";
                    }
                    $ans_method .= "<div style='float:left;width:50%;'>
                                        <input type='checkbox' name='ans_val_for_question_id_{$row->id}[]' class='{$req_class}' value='{$value}' {$val_checked_attr} />
                                        <span class='margin-left'> {$value} </span>
                                    </div>";
                }
                $ans_method .= "</div>";
                if ($qus_required == 1) {
                    $ans_method .= "<div id='required-qus-{$row->id}' style='clear: both;color: red;'></div>";
                }
            }
            if ($row['type'] == 4) {
                $answer_value = "";
                if ($with_answers) {
                    $user_answer = $user_answers->first(function ($value) use ($row) {
                        return $value->user_question_id === $row->id;
                    });
                    if ($user_answer) {
                        $answer_value = $user_answer->type_value;
                    }
                }
                $range_type_unit = $row['range_type_unit'];
                $range_type_condition = $row['range_type_condition'];
                $condition_arr = array("1" => "Greater than", "2" => "Greater than OR equal to", "3" => "Less than", "4" => "Less than OR equal", "5" => "Equal to");
                $range_val = $condition_arr[$range_type_condition];

                $ans_method .= "<select name='ans_val_for_question_id_{$row->id}' id='ans_option_{$i}' class='width-100 form-control {$req_class}' {$req_atribute} ><option value=''>Please select</option>";

                $available_values = json_decode($row['values']);
                foreach ($available_values as $value) {
                    if ($answer_value === $value) {
                        $ans_method .= "<option value='{$value}' > {$range_val} {$value} {$range_type_unit} </option>";
                    } else {
                        $ans_method .= "<option value='{$value}' selected> {$range_val} {$value} {$range_type_unit} </option>";
                    }
                }
                $ans_method .= "</select>";
                if ($qus_required == 1) {
                    $ans_method .= "<div id='required-qus-{$row->id}' style='clear: both;color: red;'></div>";
                }
            }
            if ($row['type'] == 5) {
                $answer_value = "";
                if ($with_answers) {
                    $user_answer = $user_answers->first(function ($value) use ($row) {
                        return $value->user_question_id === $row->id;
                    });
                    if ($user_answer) {
                        $answer_value = $user_answer->type_value;
                    }
                }
                $range_type_unit = $row['range_type_unit'];
                $range_type_condition = $row['range_type_condition'];
                $condition_arr = array("1" => "Greater than", "2" => "Greater than OR equal to", "3" => "Less than", "4" => "Less than OR equal", "5" => "Equal to");
                $range_val = $condition_arr[$range_type_condition];

                $ans_method .= "<select name='ans_val_for_question_id_{$row->id}' id='ans_option_{$i}' class='width-100 form-control {$req_class}' {$req_atribute} ><option value=''>Please select</option>";

                $available_values = json_decode($row['values']);
                foreach ($available_values as $value) {
                    if ($answer_value === $value) {
                        $ans_method .= "<option value='{$value}' selected > {$value} {$range_type_unit} </option>";
                    } else {
                        $ans_method .= "<option value='{$value}' > {$value} {$range_type_unit} </option>";
                    }
                }
                $ans_method .= "</select>";
                if ($qus_required == 1) {
                    $ans_method .= "<div id='required-qus-{$row->id}' style='clear: both;color: red;'></div>";
                }
            }

            if ($row['type'] == 6) {
                $answer_value = "";
                if ($with_answers) {
                    $user_answer = $user_answers->first(function ($value) use ($row) {
                        return $value->user_question_id === $row->id;
                    });
                    if ($user_answer) {
                        $answer_value = $user_answer->type_value;
                    }
                }
                $ans_method = "<select name='ans_val_for_question_id_{$row->id}' id='ans_option_{$i}' class='width-100 form-control' {$req_atribute}>";
                if ($qus_required != 1) {
                    $ans_method .= "<option value=''>Please select</option>";
                }
                if ($answer_value == "Yes") {
                    $ans_method .= "<option value='Yes' selected>Yes</option>";
                } else {
                    $ans_method .= "<option value='Yes'>Yes</option>";
                }
                if ($answer_value == "No") {
                    $ans_method .= "<option value='No' selected>No</option>";
                } else {
                    $ans_method .= "<option value='No'>No</option>";
                }
                $ans_method .= "</select>";
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

        return $html;
    }
}

if (!function_exists("get_user_default_questions")) {
    function get_user_default_questions(): string
    {
        $html = '';
        return $html;
    }
}

if (!function_exists("set_amount_format")) {
    function set_amount_format(float $amount): string
    {
        return get_site_currency_symbol() . number_format($amount, 2);
    }
}
if (!function_exists("get_site_currency_symbol")) {
    function get_site_currency_symbol(): string
    {
        return '£';
    }
}

if (!function_exists("generate_random_color")) {
    function generate_random_color()
    {
        return str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT) . str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT) . str_pad(dechex(mt_rand(0, 255)), 2, '0', STR_PAD_LEFT);
    }
}

if (!function_exists("generate_good_color")) {
    function generate_good_color(int $i = null): string
    {
        $color_list = [
            "EC6B56",
            "FFC154",
            "47B39C",
            "2085EC",
            "72B4EB",
            "0A417A",
            "8464A0",
            "CEA9BC",
            "323232",
        ];
        if ($i && $i >= 0) {
            return $color_list[$i % 9];
        } else {
            return $color_list[array_rand($color_list)];
        }
    }
}
if (!function_exists("get_abbrevated_days_list")) {
    function get_abbrevated_days_list(): array
    {
        return [
            'Sun' => 0, 'Mon' => 0, 'Tue' => 0, 'Wed' => 0, 'Thu' => 0, 'Fri' => 0, 'Sat' => 0
        ];
    }
}
if (!function_exists("get_days_list")) {
    function get_days_list(): array
    {
        return [
            "Monday",
            "Tuesday",
            "Wednesday",
            "Thursday",
            "Friday",
            "Saturday",
            "Sunday"
        ];
    }
}

if (!function_exists("get_relative_days_for_job")) {
    function get_relative_days_for_job(string $date): int
    {
        if ($date) {
            try {
                return Carbon::parse($date)->diffInDays(today());
            } catch (InvalidFormatException) {
            }
        }
        return 0;
    }
}
if (!function_exists("get_user_data_for_invoice")) {
    function get_user_data_for_invoice(User $user): array|null
    {
        if ($user) {
            $name = $user->firstname . " " . $user->lastname;
            $address = $user->user_extra_info ? $user->user_extra_info->address : "";
            $contact_no = $user->user_extra_info ? $user->user_extra_info->mobile : "";
            $email = $user->email;
            $city = $user->user_extra_info ? $user->user_extra_info->city : "";
            $company = $user->user_extra_info ? $user->user_extra_info->company : "";
            $acccount_name = $user->user_bank_detail ? $user->user_bank_detail->acccount_name : "";
            $acccount_number = $user->user_bank_detail ? $user->user_bank_detail->acccount_number : "";
            $acccount_sort_code = $user->user_bank_detail ? $user->user_bank_detail->acccount_sort_code : "";

            return [
                "name" => $name,
                "address" => $address,
                "contact_no" => $contact_no,
                "email" => $email,
                "city" => $city,
                "company" => $company,
                "acccount_name" => $acccount_name,
                "acccount_number" => $acccount_number,
                "acccount_sort_code" => $acccount_sort_code,
            ];
        }
        return null;
    }
}

if (!function_exists("get_overall_feedback_rating")) {
    function get_overall_feedback_rating(Collection $feedbacks): int
    {
        if ($feedbacks && sizeof($feedbacks) > 0) {
            $feedbacks_count = sizeof($feedbacks);
            $total_sum_rating = 0;
            foreach ($feedbacks as $feedback) {
                $total_sum_rating += $feedback->rating;
            }
            return round($total_sum_rating / ($feedbacks_count * 5) * 100);
        }
        return 0;
    }
}

if (!function_exists("get_overall_feedback_rating_by_user")) {
    function get_overall_feedback_rating_by_user($id, $type = "freelancer"): float
    {
        if ($type == "freelancer") {
            $feedbacks = JobFeedback::where("freelancer_id", $id)->where("user_type", "employer")->where("status", 1)->where("created_at", ">=", today()->subMonths(120)->startOfMonth())->get();
        } else {
            $feedbacks = JobFeedback::where("employer_id", $id)->where("user_type", "freelancer")->where("status", 1)->where("created_at", ">=", today()->subMonths(120)->startOfMonth())->get();
        }
        if (sizeof($feedbacks) > 0) {
            return get_overall_feedback_rating($feedbacks);
        }
        return 0;
    }
}

if (!function_exists("get_job_cancellation_rate_by_user")) {
    function get_job_cancellation_rate_by_user($id, $type = "freelancer"): float
    {
        $cancellation_rate = 0;
        $job_canceled = JobCancelation::where("user_id", $id)->where("created_at", ">=", today()->subMonths(6)->startOfMonth())->count();
        if ($type == "freelancer") {
            $job_accepted = JobAction::whereIn("action", [JobAction::ACTION_CANCEL_ACCEPTED_JOB_BY_EMPLOYER, JobAction::ACTION_ACCEPT, JobAction::ACTION_DONE])->where("freelancer_id", $id)->where("created_at", ">=", today()->subMonths(6)->startOfMonth())->count();
        } else {
            $job_accepted = JobPost::whereIn("job_status", [JobPost::JOB_STATUS_ACCEPTED, JobPost::JOB_STATUS_DONE_COMPLETED])->where("employer_id", $id)->where("created_at", ">=", today()->subMonths(6)->startOfMonth())->count();
        }
        if ($job_accepted > 0) {
            $cancellation_rate = number_format(($job_canceled / $job_accepted) * 100, 2);
        } else if ($job_canceled > 0) {
            $cancellation_rate = 100;
        }
        return $cancellation_rate;
    }
}

if (!function_exists('get_cleaned_html_content')) {
    function get_cleaned_html_content(string $content, int $word_limit = 20): string
    {
        // return Str::words(html_entity_decode($content), $word_limit);// setting this according to the limit of 20 words
        return Str::words(html_entity_decode(strip_tags($content)), $word_limit);
    }
}


if (!function_exists('get_default_date_format')) {
    function get_default_date_format(): string
    {
        return "d/m/Y";
    }
}
if (!function_exists('get_default_date_format_app')) {
    function get_default_date_format_app(): string
    {
        return "m/d/Y";
    }
}

if (!function_exists('get_web_default_date_format')) {
    function get_web_default_date_format(): string
    {
        return "d/m/yy";
    }
}

if (!function_exists('get_date_with_default_format')) {
    function get_date_with_default_format(mixed $date, string $originally_date_format = "Y-m-d"): string|null
    {
        if (is_null($date)) {
            return null;
        }
        $format = get_default_date_format();
        if (is_a($date, Carbon::class)) {
            return $date->format($format);
        }
        try {
            return Carbon::parse($date)->format($format);
        } catch (InvalidFormatException) {
        }
        if ($c = Carbon::createFromFormat($originally_date_format, $date)) {
            return $c->format($format);
        }
        return null;
    }
}
if (!function_exists('get_date_with_default_format_app')) {
    function get_date_with_default_format_app(mixed $date, string $originally_date_format = "Y-m-d"): string|null
    {
        if (is_null($date)) {
            return null;
        }
        $format = get_default_date_format_app();
        if (is_a($date, Carbon::class)) {
            return $date->format($format);
        }
        try {
            return Carbon::parse($date)->format($format);
        } catch (InvalidFormatException) {
        }
        if ($c = Carbon::createFromFormat($originally_date_format, $date)) {
            return $c->format($format);
        }
        return null;
    }
}
if (!function_exists('parse_date_from_default_format')) {
    function parse_date_from_default_format(mixed $date): Carbon|null
    {
        if (is_null($date)) {
            return null;
        }
        $format = get_default_date_format();
        if (is_a($date, Carbon::class)) {
            return $date;
        }
        try {
            return Carbon::parse($date);
        } catch (InvalidFormatException) {
        }
        if ($c = Carbon::createFromFormat($format, $date)) {
            return $c;
        }
        return null;
    }
}
if (!function_exists('get_question_answer_rows')) {
    function get_question_answer_rows(): string
    {
        $html = "";
        if (Auth::check()) {
            $user_questions = UserQuestion::where("user_acl_profession_id", Auth::user()->user_acl_profession_id)->get();
            $user_answers = UserAnswer::where("user_id", Auth::user()->id)->get();
            foreach ($user_questions as $question) {
                $answer_value = "";
                $user_answer = $user_answers->first(function ($value) use ($question) {
                    return $value->user_question_id === $question->id;
                });
                if ($user_answer) {
$decoded = json_decode($user_answer->type_value, true);
$answer_value = is_array($decoded) ? implode(" / ", $decoded) : $user_answer->type_value;
                }
                $question_required = $question->is_required == 1 ? '<i class="fa fa-asterisk required-stars" aria-hidden="true"></i>' : '';
                if (Auth::user()->user_acl_role_id == 2) {
                    $question_string = $question->freelancer_question;
                } else {
                    $question_string = $question->employer_question;
                }
                $html .= "
                    <tr>
                        <th style='position: relative;'><b>{$question_string}</b> {$question_required} </th>
                        <td>{$answer_value}</td>
                    </tr>
                ";
            }
        }
        return $html;
    }
}

if (!function_exists('get_available_tags')) {
    function get_available_tags(): array
    {
        $towns = SiteTown::where("town", "!=", "")->select("town")->pluck("town")->toArray();
        return $towns;
    }
}
if (!function_exists('make_short_url')) {
    function make_short_url(string $url): string
    {
        return $url;
        $api_url = config("app.bitly_short_api_url");
        $api_token = config('app.bitly_short_url_api_key');
        try {
            $response = Http::withHeaders([
                "Content-Type" => "application/json",
                "Accept" => "application/json",
                "Authorization" => "Bearer {$api_token}"
            ])->post($api_url, [
                "long_url" => $url,
                "domain" => "bit.ly",
            ])->json();

            if ($response && is_array($response) && isset($response["link"])) {
                return $response["link"];
            }
        } catch (Exception) {
        }
        return $url;
    }
}

if (!function_exists('get_locum_email_terms')) {
    function get_locum_email_terms(string $background_color = '#2dc9ff'): string
    {
        $locum_email_terms = '
            <table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;" width="100%;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
                  <tr style="background-color: ' . $background_color . ';">
                  <th style=" border: 1px solid black;  text-align:left;  padding:5px; font-weight:bold;color:#fff;"> Locumkit terms and condition</th>
                  </tr>
                  <tr>
                  <th style=" border: 1px solid black;  text-align:left;  padding:5px;">DOCUMENTATION</th>
                  </tr>
                  <tr>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">Please ensure you have provided us the up to date/latest:
                      <ul>
                        <li> GOC registration details</li>
                        <li> Evidence of current PCT listing</li>
                        <li> Two clinical references </li>
                        <li> Recent CV (not compulsory but recommended)</li>
                        <li> Proof of Professional indemnity Insurance</li>
                        <li> Copy of your personal ID </li>
                        <li>Up to date evidence of your CET record (we shall reqest this once a quarter to verify correct disclosure of CET points)</li>
                      </ul>
                    </td>
                  </tr>
                  <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px;">DRESS CODE</th>
                  </tr>
                  <tr>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">
                      <p>We expect all locums to dress appropriately in business attire. </p>
                      <p>Employees are expected to demonstrate good judgment and professional taste. Courtesy of coworkers and your professional image to clients should be the factors that are used to assess that you are dressing in business attire that is appropriate.</p>
                      <p>Please check for any additional comments above, where the employer might highlight any specific dress wear.</p>
                    </td>
                  </tr>
                  <tr>
                  <td style=" border: 1px solid black;  text-align:left;  padding:5px;font-weight: bold;">CANCELLATIONS</td>
                  </tr>
                  <tr>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">

                      <p>In the event that you are unable to fulfill your booking, it is vital that you cancel the job as soon as possible to give the store as much notice to make alternative arrangements. If the cancellation is at short notice (i.e. less than 24 hours before the booking date), please call the store directly as well as canceling through Locumkit.</p>
                      <p>We advise to keep cancellations at a minimum - all cancellations go on your record and can impact your future bookings.</p>
                    </td>
                  </tr>
            </table>
        ';
        return $locum_email_terms;
    }
}
if (!function_exists('can_user_package_has_privilege')) {
    function can_user_package_has_privilege(User $user, $privilege): bool
    {
        $package_resorce_ids = json_decode($user->user_acl_package->user_acl_package_resources_ids_list) ?? [];
        $resource_count = UserAclPackageResource::where("resource_key", $privilege)->whereIn("id", $package_resorce_ids)->count();
        return $resource_count > 0;
    }
}

if (!function_exists('is_range_condition_succeed')) {
    /**
     * @param string|int|float $value1 A string or integer or float eg [10-15], 10, 20.5
     * @param string|int|float $value2 A string or integer or float eg [10-15], 10, 20.5
     */
    function is_range_condition_succeed(string|int|float $value1, string|int|float $value2, string $condition): bool
    {
        $first_value = 0;
        $second_value = 0;
        if (is_int($value1) || is_float($value1)) {
            $first_value = $value1;
        } else if (is_string($value1)) {
            $first_value = intval($value1);
            $str_array = explode("-", $value1, 2);
            if (sizeof($str_array) == 2) {
                $first_value = (intval($str_array[0]) + intval($str_array[1])) / 2;
            }
        }
        if (is_int($value2) || is_float($value2)) {
            $second_value = $value2;
        } else if (is_string($value2)) {
            $second_value = intval($value2);
            $str_array = explode("-", $value2, 2);
            if (sizeof($str_array) == 2) {
                $second_value = (intval($str_array[0]) + intval($str_array[1])) / 2;
            }
        }

        if ($condition == ">") {
            return $first_value > $second_value;
        } else if ($condition == ">=") {
            return $first_value >= $second_value;
        } else if ($condition == "<") {
            return $first_value < $second_value;
        } else if ($condition == "<=") {
            return $first_value <= $second_value;
        } else if ($condition == "=") {
            return $first_value == $second_value;
        }
        return false;
    }
}

if (!function_exists('get_mail_header')) {

    function get_mail_header()
    {
        $site_name  = config('app.name');
        $header = '<div style="width: 700px;"><div class="mail-header" style="background: #00A9E0; padding: 20px 50px;  border: 2px solid #000; clear: both; ">';
        $header .= '
          <a style="outline: none !important;" href="' . url('/') . '"><img src="' . url("/frontend/locumkit-template/img/logo.png") . '" alt="' . $site_name . '" width="100px"></a>
          ';
        $header .= '</div><div style="border-right: 2px solid #000; border-left: 2px solid #000;">';
        return $header;
    }
}
if (!function_exists('get_mail_footer')) {

    function get_mail_footer()
{
    $footer = '<div style="padding: 0px 50px 30px; text-align: left; font-family: sans-serif;">
          <p style="margin: 5px 0px;"><b>Thank you</b></p>
          <p style="margin: 5px 0px;"><b>The Locumkit Team</b></p>
          <p style="font-size: 13px; margin: 5px 0px;"></p>
          <p style="margin: 5px 0px;">
              <em>For any queries please contact us <a href="' . env('APP_URL') . '/contact">here</a>.</em>
          </p>
          </div>
          <div class="mail-footer" style="background: #252525; color: #fff; padding: 15px 50px; margin-top: 0px; border-top: 2px solid #000;">
          <span style="width: 50%; line-height: 26px; color: #a3a3a3;">
              Copyright &copy; ' . date("Y") . ' Locumkit - All Rights Reserved
          </span>
          <ul style="display: inline-block; padding: 0; margin: 0 auto; width: 40%; text-align: right;">
              <li style="list-style: none; margin-left: 5px; margin-right: 5px; display: inherit;">
                  <a href="https://www.facebook.com/locumkit" target="_blank">
                      <img src="' . env('APP_URL') . '/frontend/locumkit-template/new-design-assets/img/facebook-n.png" title="Facebook" alt="Facebook">
                  </a>
              </li>
              <li style="list-style: none; margin-left: 5px; margin-right: 5px; display: inherit;">
                  <a href="https://www.linkedin.com/company/locumkit" target="_blank">
                      <img src="' . env('APP_URL') . '/frontend/locumkit-template/new-design-assets/img/linkedin-n.png" title="LinkedIn" alt="LinkedIn">
                  </a>
              </li>
          </ul>
          </div>';
    return $footer;
}

}

if (!function_exists('get_expired_job_info')) {

    function get_expired_job_info(JobPost $job, User $employer): string
    {
        $empName = $employer['firstname'] . ' ' . $employer['lastname'];

        $jobDate = get_date_with_default_format($job->job_date);
        $jobRate = set_amount_format($job->job_rate);

        $jobAddress = $job['job_address'] . ", " . $job['job_region'] . "-" . $job['job_zip'];
        $countFreToNotify = JobAction::where("job_post_id", $job->id)->count();

        $job_info = '<table style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;">
                        <tr style="background-color: #f2f2f2;">
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Employer</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $empName . '</td>
                    </tr>
                    <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job date</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobDate . '</td>
                    </tr>
                    <tr style="background-color: #f2f2f2;">
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job rate</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobRate . '</td>
                    </tr>
                    <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Location</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $jobAddress . '</td>
                    </tr>
                    <tr style="background-color: #f2f2f2;">
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Job title</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $job->id . '</td>
                    </tr>
                    <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Number of people sent to</th>
                    <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $countFreToNotify . '</td>
                    </tr>
                    <tr>
                    <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;"></th>
                    <td style=" border: 1px solid black;  text-align:left;">
                    <table style="text-align:left;" width="100%">
                    <tr>
                    <td width="50%" style="border-right:1px solid black;">SMS SEND : 0 </td>
                    <td style="margin-left: 10px; display: block;">EMAIL SEND : ' . $countFreToNotify . '</td>
                    </tr>
                    </td>
                    </tr>
                </table>';

        return $job_info;
    }
}

if (!function_exists('render_feedback_stars')) {
    function render_feedback_stars($ratingStar)
    {
        $totalStar = 5;
        $currentStar = 1;
        $html = "";
        while ($totalStar > 0) {
            if ($ratingStar >= $currentStar) {
                $starClass = 'fa-star';
            } else {
                $starClass = 'fa-star-o';
            }
            $html .= "<i class='fa {$starClass}' aria-hidden='true'></i>";
            $totalStar--;
            $currentStar++;
        }
        return $html;
    }
}
if (!function_exists('validate_private_user_mail')) {
    function validate_private_user_mail(string $email, $employer_id = null): bool
    {
        $count = PrivateUser::where("email", $email)->where("status", "!=", 2);
        if ($employer_id && $employer_id > 0) {
            $count = $count->where("employer_id", $employer_id);
        }
        $count = $count->count();
        if ($count > 0) {
            return false;
        }
        $count = User::where("email", $email)->count();
        if ($count > 0) {
            return false;
        }
        return true;
    }
}
if (!function_exists('extract_values_by_key_from_multiarray')) {
    function extract_values_by_key_from_multiarray(array $array, string $key): array
    {
        return array_map(function ($val) use ($key) {
            return $val[$key];
        }, $array);
    }
}

if (!function_exists('get_financial_year_range')) {
    function get_financial_year_range(int $user_financial_year_start_month, int|null $current_year = null): array
    {
        if (is_null($current_year) || !$current_year || $current_year <= 0) {
            $current_year = date('Y');
        }
        if ($user_financial_year_start_month == 1) {
            $start_year = $current_year;
        } elseif (now()->month >= $user_financial_year_start_month) {
            $start_year = $current_year;
        } else {
            $start_year = $current_year - 1;
        }

        return [
            "year_start" => Carbon::createFromDate($start_year, $user_financial_year_start_month, 1)->startOfMonth(),
            "year_end" => Carbon::createFromDate($start_year, $user_financial_year_start_month, 0)->addYear()->endOfMonth()
        ];
    }
}
if (!function_exists('get_financial_current_year')) {
    function get_financial_current_year(int $user_financial_year_start_month, int|null $current_year = null): int
    {
        if (is_null($current_year) || !$current_year || $current_year <= 0) {
            $current_year = date('Y');
        }
        if ($user_financial_year_start_month == 1) {
            $start_year = $current_year;
        } elseif (now()->month >= $user_financial_year_start_month) {
            $start_year = $current_year;
        } else {
            $start_year = $current_year - 1;
        }

        return $start_year;
    }
}
if (!function_exists('get_financial_year_range_string')) {
    function get_financial_year_range_string(int $user_financial_year_start_month, int|null $current_year = null): string
    {
        return get_financial_year_range($user_financial_year_start_month, $current_year)["year_start"]->format("Y") . "-" .  get_financial_year_range($user_financial_year_start_month, $current_year)["year_end"]->format("Y");
    }
}
if (!function_exists('get_interested_job_links')) {
    function get_interested_job_links(int $job_id, int $freelancer_id): array
    {
        $encrypted_job_id = encrypt($job_id);
        $encrypted_freelancer_id = encrypt($freelancer_id);
        $encrypted_freelancer_type = encrypt("live");
        $accept_href_link = url("/accept-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");
        $negotiate_href_link = url("/negotiate/freelancer-negotiate-on-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");
        $freeze_href_link = url("/freeze-job?job_id={$encrypted_job_id}&freelancer_id={$encrypted_freelancer_id}&freelancer_type={$encrypted_freelancer_type}");

        return [
            "accept_href_link" => $accept_href_link,
            "negotiate_href_link" => $negotiate_href_link,
            "freeze_href_link" => $freeze_href_link
        ];
    }
}
if (!function_exists('calculate_distance_for_job_search_freelancers')) {
    function calculate_distance_for_job_search_freelancers(User $freelancer, JobPost $job): float|null
    {
        $freelancer_town = SiteTown::query()->where("town", "like", "%{$freelancer->user_extra_info?->city}%")->first();
        $job_town = SiteTown::query()->where("town", "like", "%{$job->job_region}%")->first();

        $distanceHelper = new DistanceCalculateHelper();
        if ($freelancer_town && $job_town) {
            return $distanceHelper->getDistance($freelancer_town->lat, $freelancer_town->lon, $job_town->lat, $job_town->lon);
        }

        return null;
    }
}
if (!function_exists('compare_job_town_with_user_selected_towns')) {
    function compare_job_town_with_user_selected_towns(User $freelancer, JobPost $job): bool
    {
        $freelancer_towns = $freelancer->user_extra_info?->site_town_ids ? json_decode($freelancer->user_extra_info?->site_town_ids, true) : null;
        $freelancer_towns = $freelancer_towns && is_array($freelancer_towns) ? $freelancer_towns : [];
        $job_town = SiteTown::query()->where("town", "slike", "%{$job->job_region}%")->first();

        if ($job_town && in_array($job_town->id, $freelancer_towns)) {
            return true;
        }
        return false;
    }
}
if (!function_exists('send_test_mail')) {
    function send_test_mail($email = 'noumanhabib521@gmail.com')
    {
        $sentMessage = Mail::html('<p style="color: green;">Test is test, and test is here.</p>', function ($message) use ($email) {
            $message->to($email)->subject('Test');
        });
        if (app()->runningInConsole()) {
            $output = new ConsoleOutput();
            if ($sentMessage) {
                $output->writeln($sentMessage->getSymfonySentMessage()->getDebug());
            } else {
                $output->writeln('Not able to send message');
            }
        }
    }
}

if (!function_exists('is_valid_date')) {
    function is_valid_date(string $date, array $formats = ['Y-m-d'], bool $return = false): bool|Carbon
    {
        foreach ($formats as $format) {
            $parsedDate = Carbon::createFromFormat($format, $date);
            if ($parsedDate && $parsedDate->format($format) === $date) {
                return $return ? $parsedDate : true;
            }
        }
        return false;
    }
}

if (!function_exists('checkPermission')) {
    function checkPermission(string $permisssion): bool
    {
        return true;
        // return true;
       $userPermission=\App\Models\userAclPermisssion::where('permission',$permisssion)->first();
    //    if($userPermission){
       return auth()->user()->role->permissions()->where('permission',$permisssion)->exists();
       //}

    }
}