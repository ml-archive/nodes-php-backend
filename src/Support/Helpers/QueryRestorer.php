<?php

if (!function_exists('query_restore')) {
    /**
     * Will store the current query, if there is no query it will look up in cookies and redirect if found
     * @author cr@nodes.dk
     * @param array $params
     * @return bool|string
     */
    function query_restore($params = [], $blacklist = [])
    {
        return \NodesQueryRestorer::fire($params, $blacklist);
    }
}



