<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EmployerStoreList;
use App\Models\PkgPrivilegeInfo;
use App\Models\User;
use App\Models\UserAclPackage;
use App\Models\UserAclProfession;
use App\Models\UserAclRole;
use App\Models\UserAnswer;
use App\Models\UserExtraInfo;
use App\Models\UserPackageDetail;
use App\Models\UserPaymentInfo;
use App\Models\UserQuestion;
use Exception;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Nette\Utils\Random;
use App\Notifications\EmailVerficationApp;
use Illuminate\Support\Facades\DB;



class RegisterController extends Controller
{
    public function registerForm(Request $request)
    {
        $step = $request->input("step");
        $response = match (intval($step)) {
            1 => $this->get_form_field_setp_1(),
            3 => $this->get_form_field_setp_3($request->toArray()),
            4 => $this->checkUserRecordExist($request->toArray()),
            5 => $this->saveUserRecords($request->toArray()),
            default => null
        };
        if (is_null($response)) {
            return response()->error('Bad Request');
        }
        return $response;
    }

    public function get_form_field_setp_1()
    {
        $package = UserAclPackage::select("id", "name", "price", "description")->orderBy("id", "DESC")->first();
        $package_obj_array['package_list'][] = $package;

        $getAllPkgInfo = PkgPrivilegeInfo::all();
        foreach ($getAllPkgInfo as $key => $value) {
            $label = $value['label'];
            $bronze = $value['bronze'];
            $silver = $value['silver'];
            $gold = $value['gold'];
            if ($bronze == 1) {
                $package_obj_array['info']['bronze'][$key]['label'] = $label;
            }
            if ($silver == 1) {
                $package_obj_array['info']['silver'][$key]['label'] = $label;
            }
            if ($gold == 1) {
                $package_obj_array['info']['gold'][$key]['label'] = $label;
            }
        }
        $step_1_field = $package_obj_array;

        $step_1_field['role'] = UserAclRole::select("id", "name")->where("is_public", true)->get()->toArray();
        $step_1_field['profession'] = UserAclProfession::select("id", "name")->where("is_active", true)->get()->toArray();
        $step_1_field['payapal_api'] = [["value" => "ACK0LpCt36OqvfCM2B-45Xz0zObxAlaOg.QOzBOuBfkMrij"], ["value" => "live"]];
        return response()->success($step_1_field);
    }

    public function get_form_field_setp_3($form_step)
    {
        $role = isset($form_step['role']) ? $form_step['role'] : '';
        $profession = isset($form_step['profession']) ? $form_step['profession'] : '';
        if ($role != '' && $profession != '') {
            $quesData = [];
            if ($role == 3) {
                $quesData['questions'] = UserQuestion::where("employer_question", "!=", "")->where("user_acl_profession_id", $profession)->orderBy("sort_order")
                    ->select("id", "employer_question as q", "type as type_key", "values as type_value", "is_required as required_status", "range_type_unit", "range_type_condition")->get()->toArray();
                foreach ($quesData['questions'] as $key => $que) {
                    if ($que['type_value'] != '') {
                        $quesData['questions'][$key]['type_value'] = json_decode($que['type_value']);
                    }
                    if ($que['type_key'] == 5) {
                        $quesData['questions'][$key]['type_key'] = 3;
                    }
                }
            } elseif ($role == 2) {
                $quesData['questions'] = UserQuestion::where("freelancer_question", "!=", "")->where("user_acl_profession_id", $profession)->orderBy("sort_order")
                    ->select("id", "employer_question as q", "type as type_key", "values as type_value", "is_required as required_status", "range_type_unit", "range_type_condition")->get()->toArray();
                foreach ($quesData['questions'] as $key => $que) {
                    if ($que['type_value'] != '') {
                        $quesData['questions'][$key]['type_value'] = json_decode($que['type_value']);
                    }
                }
            }
            return response()->success($quesData);
        }
        return response()->error('Not found');
    }

    public function checkUserRecordExist($form_step)
    {
        $sql_check = 0;
        if (isset($form_step['email']) && $form_step['email'] != '') {
            $email = $form_step['email'];
            $sql_check = User::where("email", $email)->count();
        } else if (isset($form_step['username']) && $form_step['username'] != '') {
            $username = $form_step['username'];
            $sql_check = User::where("login", $username)->count();
        }
        if ($sql_check > 0) {
            return response()->json([
                'success' => true,
                'record_already_present' => true
            ]);
        }
        return response()->json([
            'success' => true,
            'record_already_present' => false
        ]);
    }

