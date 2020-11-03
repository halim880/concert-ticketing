<?php

namespace App\Providers;

use App\Models\OrderConfirmationNumberGenerator;
use App\Models\RandomOrderConfirmationNumberGenerator;
use App\Models\RandomTicketCodeGenerator;
use App\Models\TicketCodeGenerator;
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
        $this->app->bind(OrderConfirmationNumberGenerator::class, RandomOrderConfirmationNumberGenerator::class);
        $this->app->bind(TicketCodeGenerator::class, RandomTicketCodeGenerator::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
