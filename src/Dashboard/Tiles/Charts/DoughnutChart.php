<?php

namespace Nodes\Backend\Dashboard\Tiles\Charts;

use Nodes\Backend\Dashboard\Exceptions\MissingConfigException;

/**
 * Class DoughnutChart
 *
 * @package Nodes\Backend\Dashboard\Tiles\Charts
 */
class DoughnutChart extends Chart
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
        return 'doughnut-chart';
    }

    /**
     * prepareChartData
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access protected
     * @param array $config
     * @return array
     */
    protected function prepareChartData($data)
    {
        $chartData = [
            'id'     => $this->id,
            'title'  => $this->title,
            'data'   => [],
            'labels' => [],
        ];

        foreach ($data as $key => $value) {
            $chartData['data'][] = $value;
            $chartData['labels'][] = $key;
        }

        return $chartData;
    }
}
