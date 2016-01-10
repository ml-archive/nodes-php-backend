<?php

namespace Nodes\Backend\Support\Facades;

use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Facade;

class QueryRestorer extends Facade{

    /**
     * Will store the current query, if there is no query it will look up in cookies and redirect if found
     * @author cr@nodes.dk
     * @param array $params
     * @return bool|string
     */
    public static function fire($params = [], $blacklist = []) {

        // Store and return
        if(!empty(\Input::get())) {
            Cookie::queue(Cookie::make(self::generateCookieName($params), \Input::get(), 5));

            return false;
        }

        // Retrieve
        $query = Cookie::get(self::generateCookieName($params));

        foreach($blacklist as $key) {
            unset($query[$key]);
        }

        // Redirect with queries
        if(!empty($query) && is_array($query)) {
            return self::generateLink($query);
        }

        return false;
    }

    /**
     * Generate url by current path + query
     * @author cr@nodes.dk
     * @param $array
     * @return string
     */
    private static function generateLink($array) {
        return \Request::url() . '?' . http_build_query($array);
    }

    /**
     * Generate a cookie name by url and params
     * @author cr@nodes.dk
     * @param $array
     * @return string
     */
    private static function generateCookieName($array) {
        return md5(\Request::url() . '?' . http_build_query($array));
    }
}