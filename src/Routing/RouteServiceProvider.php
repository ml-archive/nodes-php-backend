<?php
namespace Nodes\Backend\Routing;

use App\Providers\RouteServiceProvider as IlluminateRouteServiceProvider;
use Illuminate\Routing\Router as IlluminateRouter;

/**
 * Class RouteServiceProvider
 *
 * @package Nodes\Routing
 */
class RouteServiceProvider extends IlluminateRouteServiceProvider
{
    /**
     * Route middlewares
     * @var array
     */
    protected $routeMiddleware = [
        'backend' => 'Nodes\Backend\Http\Middleware\Backend',
        'ssl' => 'Nodes\Backend\Http\Middleware\SSL'
    ];

    /**
     * Bootstrap any application services
     *
     * @access public
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function boot(IlluminateRouter $router)
    {
        $this->loadRouteMiddlewares();
        parent::boot($router);
    }

    /**
     * Register the service provider.
     *
     * @access public
     * @return void
     */
    public function register()
    {
        $this->registerRouter();
        $this->setupBinding();
    }

    /**
     * Register authenticator
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function registerRouter()
    {
        $this->app->singleton('nodes.backend.router', function ($app) {
            return new Router($app['router']);
        });
    }

    /**
     * Setup container binding
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function setupBinding()
    {
        $this->app->bind('Nodes\Backend\Routing\Router', function ($app) {
            return $app['nodes.backend.router'];
        });
    }

    /**
     * Prepend our route middlewares
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function loadRouteMiddlewares()
    {
        foreach($this->routeMiddleware as $name => $class) {
            $this->middleware($name, $class);
        }
    }

    public function map(IlluminateRouter $router)
    {
        parent::map($router);

        return load_directory(base_path(). '/project/Routes/Backend/', true);
    }
}
