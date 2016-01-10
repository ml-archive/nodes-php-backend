<?php
use Nodes\Backend\Auth\Contracts\Authenticatable;

if (!function_exists('backend_user')) {
    /**
     * Retrieve current authenticated user
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    function backend_user()
    {
        return \NodesBackendAuth::user();
    }
}

if (!function_exists('backend_user_check')) {
    /**
     * Check if there there is a authed backend user
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return boolean
     */
    function backend_user_check()
    {
        return \NodesBackendAuth::check();
    }
}

if (!function_exists('backend_user_login')) {
    /**
     * Login user
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return \Nodes\Backend\Auth\Manager
     */
    function backend_user_login(Authenticatable $user, $remember = false)
    {
        return \NodesBackendAuth::login($user, $remember);
    }
}

if (!function_exists('backend_user_logout')) {
    /**
     * Logout user
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return \Nodes\Backend\Auth\Manager
     */
    function backend_user_logout()
    {
        return \NodesBackendAuth::logout();
    }
}

if (!function_exists('backend_attempt')){
    /**
     * Attempt to authenticate a user using the given credentials
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @param  array   $credentials
     * @param  boolean $remember
     * @param  boolean $login
     * @return boolean
     */
    function backend_attempt(array $credentials = [], $remember = false, $login = true)
    {
        return \NodesBackendAuth::attempt($credentials, $remember, $login);
    }
}

