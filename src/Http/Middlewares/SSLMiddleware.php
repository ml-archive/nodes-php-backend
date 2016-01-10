<?php

namespace Nodes\Backend\Http\Middlewares;

use Closure;

/**
 * Class HttpsProtocolMiddleware
 * @author Casper Rasmussen <cr@nodes.dk>
 * @package Baas\Middleware
 */
class SSLMiddleware
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