    /**
     * //CHECK: Employer request
     *  "request": {
        "user_type": {
            "role": "3",
            "profession": "3",
            "step": 3
        },
        "personal_info": {
            "freeSubscription": false,
            "opl": "OPL ",
            "cet": "",
            "role": "3",
            "profession": "3",
            "email": "noumanhabib332211@gmail.com",
            "firstname": "Employer",
            "lastname": "Test",
            "username": "test123",
            "password": "nouman00",
            "confirmPassword": "nouman00",
            "store_name": "Test",
            "address": "Hello test address 123",
            "town": "Rwp",
            "postcode": "10017",
            "telephone": "0123456789",
            "mobile_number": "01234567891",
            "min_rate": [],
            "store_info_ques": {
                "store_id_emp": "Boots"
            },
            "store_data_ques": {
                "Optical_express_data": "0-6 months"
            }
        },
        "store_info": {
            "name": "Test",
            "address": "Hello test address 123",
            "town": "Rwp",
            "postcode": "10017",
            "monday_start_time": "09:00",
            "monday_end_time": "17:30",
            "monday_lunch_time": "00:30",
            "tuesday_start_time": "09:00",
            "tuesday_end_time": "17:30",
            "tuesday_lunch_time": "00:30",
            "wednesday_start_time": "09:00",
            "wednesday_end_time": "17:30",
            "wednesday_lunch_time": "00:30",
            "thursday_start_time": "09:00",
            "thursday_end_time": "17:30",
            "thursday_lunch_time": "00:30",
            "friday_start_time": "09:00",
            "friday_end_time": "17:30",
            "friday_lunch_time": "00:30",
            "saturday_start_time": "09:00",
            "saturday_end_time": "17:30",
            "saturday_lunch_time": "00:30",
            "sunday_start_time": "09:00",
            "sunday_end_time": "17:30",
            "sunday_lunch_time": "00:30"
        },
        "ans_val": {
            "2": "0-1",
            "3": "10-15",
            "4": "10-15",
            "5": "Yes",
            "23": [
                "English",
                "Urdu"
            ],
            "24": "Basic Eye Test",
            "28": "Autorefractor",
            "31": "Basic IT usage"
        },
        "step": 5,
        "fudugo_key": "TG9jdW1raXQtQXBwLUZ1ZHVnby1EZXZlbG9wZXItU3VyYWotV2FzbmlrLTIwMTctYXdlc29tZQ==",
        "fudugo_password": "TG9jdW1raXRAbGV0bWVpbkZ1ZHVnb19BcHBAMjAxNyE=",
        "page": "register-form"
     },
     *
     * //CHECK: Locum request
     * {
        "user_type": {
            "role": "2",
            "profession": "3",
            "step": 3
        },
        "personal_info": {
            "freeSubscription": false,
            "opl": "OPL 12-34566\/A1",
            "cet": "123",
            "role": "2",
            "profession": "3",
            "email": "noumanhabib321@gmail.com",
            "firstname": "Test",
            "lastname": "Freelancer",
            "username": "test123",
            "password": "password",
            "confirmPassword": "password",
            "address": "Test address 123",
            "town": "Rawalpindi",
            "postcode": "10017",
            "mobile_number": "12345678910",
            "experience_store": {
                "Boots": true
            },
            "checkbx": "1",
            "max_distance": "5",
            "goc": "12-34566",
            "inshurance_company": "Test",
            "inshurance_no": "M-123456",
            "inshurance_renewal_date": "2022-12-30",
            "min_rate": {
                "Monday": "350",
                "Tuesday": "350",
                "Wednesday": "350",
                "Thursday": "350",
                "Friday": "350",
                "Saturday": "350",
                "Sunday": "350"
            },
            "store_info_ques": {
                "Boots": true
            },
            "store_data_ques": {
                "Optical_express_data": "0-6 months",
                "Boots_data": "7-12 months"
            }
        },
        "store_info": [],
        "payment_info": {
            "bnCode": "PhoneGap_SP",
            "amount": "0",
            "currency": "GBP",
            "shortDescription": "Free Subscription Package",
            "intent": "4",
            "payment_type": "paypal"
        },
        "ans_val": {
            "2": "0-1",
            "3": "10-15",
            "4": "10-15",
            "5": "No",
            "23": "English",
            "24": "Basic Eye Test",
            "28": "Autorefractor",
            "31": "Basic IT usage"
        },
        "step": 5,
        "fudugo_key": "TG9jdW1raXQtQXBwLUZ1ZHVnby1EZXZlbG9wZXItU3VyYWotV2FzbmlrLTIwMTctYXdlc29tZQ==",
        "fudugo_password": "TG9jdW1raXRAbGV0bWVpbkZ1ZHVnb19BcHBAMjAxNyE=",
        "page": "register-form"
     },
     *
     *
     */
     
