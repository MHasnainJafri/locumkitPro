<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserAclPackage;
use App\Models\UserPackageDetail;
use App\Models\UserPaymentInfo;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackageMembershipController extends Controller
{
    public function index(Request $request)
    {
        $user = null;
        if (Auth::check() == false) {
            try {
                if ($request->query("user_id")) {
                    $user_id  = decrypt($request->query("user_id"));
                    $user = User::findOrFail($user_id);
                }
            } catch (DecryptException $e) {
                return abort(404);
            }
        } else {
            $user = Auth::user();
        }
        if (is_null($user)) {
            return abort(404);
        }
        if ($request->isMethod("GET")) {
            $user_packages = UserAclPackage::all();
            $pre_package_id = $user->user_acl_package_id;
            return view('shared.upgrade-account', compact('user_packages', 'pre_package_id'));
        } else if ($request->isMethod("POST")) {
            $request->validate([
                "package_id" => "required|integer"
            ]);
            $package_id = $request->input("package_id");
            $user_acl_package = UserAclPackage::findOrFail($package_id);
            if ($user->user_acl_package_id == $package_id) {
                return back()->with("error", "You cannot upgrade to same package");
            }
            $amount = floatval($user_acl_package->price);
            if ($amount <= 0) {
                return $this->applyFreePackage($user);
            }

            return view("shared.paypal", compact('amount', 'user_acl_package'));
        } else if ($request->isMethod('PUT')) {
            return $this->paypalOrderCompleted($request, $user);
        } else {
            return abort(404);
        }
    }

    public function paypalOrderCompleted(Request $request, User $user)
    {
        $transaction_details = $request->input("transaction_details");
        $package_id = $request->input("package_id");
        $transaction_details = json_decode($transaction_details, true);
        $user_package_detail = $user->user_package_detail;
        $pkg_active_date = now()->format("Y-m-d");
        $pkg_expire_date = now()->addYear()->format("Y-m-d");
        if ($user_package_detail) {
            $user_package_detail->user_acl_package_id = $package_id;
            $user_package_detail->package_active_date = $pkg_active_date;
            $user_package_detail->package_expire_date = $pkg_expire_date;
            $user_package_detail->save();
        } else {
            UserPackageDetail::create([
                "user_id" => $user->id,
                "user_acl_package_id" => $package_id,
                "package_active_date" => $pkg_active_date,
                "package_expire_date" => $pkg_expire_date,
            ]);
        }
        $user_payment_info = UserPaymentInfo::where("user_id", $user->id)->first();
        if ($user_payment_info) {
            $user_payment_info->user_acl_package_id = $package_id;
            $user_payment_info->payment_type = "PAID";
            $user_payment_info->price = $transaction_details["purchase_units"][0]["amount"]["value"];
            $user_payment_info->payment_status = 1;
            $user_payment_info->payment_token = $transaction_details["id"];
            $user_payment_info->save();
        } else {
            UserPaymentInfo::create([
                "user_id" => $user->id,
                "user_acl_package_id" => $package_id,
                "payment_type" => "PAID",
                "price" => $transaction_details["purchase_units"][0]["amount"]["value"],
                "payment_status" => 1,
                "payment_token" => $transaction_details["id"],
            ]);
        }

        $user->user_acl_package_id = $package_id;
        $user->save();

        return redirect("/")->with("success", "Package upgraded successfully");
    }

    public function applyFreePackage(User $user)
    {
        return back()->with("error", "You cannot upgrade to free package");
    }
}