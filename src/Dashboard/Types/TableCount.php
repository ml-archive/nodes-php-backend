<?php

namespace Nodes\Backend\Dashboard\Types;

use Illuminate\Support\Str;
use Nodes\Backend\Dashboard\Exceptions\MissingConfigException;

class TableCount
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
        if (empty($config['title']) || ! is_string($config['title'])) {
            throw new MissingConfigException('Missing title');
        }

        // Guard title url param
        if (empty($config['tables']) || ! is_array($config['tables'])) {
            throw new MissingConfigException('Missing tables');
        }

        // Set params
        $this->title = $config['title'];
        $this->tables = $config['tables'];

        // Assign random id
        $this->id = Str::random();

        $this->prepareChartData();
    }

    /**
     * Prepare the chart data.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function prepareChartData()
    {
        $chartData = [
            'id' => $this->id,
            'title' => $this->title,
            'data' => [],
            'labels' => [],
        ];

        foreach ($this->tables as $table => $label) {
            $chartData['data'][] = \DB::table($table)->count();
            $chartData['labels'][] = $label;
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
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return array
     */
    public function getTables()
    {
        return $this->tables;
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
