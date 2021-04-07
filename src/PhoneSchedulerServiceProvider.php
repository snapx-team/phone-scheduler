<?php

namespace Xguard\PhoneScheduler;

use Illuminate\Support\ServiceProvider;
use Xguard\PhoneScheduler\Http\Middleware\CheckHasAccess;

class PhoneSchedulerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->make('Xguard\PhoneScheduler\Http\Controllers\PhoneScheduleController');
        $this->loadViewsFrom(__DIR__ . '/resources/views', 'Xguard\PhoneScheduler');
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(\Illuminate\Routing\Router $router)
    {
        app('router')->aliasMiddleware('phone_scheduler_role_check', CheckHasAccess::class);
        $this->loadMigrationsFrom(__DIR__.'/Http/Middleware');

        include __DIR__ . '/routes/web.php';

        if ($this->app->runningInConsole()) {

            $this->publishes([
                __DIR__ . '/../public' => public_path('vendor/phone-scheduler'),
            ], 'phone-scheduler-assets');
        }
    }
}
