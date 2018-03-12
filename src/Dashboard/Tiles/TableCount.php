<?php

namespace Nodes\Backend\Dashboard\Tiles;

use Illuminate\Support\Str;
use Nodes\Backend\Dashboard\Exceptions\MissingConfigException;
use Nodes\Backend\Dashboard\Tiles\Charts\BarChart;

class TableCount extends BarChart
{
    /**
     * prepareChartData
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param $data
     * @return array
     */
    public function prepareChartData($data)
    {
        $chartData = [
            'id'     => $this->id,
            'title'  => $this->title,
            'data'   => [],
            'labels' => [],
        ];

        foreach ($data as $table => $label) {
            $chartData['data'][] = \DB::table($table)->count();
            $chartData['labels'][] = $label;
        }

        return $chartData;
    }
}
