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
     * Database table
     *
     * @var string
     */
    protected $table = 'failed_jobs';

    protected $dates = [
        'failed_at'
    ];
}
