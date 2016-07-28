<?php

namespace Nodes\Backend\Auth\Exception;

use Nodes\Exceptions\Exception;

/**
 * Class ResetPasswordNoUserException.
 */
class ResetPasswordNoUserException extends Exception
{
    /**
     * Constructor.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string  $message
     * @param  int $statusCode
     * @param  string  $statusCodeMessage
     * @param  bool $report
     */
    public function __construct($message, $statusCode = 446, $statusCodeMessage = 'No user found', $report = false)
    {
        parent::__construct($message, $statusCode, $statusCodeMessage, $report);
    }
}
