<?php
namespace Nodes\Backend\Dashboard;

use Illuminate\Database\Eloquent\Collection;
use Nodes\Backend\Dashboard\Exceptions\MissingConfigException;
use Nodes\Backend\Dashboard\Exceptions\UnsupportedTypeException;
use Nodes\Backend\Dashboard\Types\IFrame;
use Nodes\Backend\Dashboard\Types\NodesStatistics\DailyStatistic;
use Nodes\Backend\Dashboard\Types\NodesStatistics\MonthlyStatistic;
use Nodes\Backend\Dashboard\Types\TableCount;

/**
 * Class DashboardCollection
 *
 * @author  Casper Rasmussen <cr@nodes.dk>
 * @package Nodes\Backend\Dashboard
 */
class DashboardCollection extends Collection
{
    /**
     * DashboardCollection constructor.
     *
     * @param array $configs
     * @throws \Exception
     */
    public function __construct($configs)
    {
        foreach ($configs as $config) {
            if (empty($config['type'])) {
                throw new MissingConfigException('Missing type');
            }

            switch ($config['type']) {
                case 'i-frame' :
                    $this->add(new IFrame($config));
                    break;
                case 'table-count' :
                    $this->add(new TableCount($config));
                    break;
                case 'nodes-statistics-daily' :
                    $this->add(new DailyStatistic($config));
                    break;
                case 'nodes-statistics-monthly' :
                    $this->add(new MonthlyStatistic($config));
                    break;
                default :
                    throw new UnsupportedTypeException(sprintf('%s is not supported', $config['type']));
            }
        }
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBarCharts()
    {
        $tableCountCollection = new Collection();
        foreach ($this as $dashboard) {
            if ($dashboard->getType() == 'bar-chart') {
                $tableCountCollection->add($dashboard);
            }
        }

        return $tableCountCollection;
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return array
     */
    public function getBarChartsAsChartData()
    {
        $chartArray = [];

        foreach ($this->getBarCharts() as $dashboard) {
            $chartArray[] = $dashboard->getChartData();
        }

        return $chartArray;
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLineCharts()
    {
        $tableCountCollection = new Collection();
        foreach ($this as $dashboard) {
            if ($dashboard->getType() == 'line-chart') {
                $tableCountCollection->add($dashboard);
            }
        }

        return $tableCountCollection;
    }

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return array
     */
    public function getLineChartsAsChartData()
    {
        $chartArray = [];

        foreach ($this->getLineCharts() as $dashboard) {
            $chartArray[] = $dashboard->getChartData();
        }

        return $chartArray;
    }
}