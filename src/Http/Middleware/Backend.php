<?php
namespace Nodes\Backend\Http\Middleware;

use Closure;
use Nodes\Backend\Support\FlashRestorer;

/**
 * Class Backend
 *
 * @package Nodes\Backend\Http\Middleware
 */
class Backend
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
        // so we need to make sure user is authenticated, else redirect to login
        if (!backend_user_check()) {
            // Create redirect response
            $redirectResponse = redirect()->route('nodes.backend.login.form')->with('warning', 'Oops! You\'re not logged in.');

            // Apply existing flash messages
            (new FlashRestorer())->apply($redirectResponse);

            // Redirect
            return $redirectResponse;
        }

        return $next($request);
    }
}
