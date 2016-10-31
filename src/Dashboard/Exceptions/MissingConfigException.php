<?php

namespace Nodes\Backend\Dashboard\Exceptions;

use Nodes\Exceptions\Exception;

/**
 * Class MissingConfigException.
 * @author  Casper Rasmussen <cr@nodes.dk>
 */
class MissingConfigException extends Exception
{
    /**
     * MissingConfigException constructor.
     *
     * @param string $message
     */
    public function __construct($message)
    {
        parent::__construct($message, 500);
        $this->report();
    }
}
