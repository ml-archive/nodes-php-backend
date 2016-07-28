<?php

namespace Nodes\Backend\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;
use Nodes\Backend\Models\FailedJob\FailedJobRepository;

/**
 * Class FailedJobsController.
 */
class FailedJobsController extends Controller
{
    /**
     * Failed job respository.
     *
     * @var \Nodes\Backend\Models\FailedJob\FailedJobRepository
     */
    protected $failedJobRepository;

    /**
     * FailedJobsController constructor.
     *
     * @param  \Nodes\Backend\Models\FailedJob\FailedJobRepository $failedJobRepository
     */
    public function __construct(FailedJobRepository $failedJobRepository)
    {
        if (Gate::denies('backend-developer')) {
            abort(403);
        }

        $this->failedJobRepository = $failedJobRepository;
    }

    /**
     * List failed jobs.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // Retrieve data
        $failedJobs = $this->failedJobRepository->getPaginatedForBackend();

        return view('nodes.backend::failed-jobs.index', compact('failedJobs'));
    }

    /**
     * Restart all.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restartAll()
    {
        Artisan::call('queue:retry', ['id' => ['all']]);

        return redirect()->route('nodes.backend.failed-jobs')->with('success', 'All failed job has been restarted');
    }

    /**
     * Restart entry.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restart($id)
    {
        $failedJob = $this->failedJobRepository->getById($id);
        if (! $failedJob) {
            return redirect()->route('nodes.backend.failed-jobs')->with('error', 'Failed job does not exist');
        }

        Artisan::call('queue:retry', ['id' => [$id]]);

        return redirect()->route('nodes.backend.failed-jobs')->with('success', sprintf('Failed job [%d] was restarted', $failedJob->id));
    }

    /**
     * Forget job.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forget($id)
    {
        $failedJob = $this->failedJobRepository->getById($id);
        if (! $failedJob) {
            return redirect()->route('nodes.backend.failed-jobs')->with('error', 'Failed job does not exist');
        }

        Artisan::call('queue:forget', ['id' => [$id]]);

        return redirect()->route('nodes.backend.failed-jobs')->with('success', 'Failed job has been removed');
    }
}
