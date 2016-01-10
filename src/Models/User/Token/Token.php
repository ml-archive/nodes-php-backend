<?php
namespace Nodes\Backend\Models\User\Token;

use Nodes\Database\Eloquent\Model;

/**
 * Class Token
 *
 * @package NodesBackend\Models\User\Token
 */
class Token extends Model
{
    /**
     * Database table
     *
     * @var string
     */
    protected $table = 'backend_user_tokens';

    /**
     * Fillable columns
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'token',
        'expire'
    ];

    /*
    |-----------------------------------------------------------
    | Relations
    |-----------------------------------------------------------
    */

    /**
     * Token belongs to user
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(\Nodes\Backend\Models\User\User::class, 'user_id');
    }
}
