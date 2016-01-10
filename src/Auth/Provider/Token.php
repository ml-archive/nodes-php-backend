<?php
namespace Nodes\Backend\Auth\Provider;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Nodes\Backend\Auth\Contracts\Provider;
use Nodes\Backend\Auth\Exceptions\InvalidTokenException;
use Nodes\Backend\Auth\Exceptions\MissingTokenException;
use Nodes\Backend\Auth\Exceptions\TokenExpiredException;
use Nodes\Backend\Support\Helper as NodesHelper;

/**
 * Class Token
 *
 * @package Nodes\Auth\Provider
 */
class Token implements Provider
{
    /**
     * User model
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Table name of user table
     * @var string
     */
    protected $userTable;

    /**
     * Table name of access token
     * @var string
     */
    protected $tokenTable;

    /**
     * Column mapping
     * @var array
     */
    protected $tokenColumns = [];

    /**
     * Lifetime of token
     * @var string
     */
    protected $tokenLifetime;

    /**
     * Constructor
     *
     * @access public
     * @throws \Nodes\Auth\Exception\TokenMethodException
     */
    public function __construct()
    {
        // USER MODEL CONFIG
        // Set user model
        $this->model = app('nodes.backend.auth.model');

        // Set user table name
        $this->userTable = $this->model->getTable();

        // TOKEN CONFIG
        // Set table name of access token
        $this->tokenTable = config('nodes.backend.auth.token.table', 'backend_user_tokens');

        // Set lifetime of token
        $this->tokenLifetime = config('nodes.backend.auth.token.lifetime', null);

        // Prepend table name to mapped columns
        // and make them available for later use
        $columns = config('nodes.backend.auth.token.columns', [
            'user_id' => 'user_id',
            'token' => 'token',
            'expire' => 'expire'
        ]);
        foreach ($columns as $key => $field) {
            $this->tokenColumns[$key] = $this->tokenTable . '.' . $field;
        }
    }

    /**
     * Authenticate
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Routing\Route $route
     * @return object
     * @throws \Nodes\Backend\Auth\Exceptions\InvalidTokenException
     * @throws \Nodes\Backend\Auth\Exceptions\MissingTokenException
     * @throws \Nodes\Backend\Auth\Exceptions\TokenExpiredException
     */
    public function authenticate(Request $request, Route $route)
    {
        // Retrieve token
        $token = NodesHelper::headers('Authorization');

        // Make sure we have a token
        if (empty($token)) {
            throw new MissingTokenException('No token was provided');
        }

        // Retrieve user by access token
        $user = $this->getUserByToken($token);
        if (empty($user)) {
            throw new InvalidTokenException('Invalid token received');
        }

        // If an expire time has been set in config
        // we need to validate and maybe update it as well
        if (!empty($this->tokenLifetime)) {
            // Validate tokens expiry date
            if (strtotime($user->expire) < time()) {
                throw new TokenExpiredException('Token has expired');
            }

            // Update expire date
            $this->updateTokenExpiry($token);
        }

        // Now that our user has been authenticated,
        // we'll remove the sensitive fields the user object
        unset($this->tokenColumns);

        return $user;
    }

    /**
     * Get user by token
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access private
     * @param  string $token
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private function getUserByToken($token)
    {
        return $this->model->join($this->tokenTable, $this->tokenColumns['user_id'], '=', $this->userTable . '.id')
                           ->where($this->tokenColumns['token'], $token)
                           ->first([
                               $this->userTable . '.*',
                               $this->tokenColumns['token'],
                               $this->tokenColumns['expire']
                           ]);
    }

    /**
     * Update token's expire time
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string $token
     * @return bool
     */
    private function updateTokenExpiry($token)
    {
        return (bool) $this->model->join($this->tokenTable, $this->tokenColumns['user_id'], '=', $this->userTable . '.id')
                                  ->where($this->tokenColumns['token'], $token)
                                  ->update([
                                      $this->tokenColumns['expire'] => Carbon::parse('now ' . $this->tokenLifetime)
                                  ]);
    }
}
