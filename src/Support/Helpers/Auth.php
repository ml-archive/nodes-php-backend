<?php
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
        return app('nodes.backend.auth');
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
        return app('nodes.backend.auth')->getUser();
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
        return app('nodes.backend.auth')->check();
    }
}

if (!function_exists('backend_user_authenticate')) {
    /**
     * Try and authenticate user by available providers
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return boolean
     */
    function backend_user_authenticate()
    {
        return app('nodes.backend.auth')->authenticate();
    }
}

if (!function_exists('backend_user_login')) {
    /**
     * Login user
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @param  \Nodes\Backend\Auth\Contracts\Authenticatable $user
     * @param  boolean                                       $remember
     * @return \Nodes\Backend\Auth\Manager
     */
    function backend_user_login(\Nodes\Backend\Auth\Contracts\Authenticatable $user, $remember = false)
    {
        return app('nodes.backend.auth')->createLoginSession($user, $remember);
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
        return app('nodes.backend.auth')->logout();
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
        return app('nodes.backend.auth')->attempt($credentials, $remember, $login);
    }
}

