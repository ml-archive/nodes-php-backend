<?php

namespace Nodes\Backend\Dashboard\Tiles\Charts;

use Illuminate\Support\Str;
use Nodes\Backend\Dashboard\Exceptions\MissingConfigException;

class BarChart extends Chart
{
    /**
     * getType
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return string
     */
    function getType()
    {
        return 'bar-chart';
    }

    /**
     * prepareChartData
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access protected
     * @param array $data
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
            $chartData['labels'][] = $key;
            $chartData['data'][] = $value;
        }

        return $chartData;
    }
}