    //  public function sendOtp()
    // {
    //     $otp = Random::generate(4, '0-9');
    //     dd($otp);
    //     try {
    //         $this->notify(new EmailVerficationApp($otp));
    //         DB::table('password_reset_tokens')->updateOrInsert(['email' => $this->email], ['token' => Hash::make($otp), 'created_at' => now()]);
    //         return $otp;
    //     } catch (\Exception $e) {
    //         return false;
    //     }
    // }

    public function saveUserRecords($form_step)
    {
        $already_check = User::where('login', $form_step['personal_info']['username'])->first();
        
        if ($already_check) {
            return response()->json([
                'message' => 'The username already exists.'
            ], 400);
        }

        $role_id = isset($form_step['user_type']['role']) ? $form_step['user_type']['role'] : ''; //role id
        $profession_id = $profession = isset($form_step['user_type']['profession']) ? $form_step['user_type']['profession'] : '';
        $fname = isset($form_step['personal_info']['firstname']) ? $form_step['personal_info']['firstname'] : '';
        $lname = isset($form_step['personal_info']['lastname']) ? $form_step['personal_info']['lastname'] : '';
        $login = isset($form_step['personal_info']['username']) ? $form_step['personal_info']['username'] : '';
        $email = isset($form_step['personal_info']['email']) ? $form_step['personal_info']['email'] : '';
        $company = isset($form_step['personal_info']['company_name']) ? $form_step['personal_info']['company_name'] : '';
        $store = isset($form_step['personal_info']['store_name']) ? $form_step['personal_info']['store_name'] : '';
        $address = isset($form_step['personal_info']['address']) ? $form_step['personal_info']['address'] : '';
        $city = isset($form_step['personal_info']['town']) ? str_replace("'", "", $form_step['personal_info']['town']) : '';
        $zip = isset($form_step['personal_info']['postcode']) ? $form_step['personal_info']['postcode'] : '';
        $password = Hash::make(isset($form_step['personal_info']['password']) ? $form_step['personal_info']['password'] : '');
        $telephone = isset($form_step['personal_info']['telephone']) ? $form_step['personal_info']['telephone'] : '';
        $mobile = isset($form_step['personal_info']['mobile_number']) ? $form_step['personal_info']['mobile_number'] : '';
        $aoc_id = isset($form_step['personal_info']['opl']) ? $form_step['personal_info']['opl'] : '';
        $minimum_rate = isset($form_step['personal_info']['min_rate']) ? json_encode($form_step['personal_info']['min_rate']) : '';
        $max_distance = isset($form_step['personal_info']['max_distance']) ? $form_step['personal_info']['max_distance'] : '';
        $cet = isset($form_step['personal_info']['cet']) ? $form_step['personal_info']['cet'] : '';
        $goc = isset($form_step['personal_info']['goc']) ? $form_step['personal_info']['goc'] : '';
        $aop = isset($form_step['personal_info']['aop']) ? $form_step['personal_info']['aop'] : '';
        $inshurance_company = isset($form_step['personal_info']['inshurance_company']) ? $form_step['personal_info']['inshurance_company'] : '';
        $inshurance_no = isset($form_step['personal_info']['inshurance_no']) ? $form_step['personal_info']['inshurance_no'] : '';
        $inshurance_renewal_date = isset($form_step['personal_info']['inshurance_renewal_date']) ? $form_step['personal_info']['inshurance_renewal_date'] : '';
        $ans_val = isset($form_step['ans_val']) ? $form_step['ans_val'] : '';
        $store_info = isset($form_step['store_info']) ? $form_step['store_info'] : '';
        $package_id = isset($form_step['payment_info']['intent']) ? $form_step['payment_info']['intent'] : '';
        $price = isset($form_step['payment_info']['amount']) ? number_format($form_step['payment_info']['amount'], 2) : '0';
        $payment_data = isset($form_step['payment_info']['authorization_id']) ? $form_step['payment_info']['authorization_id'] : '';
        $payment_type = isset($form_step['payment_info']['payment_type']) ? $form_step['payment_info']['payment_type'] : 'paypal';
        $store_id = isset($form_step['personal_info']['store_info_ques']) ? $form_step['personal_info']['store_info_ques'] : '';
        $store_data = isset($form_step['personal_info']['store_data_ques']) ? $form_step['personal_info']['store_data_ques'] : '';
        $store_value = '';
        $store_info_ques = array();
        $store_info_data_ques = array();
        $user_payment_data['payment_info'] = array(
            'authorization_id'     => $payment_data,
            'amount'             => $price,
            'payment_type'         => $payment_type,
            'intent'             => $package_id
        );
        if ($role_id == 2) {
            if (!empty($store_data)) {
                foreach ($store_data as $key => $value) {
                    if ($key != "") {
                        $store_info_data_ques[] = $value;
                    }
                }
                $store_info_data_ques = implode(",", $store_info_data_ques);
            }
            if (!empty($store_id)) {
                foreach ($store_id as $key => $value) {
                    if ($value == 1) {
                        $value_replaced = str_replace("_", " ", $key);
                        $store_info_ques[] = $value_replaced;
                    }
                }
                $store_info_ques = implode(",", $store_info_ques);
            }
        } else {
            $company = isset($form_step['personal_info']['store_name']) ? $form_step['personal_info']['store_name'] : '';
            $store_info_ques = $store_id;
            $store_info_ques = implode(",", $store_info_ques);
            $store_info_data_ques = '';
        }

        $user = new User();
        $user->firstname = $fname;
        $user->lastname = $lname;
        $user->email = $email;
        $user->login = $login;
        $user->password = $password;
        $user->user_acl_role_id = $role_id;
        $user->user_acl_profession_id = $profession;
        $user->user_acl_package_id = 4;
        $user->active = $role_id == User::USER_ROLE_EMPLOYER ? User::USER_STATUS_GUESTUSER : User::USER_STATUS_ACTIVE;
        
        $otp = random_int(100000, 999999); // Generate an OTP
        $hashedToken = Hash::make($otp);
        
        DB::table('password_resets')->updateOrInsert(
            ['email' => $user->email],
            ['token' => $hashedToken, 'created_at' => now()]
        );
        
        $user->notify(new EmailVerficationApp($otp, $user->email));
        
        
        $user->save();

        $userExtraInfo = new UserExtraInfo();
        $userExtraInfo->user_id = $user->id;
        $userExtraInfo->aoc_id = $aoc_id;
        $userExtraInfo->mobile = $mobile;
        $userExtraInfo->address = $address;
        $userExtraInfo->city = $city;
        $userExtraInfo->zip = $zip;
        $userExtraInfo->telephone = $telephone;
        $userExtraInfo->company = $company;
        $userExtraInfo->minimum_rate = $minimum_rate;
        $userExtraInfo->max_distance = $max_distance;
        $userExtraInfo->cet = $cet;
        $userExtraInfo->goc = $goc;
        $userExtraInfo->aop = $aop;
        $userExtraInfo->inshurance_company = $inshurance_company;
        $userExtraInfo->inshurance_no = $inshurance_no;
        $userExtraInfo->inshurance_renewal_date = $inshurance_renewal_date;
        $userExtraInfo->store_type_name = $store_info_ques;
        $userExtraInfo->save();

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
            $userAnswer = new UserAnswer;
            $userAnswer->user_id = $user->id;
            $userAnswer->user_question_id = $key;
            $userAnswer->type_value = $value;
            $userAnswer->save();
        }

