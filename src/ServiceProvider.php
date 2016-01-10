<?php
namespace Nodes\Backend;

use Nodes\AbstractServiceProvider as NodesAbstractServiceProvider;

/**
 * Class ServiceProvider
 *
 * @package Nodes
 */
class ServiceProvider extends NodesAbstractServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var boolean
     */
    protected $defer = false;

    /**
     * Boot the service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return void
     */
    public function boot()
    {
        // Register namespace for backend views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'nodes.backend');
    }

    /**
     * Register the service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access public
     * @return void
     */
    public function register()
    {
        $this->app->register(\Nodes\Backend\Auth\ServiceProvider::class);
    }
}
