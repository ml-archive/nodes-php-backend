<?php
namespace Nodes\Backend\Auth;

use Illuminate\Database\Eloquent\Model as IlluminateModel;
use Illuminate\Routing\Router as IlluminateRouter;
use Illuminate\Support\Facades\Cookie as CookieJar;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Nodes\Backend\Auth\Contracts\Authenticatable;
use Nodes\Exceptions\Exception as NodesException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Class Manager
 *
 * @package Nodes\Backend\Auth
 */
class Manager
{
    /**
     * Model used to authenticate users
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The session used by the guard
     *
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected $session;

    /**
     * Illuminate router
     *
     * @var \Illuminate\Routing\Router
     */
    protected $router;

    /**
     * Available auth providers
     *
     * @var array
     */
    protected $providers;

    /**
     * The currently authenticated user
     *
     * @var \Nodes\Backend\Auth\Contracts\Authenticatable
     */
    protected $user;

    /**
     * Constructor
     *
     * @access public
     * @param  \Illuminate\Database\Eloquent\Model                        $model
     * @param  \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param  \Illuminate\Routing\Router                                 $router
     * @param  array                                                      $providers
     */
    public function __construct(IlluminateModel $model, SessionInterface $session, IlluminateRouter $router, array $providers = [])
    {
        $this->model = $model;
        $this->session = $session;
        $this->router = $router;
        $this->providers = $providers;
    }

