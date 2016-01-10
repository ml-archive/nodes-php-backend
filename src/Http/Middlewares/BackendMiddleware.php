<?php
namespace Nodes\Backend\Http\Middlewares;

use Closure;

/**
 * Class BackendMiddleware
 *
 * @package Nodes\Backend\Http\Middleware
 */
class BackendMiddleware
{
    /**
     * Check to see if user is authenticated.
     * If not, redirect user to login.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param  \Illuminate\Http\Request $request
     * @param  Closure                  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Backend request is protected,
        // so we need to make sure user is authenticatede
        if (!backend_user_check()) {
            return redirect()->route('nodes.backend.login.form')->with('warning', 'Oops! You\'re not logged in.');
        }

        return $next($request);
    }
}
