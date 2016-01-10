<?php
namespace Nodes\Backend\Models\FailedJob;

use Nodes\Database\Eloquent\Repository as NodesRepository;

/**
 * Class FailedJobRepository
 *
 * @package Nodes\Backend\Models\FailedJob
 */
class FailedJobRepository extends NodesRepository
{
    /**
     * FailedJobRepository constructor
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param \Nodes\Backend\Models\FailedJob\FailedJob $model
     */
    public function __construct(FailedJob $model)
    {
        $this->setupRepository($model);
    }

    /**
     * Retrieve all failed jobs paginated
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param  integer  $limit
     * @param  array    $fields
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getPaginatedForBackend($limit = 25, $fields = ['*'])
    {
        return $this->paginate($limit, $fields);
    }
}
