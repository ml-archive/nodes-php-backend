<?php
use Nodes\Backend\Auth\Contracts\Authenticatable;

if (!function_exists('backend_auth')) {
    /**
     * Retrieve authenticator instance
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return \Nodes\Backend\Auth\Authenticator
     */
    function backend_auth()
    {
        return \NodesBackend::auth();
    }
}

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
        return \NodesBackend::auth()->user();
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
        return \NodesBackend::auth()->check();
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
        return \NodesBackend::auth()->login($user, $remember);
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
        return \NodesBackend::auth()->logout();
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
        return \NodesBackend::auth()->attempt($credentials, $remember, $login);
    }
}

