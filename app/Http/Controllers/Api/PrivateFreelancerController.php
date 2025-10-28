<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PrivateUserResource;
use App\Models\PrivateUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PrivateFreelancerController extends Controller
{
    public function addPrivateFreelancer(Request $request)
    {
        $user_id = $request->user()->id;
        $private_user_name = $request->input('private_freelancer_name');
        $private_user_email = $request->input('private_freelancer_email');
        $private_user_mobile = $request->input('private_freelancer_mobile');
        $validator = Validator::make($request->all(), [
            'private_freelancer_name' => 'required|string',
            'private_freelancer_email' => 'required|email|string',
            'private_freelancer_mobile' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->error("Wrong inputs are given", 400, $validator->messages()->toArray());
        }

        if (validate_private_user_mail($private_user_email, $user_id)) {
            $record = PrivateUser::where("email", $private_user_email)->where("employer_id", $user_id)->where("status", "!=", 2)->first();
            if ($record) {
                $record->name = $private_user_name;
                $record->mobile = $private_user_mobile;
                $record->status = 0;
                $record->save();
            } else {
                $record = new PrivateUser();
                $record->employer_id = $user_id;
                $record->name = $private_user_name;
                $record->email = $private_user_email;
                $record->mobile = $private_user_mobile;
                $record->status = 0;
                $record->save();
            }

            return response()->success((new PrivateUserResource($record))->jsonSerialize(), 'New private freelancer added successfully');
        }
        return response()->error("Record with same email already exists. Please receck your inputs");
    }

    public function deletePrivateFreelancer(Request $request)
    {
        $employer_id = $request->user()->id;
        $validator = Validator::make($request->all(), [
            'private_freelancer_id' => [
                'required',
                'integer',
                Rule::exists('private_users', 'id')->where(function ($query) use ($employer_id) {
                    $query->where('employer_id', $employer_id);
                }),
            ],
        ]);
        if ($validator->fails()) {
            return response()->error("Private user not found", 400, $validator->messages()->toArray());
        }
        $private_freelancer_id = $request->input("private_freelancer_id");
        PrivateUser::where("id", $private_freelancer_id)->update([
            "status" => 2
        ]);
        $private_users = PrivateUser::where("employer_id", $employer_id)->where("status", "!=", 2)->get();
        return response()->success(PrivateUserResource::collection($private_users)->jsonSerialize(), "Private user deleted successfully");
    }
}
