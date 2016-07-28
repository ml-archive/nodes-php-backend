<?php

namespace Nodes\Backend\Http\Middleware;

use Closure;

/**
 * Class ApiAuth.
 */
class ApiAuth
{
    /**
     * Check to see if user is authenticated.
     * If not, redirect user to login.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @param  \Illuminate\Http\Request $request
     * @param  Closure                  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Route is protected and requires a user session
        //
        // If user is not already logged in, we'll try and
        // look for the user in sessions and cookies.
        if (! backend_user_check()) {
            try {
                backend_user_authenticate();
            } catch (\Exception $e) {
                $data = [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                ];

                if (env('APP_DEBUG')) {
                    $data['class'] = get_class($e);
                    $data['file'] = $e->getFile();
                    $data['line'] = $e->getLine();
                    $data['trace'] = explode("\n", $e->getTraceAsString());
                }

                return response()->json($data, $e->getCode());
            }
        }

        return $next($request);
    }
}
