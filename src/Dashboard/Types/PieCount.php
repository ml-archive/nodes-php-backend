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
 *  $dashboardCollection = new DashboardCollection($config['list']);
 *
 * @package Nodes\Backend\Dashboard\Types
 */
class PieCount extends Array2dCount
{
    /**
     * @var string
     */
    protected $type = 'pie-chart';

    protected $colors = [
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
        '#000000',
        '#000000',
        '#000000',
        '#000000',
        '#000000',
        '#000000',
    ];

    public function prepareChartData($config)
    {
        $chartData = [
            'id' => $this->id,
        ];

        $color = 0;
        foreach ($config['data'] as $key => $value) {
            $chartData['datasets'][] = [
                'value'         => $value,
                'label'         => $key,
                'color'         => $this->colors[$color]
            ];

            $color++;
        }

        $this->chartData = $chartData;
    }
}
