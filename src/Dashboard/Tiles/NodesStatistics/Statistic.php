<?php

namespace Nodes\Backend\Dashboard\Tiles\NodesStatistics;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Nodes\Backend\Dashboard\Exceptions\MissingConfigException;
use Nodes\Backend\Dashboard\Tiles\Charts\LineChart;

/**
 * Class Statistic.
 *
 * @author  Casper Rasmussen <cr@nodes.dk>
 */
abstract class Statistic extends LineChart
{
    /**
     * @var string
     */
    protected $period;

    /**
     * @var string
     */
    protected $gaId;


    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return string
     */
    public function getPeriod()
    {
        return $this->period;
    }


    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return bool
     * @throws \Nodes\Backend\Dashboard\Exceptions\UnsupportedTypeException
     */
    public function prepareChartData($data)
    {
        $this->gaId = $data;

        $url = sprintf(env('NODES_STATISTICS_HISTORY'), $this->gaId);

        // Generate query
        if ($this->period == 'monthly') {
            $query = [
                'from'  => Carbon::now()->subMonth()->format('Y-m-d'),
                'to'    => Carbon::now()->format('Y-m-d'),
                'group' => 'day',
            ];
        } else {
            $query = [
                'from'  => Carbon::now()->format('Y-m-d'),
                'to'    => Carbon::now()->addDay()->format('Y-m-d'),
                'group' => 'hour',
            ];
        }

        // Append query to url
        $url .= '?'.http_build_query($query);

        $chartData = [
            'id'     => $this->id,
            'title'  => $this->title,
            'data'   => [],
            'labels' => [],
        ];

        // Look up in cache
        $response = \Cache::get($url);

        $client = new Client([
            'timeout' => 5,
            'connect_timeout' => 5
        ]);
        if (! $response) {
            // Do api call in request
            try {
                $response = json_decode($client->get($url)->getBody(), true);

                \Cache::put($url, $response, 60);
            } catch (\Exception $e) {
                return false;
            }
        }

        // Now get total visitors and from those platforms
        foreach ($response['data'] as $data) {
            $time = Carbon::createFromFormat('Y-m-d H:i:s', $data['time']);
            if ($this->period == 'monthly') {
                $label = $time->format('m/d');
            } else {
                $label = $time->format('m/d H:00');
            }

            // Append
            $chartData['labels'][] = $label;
            $chartData['data'][] = ! empty($data['visit_count']) ? $data['visit_count'] : 0;
        }

        return $chartData;
    }
}
