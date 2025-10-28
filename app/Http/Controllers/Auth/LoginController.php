<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LastLoginUser;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username(): string
    {
        return 'login';
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    {
        $user = User::where("email", $request->input($this->username()))->orWhere("login", $request->input($this->username()))->first();
        
        if($user == null) {
            return false;
        }


        if ($user && $user->active == 5) {
            //User already deleted
            return false;
        }
        if ($user->active == User::USER_STATUS_DISABLE || $user->active == User::USER_STATUS_BLOCK) {
            return response(['User Access is Blocked or Disabled'], 403);
        }


        // if ($user && !in_array(intval($user->user_acl_role_id), [User::USER_ROLE_LOCUM, User::USER_ROLE_EMPLOYER])) {
        //     //User is not authroized to login into this system. Need to login at admin.locumkit.com
        //     return false;
        // }

        if (filter_var($request->input($this->username()), FILTER_VALIDATE_EMAIL)) {
            return $this->guard()->attempt(
                [
                    "email" => $request->input($this->username()),
                    "password" => $request->input("password"),
                ],
                $request->boolean('remember')
            );
        }

        return $this->guard()->attempt(
            $this->credentials($request),
            $request->boolean('remember')
        );
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function authenticated(Request $request, $user)
    {
        if ($user != null && isset($user->id) && $user->id) {
            //Enter value inside last_login_users table
           LastLoginUser::updateOrCreate(
                ['user_id' => $user->id], // Conditions to find the existing record
                [
                    'login_time' => now(),
                    'ip_address' => $request->ip(),
                ]
            );

        }
        if ($request->session()->has('url.intended')) {
            // Get the intended URL and remove it from the session
            $intendedUrl = $request->session()->pull('url.intended');
            // Redirect the user to the intended URL
            return redirect($intendedUrl);
        }
        if ($user && $user->user_acl_role_id == User::USER_ROLE_LOCUM) {
            return redirect(RouteServiceProvider::FREELANCER_DASHBOARD);
        } else if ($user && $user->user_acl_role_id == User::USER_ROLE_EMPLOYER) {
            return redirect(RouteServiceProvider::EMPLOYER_DASHBOARD);
        } else {
            // dd(config('app.admin_app_url'));
            return redirect(config('app.admin_app_url'));
        }
    }
    
    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        throw \Illuminate\Validation\ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);
    }
}
