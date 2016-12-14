<?php

namespace Nodes\Backend\Dashboard\Types;

use Illuminate\Support\Str;
use Nodes\Backend\Dashboard\Exceptions\MissingConfigException;

/**
 * Class Array2dCount
 * This class allows to use in charts custom arrays i.e. $exampleArray = ['key1'=>'value1','key2'=>'value2']
 * Below example config array
 *  $config = [
 *     'list' => [
 *               [
 *               'gaId'  => 'gaId123123',
 *               'type'  => 'custom-count',
 *               'title' => 'Example Chart Title',
 *               'data'  => $exampleArray,
 *               ],
 *           ],
 *       ];
 *
 *  $dashboardCollection = new DashboardCollection($config['list']);
 *
 *
 * @package Nodes\Backend\Dashboard\Types
 */
class Array2dCount
{
    /**
     * @var string
     */
    protected $type = 'bar-chart';

    /**
     * @var string
     */
    protected $title;

    /**
     * @var array
     */
    protected $tables;

    /**
     * @var array
     */
    protected $chartData;

    /**
     * @var string
     */
    protected $id;

    /**
     * IFrame constructor.
     *
     * @param array $config
     * @throws \Nodes\Backend\Dashboard\Exceptions\MissingConfigException
     */
    public function __construct(array $config)
    {
        // Guard title param
        if (empty($config['title']) || !is_string($config['title'])) {
            throw new MissingConfigException('Missing title');
        }

        // Guard title url param
        if (empty($config['data']) || !is_array($config['data'])) {
            throw new MissingConfigException('Missing data');
        }

        // Set params
        $this->title = $config['title'];
        $this->data = $config['data'];

        // Assign random id
        $this->id = Str::random();

        $this->prepareChartData($config);
    }

    /**
     * Prepare the chart data.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param $config
     */
    public function prepareChartData($config)
    {
        $chartData = [
            'id'     => $this->id,
            'title'  => $this->title,
            'data'   => [],
            'labels' => [],
        ];

        foreach ($config['data'] as $key => $value) {
            $chartData['data'][] = $value;
            $chartData['labels'][] = $key;
        }

        $this->chartData = $chartData;
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @author Tom Serowka <tose@nodesagency.com>
     * @return mixed
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
     * @return string
     */
    public function getType()
    {
        return $this->type;
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
