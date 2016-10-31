<?php

namespace Nodes\Backend\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Nodes Backend Facade.
 */
class Backend extends Facade
{
    /**
     * Retrieve nodes.backend.backend authenticator.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @static
     * @return \Nodes\Backend\Auth\Authenticator
     */
    public static function auth()
    {
        return static::$app['nodes.backend.auth'];
    }

    /**
     * Retrieve nodes.backend.backend router.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @static
     * @return \Nodes\Backend\Routing\Router
     */
    public static function router()
    {
        return static::$app['nodes.backend.router'];
    }
}
