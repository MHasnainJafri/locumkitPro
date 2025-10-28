<?php

namespace App\Jobs;

use App\Helpers\JobMailHelper;
use App\Helpers\PackageUpgradeHelper;
use App\Models\UserPackageDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class CronPackageStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $mailController = new JobMailHelper();
        $packageHelper = new PackageUpgradeHelper();

        $packages = UserPackageDetail::with("user")->where(function ($q) {
            $q->where("package_expire_date", today()->addDay())->orWhere("package_expire_date", today()->addDays(7));
        })->where("package_status", 1)->get();

        foreach ($packages as  $package) {
            $encrypted_user_id = encrypt($package->user_id);
            $encrypted_package_id = encrypt($package['user_acl_package_id']);
            $packageExpiryDate = $package['package_expire_date'];
            $urlHrefLink = url("/upgrade-package?user_id={$encrypted_user_id}&user_acl_package_id={$encrypted_package_id}");
            $btnLink = '<a href="' . $urlHrefLink . '" style="padding: 8px 15px; font-size: 16px; font-weight: 700; background: #00A9E0; color: #fff;     text-transform: uppercase; width: 200px; display: block; text-align: center;">Upgrade account</a>';
            if (today()->addDay()->equalTo($packageExpiryDate)) {
                $mailController->sendPackageExpiredMail($package->user, $package, $btnLink, 1);
            } else {
                $mailController->sendPackageExpiredMail($package->user, $package, $btnLink, 7);
            }
        }
        //If package is expired just suspend the account and notify the user.
        $packageHelper->updatePackageDetailsStatusOnCron();
    }
}
