<?php

namespace Nodes\Backend;

use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Nodes\Backend\Http\Middleware\Auth as NodesBackendHttpMiddlewareAuth;
use Nodes\Backend\Http\Middleware\ApiAuth as NodesBackendHttpMiddlewareApiAuth;
use Nodes\Backend\Http\Middleware\SSL as NodesBackendHttpMiddlewareSSL;
use Nodes\Backend\Routing\Router as NodesBackendRouter;

/**
 * Class ServiceProvider.
 */
class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    public function boot()
    {
        // Register namespace for backend views
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'nodes.backend');

        // Register middlewares
        $this->app['router']->middleware('backend.auth', NodesBackendHttpMiddlewareAuth::class);
        $this->app['router']->middleware('backend.api.auth', NodesBackendHttpMiddlewareApiAuth::class);
        $this->app['router']->middleware('backend.ssl', NodesBackendHttpMiddlewareSSL::class);

        // Publish groups
        $this->publishGroups();
    }

    /**
     * Register the service provider.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @return void
     */
    public function register()
    {
        // Register router
        $this->registerRouter();

        // Register auth service provider
        $this->app->register(\Nodes\Backend\Auth\ServiceProvider::class);
    }

    /**
     * Register publish groups.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function publishGroups()
    {
        // Config files
        $this->publishes([
            __DIR__.'/../config' => config_path('nodes/backend'),
        ], 'config');

        // Route files
        $this->publishes([
            __DIR__.'/../routes' => base_path('project/Routes/Backend'),
        ], 'routes');

        // View files
        $this->publishes([
            __DIR__.'/../resources/views/base.blade.php' => resource_path('views/vendor/nodes.backend/base.blade.php'),
            __DIR__.'/../resources/views/layouts/base.blade.php' => resource_path('views/vendor/nodes.backend/layouts/base.blade.php'),
            __DIR__.'/../resources/views/partials/sidebar/navigation.blade.php' => resource_path('views/vendor/nodes.backend/partials/sidebar/navigation.blade.php'),
            __DIR__.'/../resources/views/errors' => resource_path('views/errors'),
        ], 'views');

        // Assets files
        $this->publishes([
            __DIR__.'/../resources/assets/js' => resource_path('assets/js'),
            __DIR__.'/../resources/assets/scss' => resource_path('assets/scss'),
            __DIR__.'/../public/images' => public_path('images'),
        ], 'assets');

        // Favicons
        $this->publishes([
            __DIR__.'/../public/favicons' => public_path('favicons'),
        ]);

        // Database files
        $this->publishes([
            __DIR__.'/../database/migrations/reset-password' => database_path('migrations'),
            __DIR__.'/../database/migrations/users' => database_path('migrations'),
            __DIR__.'/../database/migrations/failed-jobs' => database_path('migrations'),
            __DIR__.'/../database/seeds/users' => database_path('seeds'),
            __DIR__.'/../database/seeds/NodesBackendSeeder.php' => database_path('seeds/NodesBackendSeeder.php'),
        ], 'database');

        // Frontend files
        $this->publishes([
            __DIR__.'/../bower/.bowerrc' => base_path('.bowerrc'),
            __DIR__.'/../bower/bower.json' => base_path('bower.json'),
            __DIR__.'/../gulp/tasks' => base_path('gulp/tasks'),
            __DIR__.'/../gulp/config.json' => base_path('gulp/config.json'),
            __DIR__.'/../gulp/gulpfile.js' => base_path('gulpfile.js'),
            __DIR__.'/../gulp/package.json' => base_path('package.json'),
        ], 'frontend');

        // Route files
        $this->publishes([
            __DIR__.'/../routes' => base_path('project/Routes/Backend'),
        ], 'routes');
    }

    /**
     * Register backend router.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function registerRouter()
    {
        $this->app->singleton('nodes.backend.router', function ($app) {
            return new NodesBackendRouter($app['router']);
        });

        $this->app->bind('Nodes\Backend\Routing\Router', function ($app) {
            return $app['nodes.backend.router'];
        });
    }
}
