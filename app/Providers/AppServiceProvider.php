<?php

namespace App\Providers;

use App\Cart\Cart;
use App\Cart\Services\CurrencyConverter\Contracts\CurrencyConverter;
use App\Cart\Services\CurrencyConverter\LocalCurrencyConverter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(CurrencyConverter::class, LocalCurrencyConverter::class);
    }
}
