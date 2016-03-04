<?php
namespace Nodes\Backend\Auth\Exceptions;

use Nodes\Exceptions\Exception as NodesException;

/**
 * Class InvalidTokenException
 *
 * @package Nodes\Backend\Auth\Exceptions
 */
class InvalidTokenException extends NodesException
{
    /**
     * InvalidTokenException constructor
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string   $message
     * @param  integer  $code
     * @param  array    $headers
     * @param  boolean  $report
     * @param  string   $severity
     */
    public function __construct($message = 'Invalid token', $code = 442, array $headers = [], $report = false, $severity = 'error')
    {
        parent::__construct($message, $code, $headers, $report, $severity);

        // Set status code and status message
        $this->setStatusCode(442, 'Invalid token');
    }
}
