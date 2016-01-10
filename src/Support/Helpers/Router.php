<?php

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
        return \NodesBackendRouter::pattern($patterns);
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
        return \NodesBackendRouter::alias($aliases);
    }
}