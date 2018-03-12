<?php

namespace Nodes\Backend\Dashboard\Tiles\Charts;

use Illuminate\Support\Str;
use Nodes\Backend\Dashboard\Exceptions\MissingConfigException;

/**
 * Class LineChart
 *
 * @package Nodes\Backend\Dashboard\Tiles\Charts
 */
class LineChart extends Chart
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
        return 'line-chart';
    }

    /**
     * prepareChartData
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access protected
     * @param array $data
     * @return array
     */
    protected  function prepareChartData($data)
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
