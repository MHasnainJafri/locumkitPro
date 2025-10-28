<?php

namespace App\Providers;

use App\Mail\Transport\MicrosoftOutlookMailTransport;
use App\Models\JobInvitedUser;
use App\Models\PrivateUser;
use App\Models\User;
use App\Models\UserAclPackageResource;
use Illuminate\Console\Command;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Response;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Output\ConsoleOutput;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrapFour();
        Relation::enforceMorphMap([
            JobInvitedUser::USER_TYPE_LIVE => User::class,
            JobInvitedUser::USER_TYPE_PRIVATE => PrivateUser::class,
        ]);

        Response::macro('success', function (array $data = [], string|null $message = null) {
            return Response::json([
                'success'  => true,
                'data' => $data,
                'message' => $message
            ]);
        });

        Response::macro('error', function (string $message, int $status = 400, array $errors = []) {
            return Response::json([
                'success'  => false,
                'message' => $message,
                'errors' => $errors
            ], $status);
        });

        Mail::extend('outlook', function (array $config = []) {
            return new MicrosoftOutlookMailTransport(
                $config['client_id'],
                $config['client_secret'],
                $config['tenant_id'],
                $config['send_mail_post_url'],
                $config['timeout']
            );
        });
    }
}
