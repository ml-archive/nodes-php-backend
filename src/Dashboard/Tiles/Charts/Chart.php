<?php

namespace Nodes\Backend\Dashboard\Tiles\Charts;

use Nodes\Backend\Dashboard\Exceptions\MissingConfigException;
use Nodes\Backend\Dashboard\Tiles\Tile;

/**
 * Class Chart
 *
 * @package Nodes\Backend\Dashboard\Tiles\Charts
 */
abstract class Chart extends Tile
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var array
     */
    protected $chartData;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var array
     */
    public static $colors = [
        '#4D4D4D',
        '#5DA5DA',
        '#FAA43A',
        '#60BD68',
        '#F17CB0',
        '#B2912F',
        '#B276B2',
        '#DECF3F',
        '#F15854',
        '#000000',
        '#FF0000',
        '#FFFF00',
        '#808000',
        '#00FF00',
        '#C0C0C0',
        '#008000',
        '#00FFFF',
        '#008080',
        '#0000FF',
        '#800000',
        '#000080',
        '#FF00FF',
        '#800080',
        '#808080',
        // Let's hope there is no more
    ];

    /**
     * IFrame constructor.
     *
     * @param array $config
     * @throws \Nodes\Backend\Dashboard\Exceptions\MissingConfigException
     */
    public function __construct($title, $data)
    {
        parent::__construct($title, $data);

        $this->chartData = $this->prepareChartData($data);
    }

    /**
     * Prepare the chart data.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param $data
     * @return $chartData
     */
    abstract protected function prepareChartData($data);
}
