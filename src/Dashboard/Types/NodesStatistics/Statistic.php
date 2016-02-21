<?php

namespace Nodes\Backend\Dashboard\Types\NodesStatistics;

use Carbon\Carbon;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Nodes\Backend\Dashboard\Exceptions\MissingConfigException;

/**
 * Class Statistic
 *
 * @author  Casper Rasmussen <cr@nodes.dk>
 * @package Nodes\Backend\Dashboard\Types\NodesStatistics
 */
abstract class Statistic
{
    /**
     * @var string
     */
    protected $period;

    /**
     * @var string
     */
    protected $type = 'line-chart';

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $gaId;

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
        if (empty($config['gaId']) || !is_string($config['gaId'])) {
            throw new MissingConfigException('Missing gaId');
        }

        // Guard type
        if (empty($this->period)) {
            throw new MissingConfigException('Period is not set in the child object');
        }

        // Set params
        $this->title = $config['title'];
        $this->gaId = $config['gaId'];

        // Assign random id
        $this->id = self::randomString();

        $this->prepareChartData();
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
     * @return string
     */
    public function getPeriod()
    {
        return $this->period;
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return array
     */
    public function getChartData()
    {
        return $this->chartData;
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return bool
     * @throws \Nodes\Backend\Dashboard\Exceptions\UnsupportedTypeException
     */
    protected function prepareChartData()
    {
        $url = sprintf(env('NODES_STATISTICS_HISTORY'), $this->gaId);

        // Generate query
        if ($this->period == 'monthly') {
            $query = [
                'from' => Carbon::now()->subMonth()->format('Y-m-d'),
                'to' => Carbon::now()->format('Y-m-d'),
                'group' => 'day'
            ];

        } else {
            $query = [
                'from' => Carbon::now()->format('Y-m-d'),
                'to' => Carbon::now()->addDay()->format('Y-m-d'),
                'group' => 'hour'
            ];
        }

        // Append query to url
        $url .= '?' . http_build_query($query);

        $chartData = [
            'id' => $this->id,
            'title' => $this->title,
            'data' => [],
            'labels' => []
        ];

        if(!$response = \Cache::get($url)) {
            // Do api call
            try{
                $client = new Client();
                $response = json_decode($client->get($url)->getBody(), true);

                \Cache::put($url, $response, 1440);
            } catch(\Exception $e) {
                return false;
            }
        }

        // Now get total visitors and from those platforms
        foreach($response['data'] as $data) {
            $time = Carbon::createFromFormat('Y-m-d H:i:s', $data['time']);
            if($this->period == 'monthly') {
                $label = $time->format('m/d');
            } else {
                $label = $time->format('m/d H:00');
            }

            // Append
            $chartData['labels'][] = $label;
            $chartData['data'][] = !empty($data['visit_count']) ? $data['visit_count'] : 0;
        }

        $this->chartData = $chartData;

    }

    /**
     * @author Dennis Haulund Nielsen <dhni@nodes.dk>
     * @param int $length
     * @return string
     */
    private static function randomString($length = 16) {
        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
    }
}