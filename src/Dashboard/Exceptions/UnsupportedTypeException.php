<?php

namespace Nodes\Backend\Dashboard\Exceptions;

use Nodes\Exceptions\Exception;

/**
 * Class UnsupportedTypeException.
 * @author  Casper Rasmussen <cr@nodes.dk>
 */
class UnsupportedTypeException extends Exception
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
