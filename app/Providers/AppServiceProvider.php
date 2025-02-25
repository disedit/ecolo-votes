<?php

namespace App\Providers;

use App\Models\Edition;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

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
        if (config('app.installed')) {
            $currentEdition = Edition::current()->first();

            if ($currentEdition) {
                Config::set('edition_id', $currentEdition->id);
            }
        }
    }
}