        if ($role_id == 3) {
            //Save store info
            $storeName = isset($store_info['name']) ? $store_info['name'] : '';
            $storeAddress = isset($store_info['address']) ? $store_info['address'] : '';
            $storeTown = isset($store_info['town']) ? str_replace("'", "", $store_info['town']) : '';
            $storePostcode = isset($store_info['postcode']) ? $store_info['postcode'] : '';
            $store_start_time = array();
            $store_end_time = array();
            $store_lunch_time = array();

            foreach ($store_info as $key => $store_time) {
                if (strpos($key, '_start_time') !== false) {
                    $day = ucwords(str_replace('_start_time', '', $key));
                    $store_start_time[$day] =  $store_time;
                }
                if (strpos($key, '_end_time') !== false) {
                    $day = ucwords(str_replace('_end_time', '', $key));
                    $store_end_time[$day] = $store_time;
                }
                if (strpos($key, '_lunch_time') !== false) {
                    $day = ucwords(str_replace('_lunch_time', '', $key));
                    $store_lunch_time[$day] = str_replace('00:', '', $store_time);
                }
            }

            $store_start_time = json_encode($store_start_time);
            $store_end_time = json_encode($store_end_time);
            $store_lunch_time = json_encode($store_lunch_time);

            $employerStore = new EmployerStoreList();
            $employerStore->employer_id = $user->id;
            $employerStore->store_name = $storeName;
            $employerStore->store_address = $storeAddress;
            $employerStore->store_region = $storeTown;
            $employerStore->store_zip = $storePostcode;
            $employerStore->store_start_time = $store_start_time;
            $employerStore->store_end_time = $store_end_time;
            $employerStore->store_lunch_time = $store_lunch_time;
            $employerStore->save();
        }

