<?php
namespace Nodes\Backend\Auth\Providers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Nodes\Api\Auth\Exceptions\MissingUserModelException;
use Nodes\Backend\Auth\Contracts\Provider;
use Nodes\Backend\Auth\Exceptions\InvalidTokenException;
use Nodes\Backend\Auth\Exceptions\TokenExpiredException;
use Nodes\Backend\Support\Helper as NodesHelper;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class Token
 *
 * @package Nodes\Backend\Auth\Providers
 */
class Token implements Provider
{
    /**
     * User model
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * Table name of user table
     *
     * @var string
     */
    protected $userTable;

    /**
     * Table name of access token
     *
     * @var string
     */
    protected $tokenTable;

    /**
     * Column mapping
     *
     * @var array
     */
    protected $tokenColumns = [];

    /**
     * Lifetime of token
     *
     * @var string
     */
    protected $tokenLifetime;

    /**
     * Constructor
     *
     * @access public
     * @throws \Nodes\Api\Auth\Exceptions\TokenMethodException
     */
    public function __construct()
    {
        // Set user model
        $this->userModel = app('nodes.backend.auth.model');

        // Set token table
        $this->setTokenSettings();
    }

    /**
     * Set token settings
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return \Nodes\Backend\Auth\Providers\Token
     */
    protected function setTokenSettings()
    {
        // Set table name of access token
        $this->tokenTable = config('nodes.backend.auth.token.table', 'backend_user_tokens');

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

        // Set lifetime of token
        $this->tokenLifetime = config('nodes.backend.auth.token.lifetime', null);

        return $this;
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
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     * @throws \Nodes\Backend\Auth\Exceptions\InvalidTokenException
     * @throws \Nodes\Backend\Auth\Exceptions\TokenExpiredException
     */
    public function authenticate(Request $request, Route $route)
    {
        // Validate "Authorization" header
        if (empty($request->header('authorization'))) {
            throw new BadRequestHttpException('Authorization header not provided', null, 400);
        }

        // Set token from "Authorization" header
        $this->token = (string) $request->header('authorization');

        // Authenticate user by token
        $user = $this->getUserByToken();
        if (empty($user)) {
            throw new InvalidTokenException('No user associated with provided token');
        }

        // If an expire time has been set in config
        // we need to validate and maybe update it as well
        if ($this->hasTokenLifetime()) {
            // Validate tokens expiry date
            if (strtotime($user->token->expire) < time()) {
                throw new TokenExpiredException('Token has expired');
            }

            // Update expire date
            $this->updateTokenExpiry();
        }

        return $user;
    }

    /**
     * Retrieve user by token
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return mixed|null
     */
    protected function getUserByToken()
    {
        $user = $this->generateQuery()->first();
        return $user;
    }

    /**
     * Update token's expire time
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access protected
     * @return boolean
     */
    protected function updateTokenExpiry()
    {
        return (bool) $this->generateQuery()->update([
            $this->getTokenColumn('expire') => Carbon::parse('now ' . $this->getTokenLifetime()),
        ]);
    }

    /**
     * Generate base query used to retrieve user by token
     * and update an existing tokens expire time
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access private
     * @return mixed
     */
    private function generateQuery()
    {
        return $this->getUserModel()
                    ->select([
                        $this->getUserTable() . '.*',
                    ])
                    ->join($this->getTokenTable(), $this->getTokenColumn('user_id'), '=', $this->getUserTable() . '.id')
                    ->where($this->getTokenColumn('token'), '=', $this->getToken());
    }

    /**
     * Retrieve user model
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return \Illuminate\Database\Eloquent\Model
     * @throws \Nodes\Api\Auth\Exceptions\MissingUserModelException
     */
    protected function getUserModel()
    {
        if (empty($this->userModel)) {
            throw new MissingUserModelException('No user model set for API authentication');
        }

        return $this->userModel;
    }

    /**
     * Retrieve user table
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return string
     */
    protected function getUserTable()
    {
        return $this->getUserModel()->getTable();
    }

    /**
     * Retrieve token from "Authorization" header
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return string
     */
    protected function getToken()
    {
        return $this->token;
    }

    /**
     * Retrieve token table
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return string
     */
    protected function getTokenTable()
    {
        return $this->tokenTable;
    }

    /**
     * Retrieve token columns
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  string $column
     * @return string
     */
    protected function getTokenColumn($column)
    {
        if (!array_key_exists($column, $this->tokenColumns)) {
            // This should never happen. If it does, then it means
            // that someone is a moron and has removed required
            // settings from the config files. Better safe than sorry.
            throw new BadRequestHttpException;
        }

        return $this->tokenColumns[$column];
    }

    /**
     * Retrieve token lifetime
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return integer
     */
    protected function getTokenLifetime()
    {
        return $this->tokenLifetime;
    }

    /**
     * Check if token has a lifetime
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return boolean
     */
    protected function hasTokenLifetime()
    {
        return !empty($this->tokenLifetime);
    }
}
