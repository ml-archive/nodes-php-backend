<?php
namespace Nodes\Backend\Auth\Exception;

use Nodes\Exceptions\Exception;

/**
 * Class ResetPasswordNoUserException
 *
 * @package Nodes\Backend\Auth\Exception
 */
class ResetPasswordNoUserException extends Exception
{
    /**
     * Constructor
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string  $message
     * @param  integer $statusCode
     * @param  string  $statusCodeMessage
     * @param  boolean $report
     */
    public function __construct($message, $statusCode = 446, $statusCodeMessage = 'No user found', $report = false)
    {
        parent::__construct($message, $statusCode, $statusCodeMessage, $report);
    }
}