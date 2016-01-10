<?php
namespace Nodes\Backend\Auth\Contracts;

/**
 * Interface CanResetPassword
 *
 * @interface
 * @package Nodes\Backend\Auth\Contracts
 */
interface CanResetPassword
{
    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getEmailForPasswordReset();
}
