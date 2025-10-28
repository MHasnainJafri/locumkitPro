<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\MailGroupUser;
use App\Models\User;
use App\Models\UserAclPackageResource;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define('is_freelancer', function (User $user) {
            return $user->role?->name === "Locum";
        });
        Gate::define('is_employer', function (User $user) {
            return $user->role?->name === "Employer";
        });

        Gate::define('manage_finance', function (User $user) {
            $package_resorce_ids = json_decode($user->user_acl_package->user_acl_package_resources_ids_list) ?? [];
            $resource_count = UserAclPackageResource::where("resource_key", "finance")->whereIn("id", $package_resorce_ids)->count();
            return $resource_count > 0;
        });

        Gate::define('manage_feedback', function (User $user) {
            $package_resorce_ids = json_decode($user->user_acl_package->user_acl_package_resources_ids_list) ?? [];
            $resource_count = UserAclPackageResource::where("resource_key", "feedback")->whereIn("id", $package_resorce_ids)->count();
            return $resource_count > 0;
        });

        Gate::define('is_mail_group_admin', function (Authenticatable $user) {
            if (auth()->guard('mail_group_web')->check()) {
                return auth()->guard('mail_group_web')->user()->role === MailGroupUser::USER_ROLE_ADMIN;
            }
            return false;
        });
    }
}
