<?php

namespace Nodes\Backend\Routing;

use Illuminate\Routing\Router as IlluminateRouter;

/**
 * Class Router.
 */
class Router
{
    /**
     * Illuminate router.
     * @var \Illuminate\Routing\Router
     */
    private $router;

    /**
     * Name of active class.
     * @var string
     */
    protected $activeClass = 'active';

    /**
     * Name of inactive class.
     * @var string
     */
    protected $inactiveClass = '';

    /**
     * Constructor.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  \Illuminate\Routing\Router $router
     */
    public function __construct(IlluminateRouter $router)
    {
        $this->router = $router;
    }

    /**
     * Match route by pattern.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string|array $patterns
     * @return string
     */
    public function pattern($patterns)
    {
        // Retrieve current request
        $currentRoute = $this->router->current();
        if (empty($currentRoute)) {
            return $this->inactiveClass;
        }

        // Make sure patterns is an array
        $patterns = ! is_array($patterns) ? [$patterns] : $patterns;

        // Decode route path
        if(method_exists($currentRoute, 'getPath')) {
            $uri = $currentRoute->getPath();
        } else {
            $uri = $currentRoute->uri();

        }

        // Check patterns and look for matches
        foreach ($patterns as $pattern) {
            if (str_is($pattern, $uri)) {
                return $this->activeClass;
            }
        }

        return $this->inactiveClass;
    }

    /**
     * Match route by alias.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string|array $aliases
     * @return string
     */
    public function alias($aliases)
    {
        // Retrieve current request
        $currentRoute = $this->router->current();
        if (empty($currentRoute)) {
            return $this->inactiveClass;
        }

        // Make sure patterns is an array
        $aliases = ! is_array($aliases) ? [$aliases] : $aliases;

        // Current route's alias
        $routeAlias = $this->router->currentRouteName();

        // Check aliases and look for matches
        foreach ($aliases as $alias) {
            if ($routeAlias == $alias) {
                return $this->activeClass;
            }
        }

        return $this->inactiveClass;
    }

    /**
     * Set active class.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $class
     * @return \Nodes\Backend\Routing\Router
     */
    public function setActiveClass($class)
    {
        $this->activeClass = $class;

        return $this;
    }

    /**
     * Set inactive class.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $class
     * @return \Nodes\Backend\Routing\Router
     */
    public function setInactiveClass($class)
    {
        $this->inactiveClass = $class;

        return $this;
    }
}
