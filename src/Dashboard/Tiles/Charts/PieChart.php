<?php

namespace Nodes\Backend\Dashboard\Tiles\Charts;

use Nodes\Backend\Dashboard\Exceptions\MissingConfigException;

/**
 * Class PieChart
 *
 * @package Nodes\Backend\Dashboard\Tiles\Charts
 */
class PieChart extends Chart
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
        return 'pie-chart';
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
            'labels' => []
        ];

        foreach ($data as $key => $value) {
            $chartData['data'][] = $value;
            $chartData['labels'][] = $key;
        }

        return $chartData;
    }
}
