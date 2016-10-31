<?php

namespace Nodes\Backend\Auth\Contracts;

/**
 * Interface CanResetPassword.
 *
 * @interface
 */
interface CanResetPassword
{
    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return string
     */
    public function getEmailForPasswordReset();
}
