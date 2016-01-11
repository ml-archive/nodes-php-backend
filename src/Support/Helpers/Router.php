<?php
if (!function_exists('backend_router')) {
    /**
     * Retrieve router
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return \Nodes\Backend\Routing\Router
     */
    function backend_router()
    {
        return \NodesBackend::router();
    }
}

if (!function_exists('backend_router_pattern')) {
    /**
     * Match route by pattern
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @param  string|array $patterns
     * @return string
     */

    function backend_router_pattern($patterns)
    {
        return \NodesBackend::router()->pattern($patterns);
    }
}

if (!function_exists('backend_router_alias')) {
    /**
     * Match route by pattern
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @param  string|array $aliases
     * @return string
     */

    function backend_router_alias($aliases)
    {
        return \NodesBackend::router()->alias($aliases);
    }
}