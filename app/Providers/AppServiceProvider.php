<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Share site settings (like logo) with all views
        view()->composer('*', function ($view) {
            $settings = [];
            if (Schema::hasTable('setting_website')) {
                $settings = \Illuminate\Support\Facades\DB::table('setting_website')->pluck('value', 'key')->toArray();
            }

            // Ensure logo has a usable fallback
            if (empty($settings['logo'])) {
                $settings['logo'] = 'assets/images/CepatDapat.png';
            }

            $view->with('site_settings', $settings);
        });
    }
}

