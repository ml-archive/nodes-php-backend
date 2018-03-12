<?php

namespace Nodes\Backend\Dashboard\Tiles;

use Illuminate\Support\Str;
use Nodes\Backend\Dashboard\Exceptions\MissingConfigException;

/**
 * Class IFrame
 *
 * @package Nodes\Backend\Dashboard\Tile
 */
class IFrame extends Tile
{
    /**
     * getType
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return string
     */
    public function getType()
    {
        return 'i-frame';
    }
}
