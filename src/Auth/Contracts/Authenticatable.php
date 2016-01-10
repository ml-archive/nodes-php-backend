<?php
namespace Nodes\Backend\Auth\Contracts;

/**
 * Interface Authenticatable
 *
 * @interface
 * @package Nodes\Backend\Auth\Contracts
 */
interface Authenticatable
{
    /**
     * Get the unique identifier for the user
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return mixed
     */
    public function getAuthIdentifier();

    /**
     * Get the password for the user
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getAuthPassword();

    /**
     * Get the token value for the "remember me" session
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getRememberToken();

    /**
     * Set the token value for the "remember me" session
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value);

    /**
     * Get the column name for the "remember me" token
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getRememberTokenName();

}
