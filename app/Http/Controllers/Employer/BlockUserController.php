<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\BlockUser;
use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockUserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified', 'can:is_employer', 'is_employer_active']);
    }

    public function index(Request $request)
    {
        $block_locums = BlockUser::where("employer_id", Auth::user()->id)->get();
        return view("employer.block-freelancer-index", compact("block_locums"));
    }

    public function blockUser(Request $request)
    {
        try {
            $employer_id = decrypt($request->query("employer_id"));
            $freelancer_id = decrypt($request->query("freelancer_id"));
        } catch (DecryptException $e) {
            return abort(404);
        }
        if ($employer_id != Auth::user()->id) {
            return abort(404);
        }
        $freelancer = User::findOrFail($freelancer_id);

        return view('employer.block-user', compact('freelancer'));
    }

    public function blockUserPost($id)
    {
        $freelancer = User::findOrFail($id);

        $count = BlockUser::where("freelancer_id", $freelancer->id)->where("employer_id", Auth::user()->id)->count();
        if ($count > 0) {
            return redirect(route('employer.dashboard'))->with("error", "User already in blocked list");
        }
        BlockUser::create([
            "freelancer_id" => $freelancer->id,
            "employer_id" => Auth::user()->id
        ]);

        return redirect(route('employer.dashboard'))->with("success", "User blocked successfully");
    }
}