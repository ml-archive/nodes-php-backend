<?php

namespace Nodes\Backend\Dashboard;

use Illuminate\Database\Eloquent\Collection;
use Nodes\Backend\Dashboard\Exceptions\MissingConfigException;
use Nodes\Backend\Dashboard\Exceptions\UnsupportedTypeException;
use Nodes\Backend\Dashboard\Tiles\Array2dCount;
use Nodes\Backend\Dashboard\Types\IFrame;
use Nodes\Backend\Dashboard\Tiles\NodesStatistics\DailyStatistic;
use Nodes\Backend\Dashboard\Tiles\NodesStatistics\MonthlyStatistic;
use Nodes\Backend\Dashboard\Tiles\TableCount;

/**
 * Class DashboardCollection.
 */
class DashboardCollection extends Collection
{
    /**
     * DashboardCollection constructor.
     *
     * @param  array $configs
     * @throws \Exception
     */
    public function __construct($configs)
    {
        foreach ($configs as $config) {
            if (empty($config['type'])) {
                throw new MissingConfigException('Missing type');
            }

            switch ($config['type']) {
                case 'i-frame':
                    $this->add(new IFrame($config['title'], $config['url']));
                    break;
                case 'table-count':
                    $this->add(new TableCount($config['title'], $config['tables']));
                    break;
                case 'nodes-statistics-daily':
                    $this->add(new DailyStatistic($config['title'], $config['gaId']));
                    break;
                case 'nodes-statistics-monthly':
                    $this->add(new MonthlyStatistic($config['title'], $config['gaId']));
                    break;
                default:
                    throw new UnsupportedTypeException(sprintf('%s is not supported', $config['type']));
            }
        }
    }

    /**
     * filterForType
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param $type
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function filterForType($type)
    {
        $collection = new Collection();
        foreach ($this as $dashboard) {
            if ($dashboard->getType() == $type) {
                $collection->add($dashboard);
            }
        }

        return $collection;
    }

    /**
     * getChartDataForType
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @param $type
     * @return array
     */
    public function getChartDataForType($type)
    {
        $chartArray = [];

        foreach ($this->filterForType($type) as $dashboard) {
            $chartArray[] = $dashboard->getChartData();
        }

        return $chartArray;
    }
}
