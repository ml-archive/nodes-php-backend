<?php
namespace Nodes\Backend\Auth;

use Illuminate\Container\Container;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Routing\Router;
use Nodes\Backend\Auth\Exceptions\UnauthorizedException;
use Nodes\Backend\Auth\Contracts\Authenticatable;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class Authenticator
 *
 * @package Nodes\Backend\Auth
 */
class Authenticator
{
    /**
     * IoC container
     * @var \Illuminate\Container\Container
     */
    protected $container;

    /**
     * Router instance
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * Hasher
     * @var \Illuminate\Contracts\Hashing\Hasher
     */
    protected $hasher;

    /**
     * User repository
     * @var \Nodes\Backend\Models\User\UserRepository
     */
    protected $userRepository;

    /**
     * The provider used for authentication
     * @var \Nodes\Backend\Auth\Contracts\ProviderInterface
     */
    protected $providerUsed;

    /**
     * Constructor
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Illuminate\Container\Container       $container
     * @param  \Illuminate\Routing\Router            $router
     * @param  \Illuminate\Contracts\Hashing\Hasher  $hasher
     */
    public function __construct(Container $container, Router $router, Hasher $hasher)
    {
        $this->container = $container;
        $this->router = $router;
        $this->hasher = $hasher;
        $this->userRepository = app('nodes.backend.auth.repository');
    }

    /**
     * Retrieve a user by their unique identifier
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  integer $id
     * @return \Nodes\Backend\Auth\Contracts\Authenticatable|null
     */
    public function retrieveById($id)
    {
        return $this->userRepository->getById($id);
    }

    /**
     * Retrieve a user by by their unique identifier and "remember me" token
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  integer $id
     * @param  string  $token
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByToken($id, $token)
    {
        return $this->userRepository->getByIdAndRememberToken($id, $token);
    }

    /**
     * Update the "remember me" token for the given user in storage
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Nodes\Backend\Auth\Contracts\Authenticatable $user
     * @param  string $token
     * @return boolean
     */
    public function updateRememberToken(Authenticatable $user, $token)
    {
        $user->setRememberToken($token);
        return $user->save();
    }

    /**
     * Retrieve a user by the given credentials
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        return $this->userRepository->getByColumns($credentials);
    }

    /**
     * Validate a user against the given credentials
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Nodes\Backend\Auth\Contracts\Authenticatable  $user
     * @param  array $credentials
     * @return boolean
     */
    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        return !empty($credentials['password']) ? $this->hasher->check($credentials['password'], $user->getAuthPassword()) : false;
    }

    /**
     * Authenticate the current API request
     *
     * @author Morten Rugaard <moru@nodes.backend.sdk>
     *
     * @access public
     * @return mixed
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */
    public function authenticate()
    {
        // Exception stack
        $exceptionStack = [];

        // Spin through each of the registered authentication providers and attempt to
        // authenticate through one of them. This allows a developer to implement
        // and allow a number of different authentication mechanisms.
        foreach (config('nodes.backend.auth.providers', []) as $provider) {
            try {
                // Instantiate provider
                $provider = prepare_config_instance($provider);

                // Try and authenticate with current provider
                $user = $provider->authenticate($this->router->getCurrentRequest(), $this->router->getCurrentRoute());

                // Set provider used
                $this->providerUsed = $provider;

                // Set and return user
                return $user;
            } catch (UnauthorizedHttpException $exception) {
                // Add exception to internal stack
                $exceptionStack[] = $exception;
            } catch (BadRequestHttpException $exception) {
                // We won't add this exception to the stack as it's thrown when the provider
                // is unable to authenticate due to the correct authorization header not
                // being set. We will throw an exception for this below.
            }
        }

        $this->throwUnauthorizedException($exceptionStack);
    }

    /**
     * Throw the first exception from the exception stack
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  array $exceptionStack
     * @return void
     * @throws \Nodes\Backend\Auth\Exceptions\UnauthorizedException
     */
    protected function throwUnauthorizedException(array $exceptionStack)
    {
        // Return the first exception
        $exception = array_shift($exceptionStack);

        // If exception stack is empty, we'll create a fallback
        if ($exception === null) {
            $exception = new UnauthorizedException('Failed to authenticate because of bad credentials or an invalid authorization header.');
        }

        throw $exception;
    }

    /**
     * Get the provider used for authentication
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \Nodes\Backend\Auth\ProviderInterface
     */
    public function getProviderUsed()
    {
        return $this->providerUsed;
    }
}
