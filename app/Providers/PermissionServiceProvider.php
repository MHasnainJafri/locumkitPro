<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class PermissionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Blade::directive('cando', function ($expression) {
            return "<?php 
            if(checkPermission($expression))
            {
            
            ?>";
        });
        
        Blade::directive('endcando', function () {
            return '<?php  } ?>';
        });
        
        
    }
}
