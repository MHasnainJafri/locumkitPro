<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\AppNotificationHelper;
use App\Models\MobileNotification;
use App\Models\FinancialYear;
use App\Models\User;
use App\Models\UserAclPackage;
use App\Models\UserPackageDetail;
use App\Models\UserPaymentInfo;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;

class PackageController extends Controller
{
    
    public function getTokenByID($user_id)
    {
        $tokenID = MobileNotification::where("user_id", $user_id)->latest()->first();
        if ($tokenID) {
            return $tokenID->token_id;
        }
        return null;
    }
    
    public function managePackage(Request $request)
    {
        $notificationHelper = new AppNotificationHelper();
        $job_id = 0;
        $message = 'Package Update.';
        $title = 'Package Update';
        $user_id = $request['uid']; 
        $types = 'packageUpgrade';
        $token_id = $this->getTokenByID($request['uid']);
    
        $notificationHelper->notification($job_id, $message, $title, $user_id, $types, $token_id);
        $page_id = $request->input("page_id");
        return match ($page_id) {
            'package' => $this->updatePackage($request->all()),
            'manage-financial-year' => $this->manage_financial_year($request->all()),
            default => response()->error('Invalid request')
        };
    }

    //update payment information
    public function updatePackage($user_data)
    {
        $uid = isset($user_data['uid']) ? $user_data['uid'] : ''; //user id
        $pid = isset($user_data['pid']) ? $user_data['pid'] : ''; //package id
        $user = User::findOrFail($uid);
        $package = UserAclPackage::findOrFail($pid);
        $package_id = isset($user_data['payment_info']['intent']) ? $user_data['payment_info']['intent'] : '';
        $price = isset($user_data['payment_info']['amount']) ? $user_data['payment_info']['amount'] : '';
        $payment_data = isset($user_data['payment_info']['authorization_id']) ? $user_data['payment_info']['authorization_id'] : '';
        $payment_type = isset($user_data['payment_info']['payment_type']) ? $user_data['payment_info']['payment_type'] : 'paypal';
        UserPackageDetail::updateOrCreate([
            'user_id' => $uid,
            'package_id' => $pid,
        ], [
            'package_active_date' => today(),
            'package_expire_date' => today()->addMonth(),
            'package_status' => 3,
        ]);
        UserPaymentInfo::updateOrCreate([
            "user_acl_package_id" => $pid,
            "user_id" => $uid
        ], [
            "payment_type" => $payment_type,
            "payment_token" => $payment_data,
            "price" => $price,
            "payment_status" => 1,
        ]);
        $user->user_acl_package_id = $pid;
        $user->is_free = 0;
        $user->save();
        $header   = get_mail_header();
        $footer   = get_mail_footer();
        $freEmail   = $user['email'];
        $freName  = $user['firstname'] . ' ' . $user['lastname'];
        $mail_css   = $header;
        $massageFre = $mail_css . '
        <div style="padding: 25px 50px 5px;">
          <p>Hello <b>' . $freName . '</b>,</p>
          <p>Thank you...!</p><p> You successfully renewed your package and now you can enjoy all the facility of locumkit.</p>
        </div>' . $footer . '</body></html>';

        $massageAdm = $mail_css . '
        <div style="padding: 25px 50px 5px;">
          <p>Hello Admin</p>
          <p> <b>' . $freName . ' (' . $user->id . ')</b>, locum just renewed account please check the details in admin panel.</p>
        </div>' . $footer . '</body></html>';

        $adminEmail = config('app.admin_mail');
        try {
            Mail::html($massageAdm, function (Message $message) use ($adminEmail) {
                $message->to($adminEmail)->subject('Locumkit account renewed.');
            });
        } catch (Exception $e) {
        }
        try {
            Mail::html($massageFre, function (Message $message) use ($freEmail) {
                $message->to($freEmail)->subject('Locumkit account renew successfully.');
            });
        } catch (Exception $e) {
        }
        return response()->success([], 'Package updated');
    }

    //Insert and Update Financial year
    public function manage_financial_year($user_data)
    {
        $uid = isset($user_data['user_id']) ? $user_data['user_id'] : '';
        $managefinancialyear = isset($user_data['managefinancialyear']) ? $user_data['managefinancialyear'] : '';
        if (isset($managefinancialyear) && $managefinancialyear) {
            $fyid = isset($user_data['fyid']) ? $user_data['fyid'] : '';
            $fiusertype = isset($user_data['fiusertype']) ? $user_data['fiusertype'] : '';
            $user = FinancialYear::where("user_id", $uid)->first();
            if (isset($fiusertype) && $fiusertype == 'soletrader' && $user == 1) {
                $monthstart =  '4';
                $monthend = '3';
            } else {
                $monthstart =  $managefinancialyear;
                $monthend = $monthstart == 1 ? '12' : $monthstart - 1;
            }
            FinancialYear::updateOrCreate([
                "user_id" => $uid
            ], [
                "month_start" => $monthstart,
                "month_end" => $monthend,
                "user_type" => $fiusertype
            ]);

            return response()->success([], 'Financial year updated successfully');
        }
        return response()->error('Invalid request');
    }
}
