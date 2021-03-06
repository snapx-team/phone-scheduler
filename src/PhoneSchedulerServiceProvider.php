<?php

namespace Xguard\PhoneScheduler;

use Illuminate\Support\ServiceProvider;
use Xguard\PhoneScheduler\Commands\CreateAdmin;
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
        $this->mergeConfigFrom(__DIR__.'/../config.php', 'phone_scheduler');

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot(\Illuminate\Routing\Router $router)
    {
        app('router')->aliasMiddleware('phone_scheduler_role_check', CheckHasAccess::class);
        $this->loadMigrationsFrom(__DIR__ . '/Http/Middleware');
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->commands([CreateAdmin::class]);

        include __DIR__ . '/routes/web.php';

        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/phone-scheduler'),
        ], 'phone-scheduler-assets');
    }
}
