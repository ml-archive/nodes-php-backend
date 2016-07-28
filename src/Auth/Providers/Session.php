<?php

namespace Nodes\Backend\Auth\Providers;

use Illuminate\Database\Eloquent\Model as IlluminateModel;
use Illuminate\Http\Request as IlluminateRequest;
use Illuminate\Routing\Route as IlluminateRoute;
use Illuminate\Support\Facades\Hash;
use Nodes\Backend\Auth\Contracts\Authenticatable;
use Nodes\Backend\Auth\Manager;
use Nodes\Backend\Auth\Contracts\Provider;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class Session.
 */
class Session implements Provider
{
    /**
     * User model we should use to authenticate.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The session used by the guard.
     *
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected $session;

    /**
     * Indicates if a token user retrieval has been attempted.
     *
     * @var bool
     */
    protected $tokenRetrievalAttempted = false;

    /**
     * Session constructor.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @param  \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     */
    public function __construct(IlluminateModel $model, SessionInterface $session)
    {
        $this->model = $model;
        $this->session = $session;
    }

    /**
     * Authenticate.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Routing\Route $route
     * @return object|bool
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */
    public function authenticate(IlluminateRequest $request, IlluminateRoute $route)
    {
        // Retrieve user from session
        $userId = $this->session->get(Manager::getName());
        if (! empty($userId) && ($user = $this->model->where('id', '=', $userId)->first())) {
            return $user;
        }

        // Retrieve user from cookie
        $recaller = $this->getRecaller();
        if (! empty($recaller) && ($user = $this->getUserByRecaller($recaller))) {
            $this->updateSession($user->getAuthIdentifier());

            return $user;
        }

        throw new UnauthorizedHttpException(null, 'Could not find user in either sessions or cookies', null, 401);
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  array   $credentials
     * @param  bool $remember
     * @param  bool $login
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false, $login = true)
    {
        // Build query to retrieve model
        $model = clone $this->model;
        foreach ($credentials as $key => $value) {
            if (in_array($key, $model->getHidden())) {
                continue;
            }

            // Add credential to condition
            $model->where($key, '=', $value);
        }

        // Retrieve user by query
        $user = $model->first();

        // Set last attempted user
        $this->lastAttempted = $user;

        // If an implementation of UserInterface was returned, we'll ask the provider
        // to validate the user against the given credentials, and if they are in
        // fact valid we'll log the users into the application and return true.
        if (! empty($user) && $this->auth->validateCredentials($user, $credentials)) {
            if ($login) {
                $this->login($user, $remember);
            }

            return true;
        }

        return false;
    }

    /**
     * Validate user credentials.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  \Nodes\Backend\Auth\Contracts\Authenticatable $user
     * @param  array $credentials
     * @return bool
     */
    protected function validateCredentials(Authenticatable $user, array $credentials)
    {
        if (empty($credentials['password'])) {
            return false;
        }

        return Hash::check($credentials['password'], $user->getAuthPassword());
    }

    /**
     * Retrieve user by "remember me" cookie.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $recaller
     * @return \Nodes\Backend\Auth\Contracts\Authenticatable|null|bool
     */
    protected function getUserByRecaller($recaller)
    {
        if (! $this->validRecaller($recaller) || $this->tokenRetrievalAttempted) {
            return false;
        }

        // Mark token retrieval attempt
        $this->tokenRetrievalAttempted = true;

        // Parse cookie
        list($id, $token) = explode('|', $recaller, 2);

        // Retrieve user by ID and token
        $user = $this->model->where('id', '=', $id)->where('remember_token', '=', $token)->first();
        if (! empty($user)) {
            $this->viaRemember = true;
        }

        return $user;
    }

    /**
     * Determine if the "remember me" cookie is in a valid format.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @param  string $recaller
     * @return bool
     */
    protected function validRecaller($recaller)
    {
        if (! is_string($recaller) || ! str_contains($recaller, '|')) {
            return false;
        }

        $segments = explode('|', $recaller);

        return count($segments) == 2 && trim($segments[0]) !== '' && trim($segments[1]) !== '';
    }

    /**
     * Get the decrypted "remember me" cookie for the request.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return string|null
     */
    protected function getRecaller()
    {
        return IlluminateRequest::createFromGlobals()->cookies->get(Manager::getRecallerName());
    }
}
