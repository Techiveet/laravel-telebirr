<?php

namespace Techive\Telebirr;

use Illuminate\Support\ServiceProvider;

class TelebirrServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish the configuration file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/telebirr.php' => config_path('telebirr.php'),
            ], 'telebirr-config');
        }

        // Load the routes
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Merge the default config
        $this->mergeConfigFrom(__DIR__.'/../config/telebirr.php', 'telebirr');

        // Register the main Telebirr class as a singleton
        $this->app->singleton('telebirr', function ($app) {
            return new Telebirr($app['config']['telebirr']);
        });
    }
}