<?php
namespace Nodes\Backend\Models\Role;

use Nodes\Backend\Models\User\User;
use Nodes\Database\Eloquent\Model;

/**
 * Class Role
 *
 * @package Nodes\Backend\Models\Role
 */
class Role extends Model
{
    /**
     * Database table
     *
     * @var string
     */
    protected $table = 'backend_roles';

    /**
     * Fillable columns
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'slug',
        'user_count',
        'default',
    ];

    /**
     * Typecast columns
     *
     * @var array
     */
    protected $casts = [
        'id' => 'integer',
        'user_count' => 'integer',
        'default' => 'boolean'
    ];

    /**
     * Role has many users
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function users()
    {
        return $this->hasMany(User::class, 'user_role', 'slug');
    }

    /**
     * Check if role is set as default
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function isDefault()
    {
        return (bool) $this->default;
    }
}
