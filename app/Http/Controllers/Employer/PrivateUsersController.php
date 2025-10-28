<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\PrivateUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivateUsersController extends Controller
{
    public function storePrivateUsers(Request $request)
    {
       $request->validate([
        "private_user_name" => "required|array|min:1",
        "private_user_name.*" => "required|string|max:100",
        "private_user_email" => "required|array|min:1",
        "private_user_email.*" => [
            "required",
            "email",
            "regex:/^[a-zA-Z0-9._%+-]+(\+[a-zA-Z0-9._%+-]+)?@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/",
            "max:100",
        ],
        "private_user_mobile" => "required|array|min:1",
        "private_user_mobile.*" => [
            "required",
            "regex:/^\+?[0-9]{10,15}$/", // Validate mobile number format
            "max:15",
        ],
    ], [
        // Custom messages for names
        "private_user_name.required" => "The user name field is required.",
        "private_user_name.*.required" => "Each user name is required.",
        "private_user_name.*.string" => "Each user name must be a valid string.",
        "private_user_name.*.max" => "Each user name must not exceed 100 characters.",
        
        // Custom messages for emails
        "private_user_email.required" => "The email field is required.",
        "private_user_email.*.required" => "Each email address is required.",
        "private_user_email.*.email" => "Please enter a valid email address.",
        "private_user_email.*.regex" => "Please enter a valid email with a proper domain.",
        "private_user_email.*.max" => "Each email address must not exceed 100 characters.",
        
        // Custom messages for mobile numbers
        "private_user_mobile.required" => "The mobile number field is required.",
        "private_user_mobile.*.required" => "Each mobile number is required.",
        "private_user_mobile.*.regex" => "Each mobile number must be 10 to 15 digits.",
        "private_user_mobile.*.max" => "Each mobile number must not exceed 15 characters.",
    ]);


        $private_user_names = $request->input("private_user_name");
        $private_user_emails = $request->input("private_user_email");
        $private_user_mobiles = $request->input("private_user_mobile");

        if (sizeof($private_user_names) != sizeof($private_user_emails) || sizeof($private_user_emails) != sizeof($private_user_mobiles)) {
            return back()->with("error", "Please fill all the fields");
        }
        if (sizeof(array_unique($private_user_emails)) != sizeof($private_user_emails)) {
            return back()->with("error", "You have duplicate emails. Please enter correct records and try again.");
        }
        $already_present_records = PrivateUser::where("employer_id", Auth::user()->id)->whereIn("email", $private_user_emails)->get();
        if ($already_present_records->count() > 0) {
            $already_present_emails = 'Emails ';
            foreach ($already_present_records as $record) {
                $already_present_emails .= $record->email . ', ';
            }
            $already_present_emails .= 'are already present.';
            return back()->with("error", $already_present_emails);
        }

        $data = array();
        foreach ($private_user_emails as $key => $user_email) {
            $data[] = [
                "employer_id" => Auth::user()->id,
                "name" => $private_user_names[$key],
                "email" => $user_email,
                "mobile" => $private_user_mobiles[$key],
                "status" => "0",
                "created_at" => now(),
                "updated_at" => now()
            ];
        }

        PrivateUser::insert($data);

        return back()->with("success", "Private users addedd successfully");
    }

    public function deletePrivateUsers($id)
    {
        $user = PrivateUser::where("employer_id", Auth::user()->id)->where("id", $id)->first();
        if ($user) {
            $user->status = '2';
            $user->save();
            return back()->with("success", "User deleted successfully");
        }
        return back()->with("error", "User not found");
    }
}