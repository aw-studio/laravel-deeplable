<?php

namespace AwStudio\Deeplable;

use AwStudio\Deeplable\Commands\DeeplableCommand;
use Illuminate\Support\ServiceProvider;

class DeeplableServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('laravel-deeplable', function ($app) {
            return new Deepl;
        });
    }

    /**
     * Boot application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerCommands();

        $this->publishes([
            __DIR__.'/../config/deeplable.php' => config_path('deeplable.php'),
        ], 'deeplable');
    }

    /**
     * Register Deeplable command.
     *
     * @return void
     */
    public function registerCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                DeeplableCommand::class,
            ]);
        }
    }
}
