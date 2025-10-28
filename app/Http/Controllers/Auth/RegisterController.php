<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\EmployerStoreList;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\UserAclPackage;
use App\Models\UserAclProfession;
use App\Models\UserAclRole;
use App\Models\UserQuestion;
use App\Models\UserAnswer;
use App\Models\UserExtraInfo;
use App\Models\UserPackageDetail;
use App\Models\UserPaymentInfo;
use Error;
use Exception;
use App\Notifications\NewRegisterAdminNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Throwable;
use UnhandledMatchError;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        $roles = UserAclRole::where("is_public", true)->get();
        $professions = UserAclProfession::where("is_active", true)->get();
        $questions = UserQuestion::where("is_active", true)->orderBy('sort_order', 'asc')->get();
        return view('auth.register', ['roles' => $roles, 'professions' => $professions, 'questions' => $questions]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $recaptcha = $request->input("g-recaptcha-response", "");
        /*  $url = "https://www.google.com/recaptcha/api/siteverify?secret=" . config('app.google_recaptcha_secret_key') . '&response=' . $recaptcha;
        try {
            $response = file_get_contents($url);
            $response = json_decode($response);
            if ($response->success == false) {
                return back()->with("error", "Captcha Validation Required!");
            }
        } catch (Throwable) {
        } */

        $role_id = $request->input('role');

        try {
            DB::transaction(function () use ($request, $role_id) {

                $profession_id  = $request->input('profession');
                $package_id  = $request->input('package_id');
                $fname = $request->input('fname');
                $lname = $request->input('lname');
                $login = $request->input('login');
                $password = $request->input('password');
                $email = $request->input('email');
                $company = $request->input('company');
                $address = $request->input('address');
                $city = str_replace("'", "", $request->input('city'));
                $zip = $request->input('zip');
                $date_of_birth = $request->input('dob');
                if ($date_of_birth) {
                    $date_of_birth = date('Y-m-d', strtotime($date_of_birth));
                } else {
                    $date_of_birth = null;
                }

                $telephone  = $request->input('telephone');
                $mobile  = $request->input('mobile');
                $package_final  = $request->input('package-final');
                $package_val  = $request->input('package_val');
                $gender  = $request->input('gender') ?? "";
                $role_pack  = 2;
                $no_role_pack  = 3;
                $answer_hash  = $request->input('answer_hash');
                $answer  = substr(sha1($request->input('answer')), 5, 10);
                $paymentAmount  = $request->input('PAYMENTREQUEST_0_AMT');
                $paypalMethod  = $request->input('paypal_method');
                if ($paypalMethod == 'direct_debit') {
                    $paytype = 'Billing';
                } else {
                    $paytype = 'Login';
                }
                $cet  = $request->get('cet');

                $min_rate = $request->get('min_rate');
                $minimum_rate = json_encode([]);
                if ($min_rate && is_array($min_rate) && sizeof($min_rate) === 7) {
                    $day_with_rate = array(
                        'Monday'     => $min_rate[0] ?? 0,
                        'Tuesday'     => $min_rate[1] ?? 0,
                        'Wednesday' => $min_rate[2] ?? 0,
                        'Thursday'     => $min_rate[3] ?? 0,
                        'Friday'     => $min_rate[4] ?? 0,
                        'Saturday'     => $min_rate[5] ?? 0,
                        'Sunday'     => $min_rate[6] ?? 0,
                    );
                    $minimum_rate = json_encode($day_with_rate);
                }

                $total_emp_stores = $request->input("total_emp_stores");

                $aoc_id            = $request->input('aoc_id') ?? "";
                $max_distance     = $request->input('max_distance') ?? "";
                $store_list     = $request->input('store_list') ?? [];
                if ($store_list && is_array($store_list) && sizeof($store_list) > 0) {
                    $store_list = json_encode($store_list);
                } else {
                    $store_list = json_encode([]);
                }
                $goc             = $request->input('goc') ?? "";
                $aop             = $request->input('aop') ?? "";
                $inshurance_company = $request->input('inshurance_company') ?? "";
                $inshurance_no     = $request->input('inshurance_no') ?? "";
                $inshurance_renewal_date = $request->input('inshurance_renewal_date') ?? "";
                $store_type_name = $request->input('store_id_emp') ?? "";

                $this->validator($request->all())->validate();

                //Upload profile image if any
                $profile_image = "";

                //Assign free package if no other present
                if (!in_array($package_id, [1, 2, 3])) {
                    $package_id = 4;
                    $package_final = 0;
                }

                //create new user
                $user = User::create([
                    'firstname' => $fname,
                    'lastname' => $lname,
                    'email' => $email,
                    'login' => $login,
                    'password' => Hash::make($password),
                    'user_acl_role_id' => $role_id,
                    'active' => $role_id == User::USER_ROLE_EMPLOYER ? User::USER_STATUS_GUESTUSER : User::USER_STATUS_ACTIVE,
                    'user_acl_profession_id' => $profession_id,
                    'user_acl_package_id' => $package_id,
                ]);
                $admin = User::where('user_acl_role_id', 1)->first();
                
                if ($admin) {
                    $admin->notify(new NewRegisterAdminNotification($user));
                }
                $emp_store_result = array();
                if ($total_emp_stores && is_array($total_emp_stores)) {
                    foreach ($total_emp_stores as $key => $store_key) {
                        $emp_start_time = $request->input('job_start_time_' . $store_key);
                        $emp_end_time = $request->input('job_end_time_' . $store_key);
                        $emp_lunch_time = $request->input('job_lunch_time_' . $store_key);
                        $emp_store_name = $request->input('emp_store_name_' . $store_key);
                        $emp_store_address = $request->input('emp_store_address_' . $store_key);
                        $emp_store_region = $request->input('emp_store_region_' . $store_key);
                        $emp_store_zip = $request->input('emp_store_zip_' . $store_key);

                        $emp_store_result[] = array(
                            'employer_id' => $user->id,
                            'store_name'    => $emp_store_name,
                            'store_address' => $emp_store_address,
                            'store_region'  => $emp_store_region,
                            'store_zip'     => $emp_store_zip,
                            'store_start_time' => json_encode($emp_start_time),
                            'store_end_time'   => json_encode($emp_end_time),
                            'store_lunch_time' => json_encode($emp_lunch_time),
                            'created_at' => now(),
                            'updated_at' => now()
                        );
                    }
                }

                if ($role_id == $role_pack) {

                    UserExtraInfo::create([
                        "user_id" => $user->id,
                        "aoc_id" => $aoc_id,
                        "gender" => $gender,
                        "dob" => $date_of_birth,
                        "mobile" => $mobile,
                        "address" => $address,
                        "city" => $city,
                        "zip" => $zip,
                        "telephone" => $telephone,
                        "company" => $company,
                        "profile_image" => $profile_image,
                        "max_distance" => $max_distance,
                        "minimum_rate" => $minimum_rate,
                        "site_town_ids" => $store_list,
                        "cet" => $cet,
                        "goc" => $goc,
                        "aop" => $aop,
                        "inshurance_company" => $inshurance_company,
                        "inshurance_no" => $inshurance_no,
                        "inshurance_renewal_date" => $inshurance_renewal_date,
                        "store_type_name" => $store_type_name
                    ]);

                    $pkg_active_date = now()->format("Y-m-d");
                    $pkg_expire_date = now()->addDays(90)->format("Y-m-d");

                    UserPackageDetail::create([
                        "user_id" => $user->id,
                        "user_acl_package_id" => $package_id,
                        "package_active_date" => $pkg_active_date,
                        "package_expire_date" => $pkg_expire_date,
                    ]);
                } else {
                    UserExtraInfo::create([
                        "user_id" => $user->id,
                        "gender" => $gender,
                        "dob" => $date_of_birth,
                        "mobile" => $mobile,
                        "address" => $address,
                        "city" => $city,
                        "zip" => $zip,
                        "telephone" => $telephone,
                        "company" => $company,
                        "profile_image" => $profile_image,
                    ]);
                    if (sizeof($emp_store_result) > 0) {
                        EmployerStoreList::insert($emp_store_result);
                    }
                }

                //Save question answers present in database
                $question_ids = $request->input("question_id");
                if ($request && is_array($question_ids) && sizeof($question_ids) > 0) {
                    $answer_inserted_records = array();
                    foreach ($question_ids as $question_id) {
                        $value = $request->input("ans_val_for_question_id_{$question_id}") ?? "";
                        if ($value && is_array($value)) {
                            $value = json_encode($value);
                        }
                        $answer_inserted_records[] = [
                            "user_id" => $user->id,
                            "user_question_id" => $question_id,
                            "type_value" => $value,
                        ];
                    }
                    UserAnswer::insert($answer_inserted_records);
                }

                if ($role_id == 2) {

                    UserPaymentInfo::create([
                        "user_id" => $user->id,
                        "user_acl_package_id" => $package_id,
                        "payment_type" => "FREE",
                        "price" => $package_final,
                        "payment_status" => 1,
                    ]);
                    try {
                        event(new Registered($user));
                    } catch (Exception $ignore) {
                    }
                    $this->guard()->login($user);
                }

                if ($role_id == 3) {
                    $header   = get_mail_header();
                    $footer   = get_mail_footer();
                    $message = $header . '
                                <div style="padding: 25px 50px 5px; text-align: left;">
                                <p>Hello ' . $fname . ' ' . $lname . ',</p>
                                        <p>Thank you for joining Locumkit.</p><p> We have received your account application and your details are currently being verified by our team. Verification can take up to 48 hours and once this process is complete we will notify you.</p><p> Please note that during this process you will be unable to access any of the features of the site</p></div>
                    ';
                    $message .= $footer;

                    if ($profession_id != '') {
                        $Professiontr = '<tr><th style=" border: 1px solid black;  text-align:left;  padding:5px; width: 200px;">Profession</th>
                                <td style=" border: 1px solid black;  text-align:left;  padding:5px;"> </td></tr>';
                    } else {
                        $Professiontr = '';
                    }


                    $message2 = $header . '
                                <div style="padding: 25px 50px 5px; text-align: left; width:84.2%">
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
                                </div>' . $footer;
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
                        event(new Registered($user));
                    } catch (Exception $ignore) {
                    }
                    $this->guard()->login($user);
                }
            });
        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            return back()->with("error", $e->getMessage());
        }
        if ($role_id == 2) {
            return redirect("/thank-you?type=freelancer");
        }

        return redirect("/thank-you?type=employer");
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'fname' => ['required', 'string', 'max:255'],
            'role' => ['required', 'integer'],
            'lname' => ['required', 'string', 'max:255'],
            'telephone' => ['required_if:role,3', 'nullable', 'string', 'min:11', 'max:255'],
            'mobile' => ['required_if:role,2', 'nullable', 'string', 'min:11', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'login' => ['required', 'string', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        throw new Error("Don't use this create method.");
    }
}
