<?php
namespace Nodes\Backend\Models\FailedJob;

use Nodes\Database\Eloquent\Model;
use Nodes\Database\Support\Traits\Date;

/**
 * Class FailedJob
 *
 * @package Nodes\Backend\Models\FailedJob
 */
class FailedJob extends Model
{
    use Date;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'failed_jobs';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['failed_at'];
}
