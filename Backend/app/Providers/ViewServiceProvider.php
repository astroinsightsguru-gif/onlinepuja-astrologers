<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
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
        View::composer('*', 'App\Http\View\Composers\MenuComposer');

        // Only load FakerComposer in development environment to avoid performance hit
        if (config('app.env') === 'development') {
            View::composer('*', 'App\Http\View\Composers\FakerComposer');
        }

        View::composer('*', 'App\Http\View\Composers\DarkModeComposer');
        View::composer('*', 'App\Http\View\Composers\LoggedInUserComposer');
        View::composer('*', 'App\Http\View\Composers\ColorSchemeComposer');

        View::composer(
            ['frontend.layout.header', 'frontend.layout.footer', 'frontend.layout.master'],
            'App\Http\View\Composers\FrontendLayoutComposer'
        );

        View::composer(
            ['frontend.astrologers.layout.*'],
            'App\Http\View\Composers\AstrologerLayoutComposer'
        );
    }
}
