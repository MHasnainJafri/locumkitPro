<?php

namespace App\Providers;

use App\Models\Blog;
use App\Models\SiteTown;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\View\View as ViewHelper;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $latest_blogs = array();
        $available_tags = array();
        if (!app()->runningInConsole()) {
            $latest_blogs = Blog::query()->latest()->limit(3)->get();
            $available_tags = SiteTown::where("town", "!=", "")->select("town")->pluck("town")->toArray();
        }
        View::composer(['layouts.app', 'layouts.user_profile_app'], function (ViewHelper $view) use ($latest_blogs) {
            $view->with('latest_blogs', $latest_blogs);
        });
        View::composer(['auth.register', 'employer.edit-profile', 'employer.manage-store', 'freelancer.edit-profile'], function (ViewHelper $view) use ($available_tags) {
            $view->with('site_towns_available_tags', $available_tags);
        });
    }
}