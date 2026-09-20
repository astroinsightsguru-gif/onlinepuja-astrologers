<?php

namespace App\Providers;

use App\Models\AdminModel\SystemFlag;
use App\Models\Page;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        Paginator::defaultView('vendor.pagination.simple-tailwind');

        View::composer('*', function ($view) {
            // Use helper functions for cached system flags
            $professionTitle = getProfessionTitle();
            $appname = getAppName();

            // Share the data with the view
            $view->with([
                'professionTitle' => $professionTitle,
                'appname' => $appname
            ]);

            // Cache footer pages for 1 hour
            $footerPages = cache()->remember('footer_pages_active', 3600, function () {
                return Page::where('isActive', 1)->get();
            });
            $view->with('footerPages', $footerPages);

            // Cache coin icon and wallet type for 1 hour
            $coinIcon = cache()->remember('system_flag_coinIcon', 3600, function () {
                return systemflag('coinIcon');
            });

            $walletType = cache()->remember('system_flag_walletType', 3600, function () {
                return strtolower(systemflag('walletType'));
            });

            // Share them with all Blade views
            View::share('coinIcon', $coinIcon);
            View::share('walletType', $walletType);
        });
    }
}
