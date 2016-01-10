<?php
namespace Nodes\Backend\Http\Controllers;

use Illuminate\Routing\Controller;
use Nodes\Backend\Models\FailedJob\FailedJobRepository;

/**
 * Class FailedJobsController
 *
 * @author  Casper Rasmussen <cr@nodes.dk>
 * @package Nodes\Backend\Http\Controllers
 */
class FailedJobsController extends Controller
{
    /**
     * @var \Nodes\Backend\Models\FailedJob\FailedJobRepository
     */
    protected $failedJobRepository;

    /**
     * FailedJobsController constructor.
     *
     * @param \Nodes\Backend\Models\FailedJob\FailedJobRepository $failedJobRepository
     */
    public function __construct(FailedJobRepository $failedJobRepository)
    {
        if (\Gate::denies('developer')) {
            abort(403);
        }

        $this->failedJobRepository = $failedJobRepository;
    }

    /**
     * List failed jobs
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        // Retrieve data
        $failedJobs = $this->failedJobRepository->getPaginatedForBackend();

        return view('nodes.backend::failed-jobs.index', compact('failedJobs'));
    }

    /**
     * Restart all
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restartAll()
    {
        \Artisan::call('queue:retry', ['id' => ['all']]);

        return redirect()->route('nodes.backend.failed-jobs')->with('success', 'All failed job is restarted');
    }

    /**
     * Restart entry
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restart($id)
    {
        $failedJob = $this->failedJobRepository->getById($id);
        if (!$failedJob) {
            return redirect()->route('nodes.backend.failed-jobs')->with('error', 'The failed job was not found');
        }

        \Artisan::call('queue:retry', ['id' => [$id]]);

        return redirect()->route('nodes.backend.failed-jobs')->with('success', 'The failed job is restarted');
    }

    /**
     * Forget entry
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forget($id)
    {
        $failedJob = $this->failedJobRepository->getById($id);
        if (!$failedJob) {
            return redirect()->route('nodes.backend.failed-jobs')->with('error', 'The failed job was not found');
        }

        \Artisan::call('queue:forget', ['id' => [$id]]);

        return redirect()->route('nodes.backend.failed-jobs')->with('success', 'The failed job is forgotten');
    }
}
