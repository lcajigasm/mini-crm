<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Listeners\AppointmentTemplateSubscriber;
use App\Console\Commands\GdprExportCustomer;
use App\Console\Commands\GdprEraseCustomer;

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
        Event::subscribe(AppointmentTemplateSubscriber::class);
        if ($this->app->runningInConsole()) {
            $this->commands([
                GdprExportCustomer::class,
                GdprEraseCustomer::class,
            ]);
        }
    }
}
