<?php
namespace Nodes\Backend\Auth\Exceptions;

use Nodes\Exceptions\Exception;

/**
 * Class InvalidPasswordException
 *
 * @package Nodes\Backend\Auth\Exceptions
 */
class InvalidPasswordException extends Exception
{
    /**
     * InvalidPasswordException constructor
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param  string  $message   Error message
     * @param  mixed   $code      Error code
     * @param  array   $headers   List of headers
     * @param  boolean $report    Wether or not exception should be reported
     * @param  string  $severity  Options: "fatal", "error", "warning", "info"
     */
    public function __construct($message = 'Invalid password', $code = 400, $headers = [], $report = true, $severity = 'error')
    {
        parent::__construct($message, $code, $headers, $report, $severity);
    }

}