<?php

namespace Laravilt\Schemas;

use Illuminate\Support\ServiceProvider;

class SchemasServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Merge config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/laravilt-schemas.php',
            'laravilt-schemas'
        );

        // Register any services, bindings, or singletons here
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        // Load translations
        $this->loadTranslationsFrom(__DIR__ . '/../resources/lang', 'schemas');



        if ($this->app->runningInConsole()) {
            // Publish config
            $this->publishes([
                __DIR__ . '/../config/laravilt-schemas.php' => config_path('laravilt-schemas.php'),
            ], 'laravilt-schemas-config');

            // Publish assets
            $this->publishes([
                __DIR__ . '/../dist' => public_path('vendor/laravilt/schemas'),
            ], 'laravilt-schemas-assets');


            // Register commands
            $this->commands([
                Commands\InstallSchemasCommand::class,
            ]);
        }
    }
}
