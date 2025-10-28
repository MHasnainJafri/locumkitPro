<?php

namespace App\Helpers;

use App\Models\User;
use App\Models\UserPackageDetail;
use App\Models\UserPaymentInfo;

class PackageUpgradeHelper
{
    public function insertPaymentInfo($uid, $pkgId, $pkgPrice, $token)
    {
        $paymentInfo = UserPaymentInfo::create([
            "user_id" => $uid,
            "user_acl_package_id" => $pkgId,
            "payment_type" => 'paypal',
            "price" => $pkgPrice,
            "payment_status" => 0,
            "payment_token" => $token
        ]);
        $_SESSION['last_payment_insert_id'] = $paymentInfo->id;
    }

    /* Update payment status to 1 after payment done */
    public function updatePaymentInfo($pid, $adapter)
    {
        $currentDate = date("Y-m-d H:i:s");
        $sqlPaymentInfo = "UPDATE user_payment_info SET payment_status = 1 WHERE pid = '$pid'";
        $paymentInfo = $adapter->query($sqlPaymentInfo, $adapter::QUERY_MODE_EXECUTE);
    }


    public function getExpiryDate($uid, $pid, $adapter)
    {
        $sqlPkg = "SELECT package_expire_date FROM user_package_details WHERE user_id = '$uid'  ORDER BY pid DESC";
        $pkgDetails = $adapter->query($sqlPkg, $adapter::QUERY_MODE_EXECUTE); //print_r($storeDetails);
        return $pkgRecord = (array)$pkgDetails->current();
    }

    /* Insert package details */
    public function insertPkgDetails($uid, $pkgId, $activeDate, $expireDate, $adapter)
    {
        $sqlPkgInfo = "INSERT INTO user_package_details (user_id,package_id,package_active_date,package_expire_date,package_status) VALUES ('$uid','$pkgId','$activeDate','$expireDate','0')";
        $pkgInfo = $adapter->query($sqlPkgInfo, $adapter::QUERY_MODE_EXECUTE);
        $_SESSION['last_pkg_details_info_id'] = $pkgInfo->getGeneratedValue();
    }
    /* Update payment status to 1 after payment done */
    public function updatePackageInfo($pid, $adapter)
    {
        $currentDate = date("Y-m-d");
        $sqlPkgExpired = "SELECT package_expire_date FROM user_package_details WHERE pid = '$pid' ORDER BY pid DESC";
        $pkgExpired = $adapter->query($sqlPkgExpired, $adapter::QUERY_MODE_EXECUTE); //print_r($storeDetails);
        $pkgExpiredRecord = (array)$pkgExpired->current();
        if ($currentDate <= $pkgExpiredRecord['package_expire_date']) {
            echo "I am here";
            $sqlPkgInfo = "UPDATE user_package_details SET package_status = 2 WHERE pid = '$pid'";
            $paymentInfo = $adapter->query($sqlPkgInfo, $adapter::QUERY_MODE_EXECUTE);
        } else {
            $sqlPkgInfo = "UPDATE user_package_details SET package_status = 1 WHERE pid = '$pid' AND package_active_date < $currentDate ";
            $paymentInfo = $adapter->query($sqlPkgInfo, $adapter::QUERY_MODE_EXECUTE);
        }
    }
    /* Update payment status to 1 after payment done for package change */
    public function updateChangePackageInfo($pid, $adapter)
    {
        $currentDate = date("Y-m-d");
        $sqlPkgExpired = "SELECT package_expire_date,package_id,user_id FROM user_package_details WHERE pid = '$pid' ORDER BY pid DESC";
        $pkgExpired = $adapter->query($sqlPkgExpired, $adapter::QUERY_MODE_EXECUTE); //print_r($storeDetails);
        $pkgExpiredRecord = (array)$pkgExpired->current();
        $package_id = $pkgExpiredRecord['package_id'];
        $user_id = $pkgExpiredRecord['user_id'];
        if ($currentDate < $pkgExpiredRecord['package_expire_date']) {
            $sqlPkgInfo = "UPDATE user_package_details SET package_status = 1 WHERE pid = '$pid'";
            $paymentInfo = $adapter->query($sqlPkgInfo, $adapter::QUERY_MODE_EXECUTE);

            // update user table for new package id
            $sqlUserInfo = "UPDATE user SET user_acl_package_id = '$package_id' WHERE id = '$user_id'";
            $userInfo = $adapter->query($sqlUserInfo, $adapter::QUERY_MODE_EXECUTE);
            $_SESSION['user_package_id'] = $package_id; // from 20/06/17
            //print_r($userInfo); die();
        }
    }
    /* Upadete package on active date via cron */
    public function updatePackageDetailsStatusOnCron()
    {
        $mail_helper = new JobMailHelper();
        $pkgExpiredRecord = UserPackageDetail::where("package_status", "!=", 3)->whereDate("package_expire_date", now()->subDay())->get();
        $user_ids = [];
        $package_detail_ids = [];
        foreach ($pkgExpiredRecord as $value) {
            $user_ids[] = $value->user_id;
            $package_detail_ids[] = $value->id;
            $mail_helper->sendMembershipExpired($value->user_id);
        }
        User::whereIn("id", $user_ids)->update([
            "active" => User::USER_STATUS_EXPIRED
        ]);
        UserPackageDetail::whereIn("id", $package_detail_ids)->update([
            "package_status" => 3
        ]);
    }
}