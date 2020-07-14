<?php

namespace ModelValidator;

use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use ModelValidator\Commands\MakeModelValidator;

class ServiceProvider extends LaravelServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/modelvalidator.php' => config_path('modelvalidator.php'),
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->commands(MakeModelValidator::class);
    }
}