    /**
     * Authenticate user via providers
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  array $providers
     * @return \Nodes\Backend\Auth\Contracts\Authenticatable
     */
    public function authenticate(array $providers = [])
    {
        if (!empty($this->user)) {
            return $this->user;
        }

        // Spin through each of the registered authentication providers and attempt to
        // authenticate through one of them. This allows a developer to implement
        // and allow a number of different authentication mechanisms.
        $exceptionStack = [];
        foreach ($this->filterProviders($providers) as $provider) {
            /** @var $provider \Nodes\Backend\Auth\Contracts\Provider */
            try {
                // Authenticate user with current provider
                $user = $provider->authenticate($this->router->getCurrentRequest(), $this->router->getCurrentRoute());

                return $this->user = $user;
            } catch (NodesException $exception) {
                $exceptionStack[] = $exception;
            } catch (UnauthorizedHttpException $exception) {
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
     * Filter auth providers
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  array $providers
     * @return array
     */
    public function filterProviders(array $providers = [])
    {
        if (empty($providers)) {
            return $this->providers;
        }

        return array_intersect_key($this->providers, array_flip($providers));
    }

    /**
     * Throw the first exception from the exception stack
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  array $exceptionStack
     * @return void
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     */
    protected function throwUnauthorizedException(array $exceptionStack)
    {
        $exception = array_shift($exceptionStack);
        if ($exception === null) {
            $exception = new UnauthorizedHttpException(null, 'Failed to authenticate because of bad credentials or an invalid authorization header.');
        }

        throw $exception;
    }

    /**
     * Attempt to authenticate a user using credentials
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  array   $credentials
     * @param  boolean $remember
     * @param  boolean $login
     * @return boolean
     */
    public function attempt(array $credentials = [], $remember = false, $login = true)
    {
        // Retrieve user by credentials
        $user = $this->retrieveByCredentials($credentials);
        if (empty($user) || !$this->validateCredentials($user, $credentials)) {
            return false;
        }

        // Create login session
        if ($login) {
            $this->createLoginSession($user, $remember);
        }

        return true;
    }

    /**
     * Retrieve user by credentials
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  array $credentials
     * @return \Nodes\Backend\Auth\Contracts\Authenticatable|null
     */
    protected function retrieveByCredentials(array $credentials)
    {
        // Retrieve new query builder
        $query = $this->model->newQuery();

        foreach ($credentials as $key => $value) {
            // Ignore sensitive fields
            if (in_array($key, $this->model->getHidden())) {
                continue;
            }

            // Add where condition
            $query->where($key, '=', $value);
        }

        return $query->first();
    }

    /**
     * Validate user credentials
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  \Nodes\Backend\Auth\Contracts\Authenticatable $user
     * @param  array                                         $credentials
     * @return boolean
     */
    protected function validateCredentials(Authenticatable $user, array $credentials)
    {
        if (empty($credentials['password'])) {
            return false;
        }

        return Hash::check($credentials['password'], $user->getAuthPassword());
    }

    /**
     * Create login session
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Nodes\Backend\Auth\Contracts\Authenticatable $user
     * @param  boolean                                       $remember
     * @return $this
     */
    public function createLoginSession(Authenticatable $user, $remember = false)
    {
        // Flush existing sessions
        Session::flush();

        // Update login session
        $this->updateLoginSession($user->getAuthIdentifier());

        // Set "remember me" cookie
        if ($remember) {
            $this->createRememberTokenIfDoesntExist($user);
            $this->queueRecallerCookie($user);
        }

        // Set authenticated user
        $this->setUser($user);

        return $this;
    }

    /**
     * Update session by identifier
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string  $identifier
     * @return \Nodes\Backend\Auth\Manager
     */
    protected function updateLoginSession($identifier)
    {
        $this->session->set($this->getName(), $identifier);
        $this->session->migrate(true);
        return $this;
    }

    /**
     * Log the user out of the application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \Nodes\Backend\Auth\Manager
     */
    public function logout()
    {
        // Retrieve authenticated user
        $user = $this->getUser();

        // Refresh "remember me" token
        // as a security measure
        if (!empty($user)) {
            $this->refreshRememberToken($user);
        }

        // Clear session and delete cookie
        $this->clearUserDataFromStorage();

        // Clear authenticated user
        $this->user = null;

        // Mark user as logged out
        $this->loggedOut = true;

        return $this;
    }

    /**
     * Remove the user data from the session and cookies
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return \Nodes\Backend\Auth\Manager
     */
    public function clearUserDataFromStorage()
    {
        // Clear user session
        $this->session->remove($this->getName());

        // Delete "remember me" cookie
        CookieJar::queue(CookieJar::forget($this->getRecallerName()));

        return $this;
    }

    /**
     * Create a new remember token, if one doesn't already exist
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  \Nodes\Backend\Auth\Contracts\Authenticatable  $user
     * @return \Nodes\Backend\Auth\Manager
     */
    protected function createRememberTokenIfDoesntExist(Authenticatable $user)
    {
        $rememberToken = $user->getRememberToken();
        if (empty($rememberToken)) {
            $this->refreshRememberToken($user);
        }

        return $this;
    }

    /**
     * Refresh remember token
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  \Nodes\Backend\Auth\Contracts\Authenticatable  $user
     * @return \Nodes\Backend\Auth\Manager
     */
    protected function refreshRememberToken(Authenticatable $user)
    {
        // Generate token
        $token = str_random(60);

        // Set and update remember token
        $user->setRememberToken($token)->save();

        return $this;
    }

    /**
     * Queue the "remember me" cookie into the cookie jar
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Nodes\Backend\Auth\Contracts\Authenticatable $user
     * @return \Nodes\Backend\Auth\Manager
     */
    protected function queueRecallerCookie(Authenticatable $user)
    {
        // Generate cookie
        $value = $user->getAuthIdentifier().'|'.$user->getRememberToken();

        // Queue cookie into CookieJar
        CookieJar::queue($this->createRecaller($value));

        return $this;
    }

    /**
     * Check if user is already authenticated
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function check()
    {
        return !is_null($this->getUser());
    }

    /**
     * Determine if the current user is a guest
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function guest()
    {
        return !$this->check();
    }

    /**
     * Retrieve authenticated user
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \Nodes\Backend\Auth\Contracts\Authenticatable
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set authenticated user
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Nodes\Backend\Auth\Contracts\Authenticatable $user
     * @return $this
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get a unique identifier for the auth session value
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @static
     * @access public
     * @return string
     */
    public static function getName()
    {
        return 'nodes_backend_' . md5(__CLASS__);
    }

    /**
     * Get the name of the cookie used to store the "recaller"
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @static
     * @access public
     * @return string
     */
    public static function getRecallerName()
    {
        return 'nodes_backend_remember_' . md5(__CLASS__);
    }
}
