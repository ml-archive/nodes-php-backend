<?php

namespace Nodes\Backend\Http\Middleware;

use Closure;

/**
 * Class SSL
 * @author Casper Rasmussen <cr@nodes.dk>
 * @package Nodes\Backend\Http\Middleware
 */
class SSL
{
    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param $request
     * @param Closure $next
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        if (!$request->secure() && env('APP_ENV') == 'production') {
            return redirect()->secure($request->getRequestUri());
        }

        return $next($request);
    }
}

