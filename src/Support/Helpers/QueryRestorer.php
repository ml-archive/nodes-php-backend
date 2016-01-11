<?php
use Illuminate\Support\Facades\Cookie;

if (!function_exists('query_restore')) {
    /**
     * Will store the current query, if there is no query it will look up in cookies and redirect if found
     * @author cr@nodes.dk
     * @param array $params
     * @return bool|string
     */
    function query_restore($params = [], $blacklist = [])
    {

        // Store and return
        if(!empty(\Input::get())) {
            \Cookie::queue(Cookie::make(md5(\Request::url() . '?' . http_build_query($params)), \Input::get(), 5));

            return false;
        }

        // Retrieve
        $query = Cookie::get(\Request::url() . '?' . http_build_query($params));

        foreach($blacklist as $key) {
            unset($query[$key]);
        }

        // Redirect with queries
        if(!empty($query) && is_array($query)) {
            return \Request::url() . '?' . http_build_query($query);
        }

        return false;
    }
}



