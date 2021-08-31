<?php

namespace AwStudio\Deeplable;

use AwStudio\Deeplable\Deepl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\ServiceProvider;
use AwStudio\Deeplable\Translators\Resolver;
use AwStudio\Deeplable\Commands\DeeplableCommand;
use Astrotomic\Translatable\Contracts\Translatable;
use AwStudio\Deeplable\Translators\AstrotomicTranslator;

class DeeplableServiceProvider extends ServiceProvider
{
    /**
     * Register application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('deeplable.api', function ($app) {
            $apiToken = $app['config']['deeplable.api_token'];
            $apiUrl = $app['config']['deeplable.api_url'];
            $fallbackLocale = $app['config']['translatable.fallback_locale'];

            return new Deepl($apiToken, $apiUrl, $fallbackLocale);
        });

        $this->app->singleton('deeplable.translator', function ($app) {
            $resolver = new Resolver();

            $resolver->register(Translatable::class, function () use ($app) {
                return new AstrotomicTranslator($app['deeplable.api']);
            });

            $resolver->strategy(function (Model $model) {
                return Translatable::class;
            });

            return $resolver;
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
