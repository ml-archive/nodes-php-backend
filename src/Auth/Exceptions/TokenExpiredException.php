<?php
namespace Nodes\Backend\Auth\Exceptions;

use Nodes\Exceptions\Exception as NodesException;

/**
 * Class TokenExpiredException
 *
 * @package Nodes\Backend\Auth\Exceptions
 */
class TokenExpiredException extends NodesException
{
    /**
     * TokenExpiredException constructor
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
    public function __construct($message = 'Token expired', $code = 443, array $headers = [], $report = false, $severity = 'error')
    {
        parent::__construct($message, $code, $headers, $report, $severity);

        // Set status code and status message
        $this->setStatusCode(443, 'Token expired');
    }
}