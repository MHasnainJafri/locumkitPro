<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\FinancialYear;
use App\Models\LastLoginUser;
use App\Models\MobileNotification;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        try {
            $request->validate([
                "email" => "required|email",
                "password" => "required"
            ]);
        } catch (Exception) {
            return response()->error("Please enter valid details.");
        }
        $login = $request->input('email');
        $token = $request->input('token');
        $password = $request->input('password');
        $user = User::where("email", $login)->orWhere("login", $login)->first();
        
        if(!$user)
        {
            return response()->error("No user found with this email address");
        }
        
        if($user->email_verified_at == Null)
        {
            return response()->error("Please verify email address.");
        }
        

        if ($user && Hash::check($password, $user->password)) {
            $membership_count = 1;
            if ($user->user_package_detail) {
                $membership_count = $user->user_package_detail->where("package_status", 1)->whereDate("package_expire_date", ">", now())->count();
            }
            $is_expired = 0;
            if ($membership_count <= 0 && $user['user_acl_role_id'] == 2) {
                $is_expired = 1;
            }
            if ($is_expired == 0 || $user['is_free'] == 0) {
                switch (intval($user['active'])) {
                    case 0:
                        if ($user['user_acl_role_id'] == 3) {
                            return response()->error("Your profile is under verification process and it will take around 48 hours from the time of registration.");
                        } else {
                            return response()->error("Please verify email address.");
                        }
                        break;
                    case 3:
                        return response()->error("Please complete payment through website.");
                        break;
                    case 5:
                        return response()->error("Account is disabled.");
                        break;
                }
                if ($token) {
                    MobileNotification::where("token_id", $token)->delete();
                    MobileNotification::where("user_id", $user->id)->delete();
                    MobileNotification::create([
                        "user_id" => $user->id,
                        "token_id" => $token,
                        "status" => 0
                    ]);
                }
                $user->tokens()->delete();

                $mobile_app_token = $user->createToken('mobile_app_token');

                $user['financial_year'] = FinancialYear::where("user_id", $user->id)->first();
                $user['mobile_app_token'] = $mobile_app_token->plainTextToken;

                return response()->success((new UserResource($user))->jsonSerialize());
            }
            return response()->error("Your package is expired.Please upgrade it from website.");
        } else {
            return response()->error("Please enter valid details.");
        }
    }

    public function updateSession(Request $request)
    {
        try {
            $request->validate([
                "email" => "required",
                "password" => "required"
            ]);
        } catch (Exception) {
            return response('0');
        }
        $login = $request->input('email');
        $password = $request->input('password');
        $token = $request->input("token");

        $user = User::where("email", $login)->orWhere("login", $login)->first();

        if ($user && $user->password == $password) {
            //Please check Package is expire or not start
            $membership_count = 1;
            if ($user->user_package_detail) {
                $membership_count = $user->user_package_detail->where("package_status", 1)->whereDate("package_expire_date", ">", now())->count();
            }
            $is_expired = 0;
            if ($membership_count <= 0 && $user['user_acl_role_id'] == 2) {
                $is_expired = 1;
            }
            //Please check Package is expire or not end
            if ($is_expired == 0 || $user['is_free'] == 0) {

                switch ($user['active']) {
                    case '0':
                        return response("0");
                        break;
                    case '3':
                        return response("0");
                        break;
                    case '5':
                        return response("Account is disabled.");
                        break;
                    default:
                        if ($token) {
                            //Check if token already saved
                            $tokens_present_count = MobileNotification::where("token_id", $token)->where("user_id", $user->id)->count();
                            if ($tokens_present_count == 0) {
                                MobileNotification::where("token_id", $token)->delete();
                                MobileNotification::where("user_id", $user->id)->delete();
                                MobileNotification::create([
                                    "user_id" => $user->id,
                                    "token_id" => $token,
                                    "status" => 0
                                ]);
                            }
                        }
                        LastLoginUser::updateOrCreate([
                            "user_id" => $user->id,
                            "ip_address" => $request->ip(),
                        ], [
                            "login_time" => now(),
                        ]);
                        $user['financial_year'] = FinancialYear::where("user_id", $user->id)->first();
                        return response((new UserResource($user))->toJson());
                }
            } else {
                return response("0");
            }
        } else {
            return response("0");
        }
    }

    public function isProfileCompleted(Request $request)
    {
        $user = $request->user();
        if ($user) {
            if ($user->isUserProfileCompleted()) {
                return response()->json([
                    'success' => true,
                    'is_profile_completed' => true
                ]);
            }
            return response()->json([
                'success' => true,
                'is_profile_completed' => false
            ]);
        }
        return response()->error('User not found');
    }

    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->tokens()->delete();
        }
        $token = $request->input("token");
        if ($token) {
            MobileNotification::where("token_id", $token)->delete();
        }
        return response()->success();
    }
}
