<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Nette\Utils\Random;
use Symfony\Component\HttpFoundation\Response;
use App\Notifications\SendResetTokenNotification;


class ChangePasswordController extends Controller
{

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|string|same:new_password',
        ]);
        
        try {
            $user = User::findOrFail($request->user_id);
            if(!Hash::check($request->current_password,$user->password)) {
                return response()->json([
                    'error' => "current password is not correct."
                    ], 500);
            }
            if(Hash::check($request->new_password, $user->password)) {
                return response()->json([
                    'error' => "current and new password is same"
                    ], 400);
            }

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'error' => 'User not found.',
            ], 404);
        }


        if(!$user) {
            throw ValidationException::withMessages([
                'user_id' => ['User Not Found'],
            ]);

        }
        
        // if (!Hash::check($request->current_password, $user->password)) {
        //     throw ValidationException::withMessages([
        //         'current_password' => ['The provided password does not match your current password.'],
        //     ]);
        // }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully.'], 
            200);
    }
    
    public function forgetPassword(Request $request)
    {
        try {
            $request->validate(['email' => 'required']);

            $user = User::whereEmail($request->email)->first();

            if (!$user) {
                return response()->json(['message' => 'This email is not registered.Please enter your registered email.'], Response::HTTP_BAD_REQUEST);
            }
            

            $otp = Random::generate(4, '0-9');

            DB::table('password_resets')->updateOrInsert(['email' => $user->email], ['token' => Hash::make($otp), 'created_at' => now()]);
            $user->notify(new SendResetTokenNotification($otp));

            return response()->json(['status' => true, 'otp' => $otp ,'message' => 'otp send.'], Response::HTTP_OK);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    public function verifyEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|min_digits:4|max_digits:4'
        ]);

        $user = User::whereEmail($request->email)->first();

        $hash = DB::table('password_resets')->where(['email' => $request->email])->where('created_at', '>=', Carbon::now()->subMinutes(20))->first();
        if ($hash) {

            if (Hash::check($request->otp, $hash->token)) {
                if (!$user->hasVerifiedEmail()) {
                    if ($user->markEmailAsVerified()) {
                        event(new Verified($user));
                    }
                }
                return response()->json(['message' => 'OTP verified', 'email' => $request->email, 'token' => Password::createToken($user)], 200);
            } else {
                return response()->json(['message' => 'Invalid Otp'], 422);
            }
        }

        return response()->json(['message' => 'Otp has been expired'], 422);
    }
    
    public function resendEmailOtp(Request $request)
    {
        $user = User::whereEmail($request->email)->first();
        if ($user) {
            $otp = $user->sendOtp();
            return response()->json(['message' => 'Otp sent', 'otp' => $otp], 200);
        }
        return response()->json(['message' => 'User not found'], 422);
    }
    
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $user->save();

                event(new PasswordReset($user));
            }
        );

        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password updated'], 200)
            : response()->json(['message' => 'Error occoured'],422);
    }
    
}
