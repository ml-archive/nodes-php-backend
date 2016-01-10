<?php
namespace Nodes\Backend\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class Nodes Backend Route Facade
 *
 * @package Nodes\Backend\Support\Facade
 */
class Router extends Facade
{
    /**
     * Get the registered name of the component
     *
     * @access protected
     * @return string
     */
    protected static function getFacadeAccessor() { return 'nodes.backend.router'; }
}