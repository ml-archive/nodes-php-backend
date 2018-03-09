<?php

namespace Nodes\Backend\Dashboard\Types;

use Illuminate\Support\Str;
use Nodes\Backend\Dashboard\Exceptions\MissingConfigException;

/**
 * Class LineChartCount
 *
 * @package Nodes\Backend\Dashboard\Types
 */
class LineChartCount extends Array2dCount
{
    /**
     * @var string
     */
    protected $type = 'line-chart';

    public function prepareChartData($config)
    {
        $chartData = [
            'id' => $this->id,
        ];

        foreach ($config['data'] as $key => $value) {
            $chartData['data'][] = $value;
            $chartData['labels'][] = $key;
        }

        $this->chartData = $chartData;
    }
}
