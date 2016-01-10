<?php
namespace Nodes\Backend\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Nodes Backend Authenticator Facade
 *
 * @package Nodes\Backend\Support\Facade
 */
class Authenticator extends Facade
{
    /**
     * Get currently authenticated user
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @static
     * @access public
     * @return array|null
     */
    public static function user()
    {
        return static::$app['nodes.backend.auth']->getUser();
    }

    /**
     * Get the registered name of the component
     *
     * @access protected
     * @return string
     */
    protected static function getFacadeAccessor() { return 'nodes.backend.auth'; }
}