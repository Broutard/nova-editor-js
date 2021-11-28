<?php

namespace Broutard\NovaEditorJs;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Laravel\Nova\Events\ServingNova;
use Laravel\Nova\Nova;

class FieldServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->app->booted(function () {
            $this->routes();
        });

        $this->publishes([
            __DIR__ . '/../config/nova-editor-js.php' => base_path('config/nova-editor-js.php'),
        ], 'editorjs-config');

        $this->publishes([
            __DIR__ . '/../resources/views/nova' => resource_path('views/vendor/nova-editor-js/html'),
        ], 'editorjs-views');

        $this->publishes([
            __DIR__ . '/../resources/lang' => resource_path('lang/vendor/nova-editor-js'),
        ], 'editorjs-lang');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'nova-editor-js');

        Nova::serving(function (ServingNova $event) {
            Nova::script('nova-editor-js', __DIR__ . '/../dist/js/field.js');
            Nova::style('nova-editor-js', __DIR__ . '/../dist/css/field.css');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('editorjs.sanitizer', function ($app) {
            return new BlockSanitizer;
        });

        $this->app->singleton('editorjs.handler', function ($app) {
            return new BlockHandler;
        });
    }

    /**
     * Register the fields's routes.
     *
     * @return void
     */
    protected function routes()
    {
        if ($this->app->routesAreCached()) {
            return;
        }

        Route::middleware(['nova'])
            ->prefix('nova-vendor/editor-js-field')
            ->group(__DIR__ . '/../routes/api.php');
    }
}
