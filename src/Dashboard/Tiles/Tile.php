<?php

namespace Nodes\Backend\Dashboard\Tiles;

use Illuminate\Support\Str;
use Nodes\Backend\Dashboard\Exceptions\MissingConfigException;

abstract class Tile
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var mixed
     */
    protected $data;

    /**
     * @var array
     */
    protected $tables;

    /**
     * @var mixed
     */
    protected $chartData;

    /**
     * @var string
     */
    protected $id;

    /**
     * Tile constructor
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param string $title
     * @param        $data
     */
    public function __construct(string $title, $data)
    {
        // Set params
        $this->title = $title;
        $this->data = $data;

        // Assign random id
        $this->id = Str::random();
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return string
     */
    abstract public function getType();

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * getData
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return array
     */
    public function getChartData()
    {
        return $this->chartData;
    }
}
