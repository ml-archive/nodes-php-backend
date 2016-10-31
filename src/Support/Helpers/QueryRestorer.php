<?php

use Nodes\Backend\Support\FlashRestorer;

if (!function_exists('query_restorer')) {
    /**
     * Will store the current query, if there is no query it will look up in cookies and redirect if found.
     *
     * @author cr@nodes.dk
     * @param array $params
     * @return bool|string
     */
    function query_restorer($params = [], $blacklist = [])
    {

        // Store and return
        if (!empty(\Request::all())) {
            \Cookie::queue(\Cookie::make(md5(\Request::url() . '?' . http_build_query($params)), \Request::all(), 5));

            return false;
        }

        // Retrieve
        $query = \Cookie::get(md5(\Request::url() . '?' . http_build_query($params)));

        foreach ($blacklist as $key) {
            unset($query[$key]);
        }

        // Redirect with queries
        if (!empty($query) && is_array($query)) {
            return \Request::url() . '?' . http_build_query($query);
        }

        return false;
    }
}

if (!function_exists('query_restorer_with_flash')) {
    /**
     * Will restore the origin url with queries, but also remember the session flashes
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param array $params
     * @param array $blacklist
     * @return bool|\Illuminate\Http\RedirectResponse
     */
    function query_restorer_with_flash($params = [], $blacklist = [])
    {
        if ($redirect = query_restorer($params, $blacklist)) {
            $redirectResponse = redirect()->to($redirect);

            (new FlashRestorer())->apply($redirectResponse);

            return $redirectResponse;
        }

        return false;
    }
}
