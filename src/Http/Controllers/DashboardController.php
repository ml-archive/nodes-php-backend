<?php

namespace Nodes\Backend\Http\Controllers;

use Illuminate\Routing\Controller;
use Nodes\Backend\Dashboard\DashboardCollection;

/**
 * Class DashboardController.
 */
class DashboardController extends Controller
{
    /**
     * Generate dashboard.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $dashboardCollection = new DashboardCollection(config('nodes.backend.dashboard.list'));

        return view('nodes.backend::dashboard.index', compact('dashboardCollection'));
    }
}
