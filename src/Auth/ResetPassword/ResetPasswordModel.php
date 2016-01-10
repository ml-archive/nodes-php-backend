<?php
namespace Nodes\Backend\Auth\ResetPassword;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Model
 *
 * @package Nodes\Backend\Auth\ResetPassword
 */
class ResetPasswordModel extends Model
{
    /**
     * Database table
     * @var string
     */
    protected $table = 'backend_reset_password_tokens';

    /**
     * Do not touch timestamps
     * @var boolean
     */
    public $timestamps = false;

    /**
     * Carbon dates
     * @var array
     */
    protected $dates = [
        'expire_at'
    ];

    /**
     * Fillable columns
     * @var array
     */
    protected $fillable = [
        'token',
        'used',
        'expire_at'
    ];

    /**
     * Constructor
     *
     * @access public
     * @param  array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('nodes.backend.reset-password.table', 'backend_reset_password_tokens');
    }

    /**
     * Check if token is expired
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function isExpired()
    {
        return (bool) Carbon::now()->gt($this->expire_at);
    }

    /**
     * Check if token has already been used
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function isUsed()
    {
        return (bool) $this->used;
    }

    /**
     * Mark token as used
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function markAsUsed()
    {
        return (bool) $this->update(['used' => 1]);
    }
}