        //send activation mail to users
        $header = get_mail_header();
        $footer = get_mail_footer();
        $sqlProval = "";
        if ($profession_id != '') {
            $sqlProval = UserAclProfession::select("name")->where("id", $profession_id)->first()->toArray();
        }
        if ($role_id == 2) {
            $package_expire_date = today()->addYear();
            if ($price == 0) {
                $package_expire_date = today()->addYears(2);
                $user->is_free = 0;
                $user->save();
            }
            UserPackageDetail::create([
                "user_id" => $user->id,
                "user_acl_package_id" => $package_id,
                "package_active_date" => today(),
                "package_expire_date" => $package_expire_date,
            ]);
            if ($price == 0) {
                $payment_data = 'Free Subscription';
                $payment_type = 'FREE';
            }
            UserPaymentInfo::create([
                "user_id" => $user->id,
                "user_acl_package_id" => $package_id,
                "payment_type" => $payment_type,
                "payment_token" => $payment_data,
                "price" => $price,
                "payment_status" => 1,
            ]);
            try {
                // event(new Registered($user));
            } catch (Exception $ignore) {
            }
        }
        if ($role_id == 3) {
            $message = $header . '
							<div style="padding: 25px 50px 5px; text-align: left;">
							<p>Hello Employer ' . $fname . ' ' . $lname . ',</p>
							<p>Thank you for joining Locumkit.</p><p> We have received your account application and your details are currently being verified by our team. Verification can take up to 48 hours and once this process is complete we will notify you.</p><p> Please note that during this process you will be unable to access any of the features of the site</p>
							</div>' . $footer;

            if ($profession_id != '') {
                $Professiontr = '<tr><th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Profession</th>
							<td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . isset($sqlProval['name']) ? $sqlProval['name'] : '' . '</td></tr>';
            } else {
                $Professiontr = '';
            }

            $message2 = $header . '
                <div style="padding: 25px 50px 30px; border-right: 2px solid #000; border-left: 2px solid #000;text-align: left; width:84.2%">
                    <p>Hello <b>Admin</b>,</p>
                    <p>A new employer account has been created which needs to be verified. The employer details are listed below:</p>
                    <table width="100%" style="border-collapse: collapse;  border: 1px solid black;  text-align:left;  padding:5px;">
                    <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Email</th>
                        <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $email . '</td>
                    </tr>
                    <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">First Name</th>
                        <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $fname . '</td>
                    </tr>
                    <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Last Name</th>
                        <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $lname . '</td>
                    </tr>
                    <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">ID</th>
                        <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $user->id . '</td>
                    </tr>' . $Professiontr . '
                    <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Store Name</th>
                        <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $company . '</td>
                    </tr>
                    <tr>
                        <th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Telephone number</th>
                        <td style=" border: 1px solid black;  text-align:left;  padding:5px;">' . $telephone . '</td>
                    </tr>

                    </table><br/>
                    <p><p>
                </div>
            ';
            $message2 .= $footer;
            try {
                Mail::html($message, function (Message $message) use ($email) {
                    $message->to($email)->subject('Welcome to LocumKit');
                });
                $admin_mail = config('app.admin_mail');
                Mail::html($message2, function (Message $message) use ($admin_mail) {
                    $message->to($admin_mail)->subject('New employer registration - Needs to be verified');
                });
            } catch (Exception $e) {
            }

            try {
                // event(new Registered($user));
            } catch (Exception $ignore) {
            }
        }
        $mobile_app_token = $user->createToken('mobile_app_token');

        $user_return_data = array(
            'id' => $user->id,
            'firstname' => $fname,
            'lastname' => $lname,
            'email' => $email,
            'role' => $role_id,
            'profession' => $profession,
            'mobile_app_token' => $mobile_app_token->plainTextToken
        );

        return response()->success($user_return_data);
    }
}
