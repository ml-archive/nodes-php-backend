<?php
namespace Nodes\Backend\Support\Facades;

use Illuminate\Support\Facades\Facade;
use Nodes\Backend\Models\User\User;

/**
 * Class Nodes Backend Facade
 *
 * @package Nodes\Backend\Support\Facade
 */
class Backend extends Facade
{
    /**
     * Retrieve nodes.backend.backend authenticator
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @static
     * @access public
     * @return \Nodes\Backend\Auth\Authenticator
     */
    public static function auth()
    {
        return static::$app['nodes.backend.auth'];
    }

    /**
     * Retrieve currently authenticated user
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @static
     * @access public
     * @return array|null
     */
    public static function user()
    {
        return self::auth()->getUser();
    }

    /**
     * Retrieve nodes.backend.backend router
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @static
     * @access public
     * @return \Nodes\Backend\Routing\Router
     */
    public static function router()
    {
        return static::$app['nodes.backend.router'];
    }

    /**
     * Abort request and redirect user to "permission denied" page
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @static
     * @access public
     * @return void
     */
    public static function abort() {
        abort(302, 'User does not have the required permission.', [
            'Location' => route('nodes.backend.errors.permission-denied')
        ]);
    }
}