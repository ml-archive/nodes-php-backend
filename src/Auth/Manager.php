<?php
namespace Nodes\Backend\Auth;

use Illuminate\Support\Facades\Cookie as CookieJar;
use Illuminate\Support\Facades\Session;
use Nodes\Backend\Auth\Contracts\Authenticatable;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class Manager
 *
 * @package Nodes\Backend\Auth
 */
class Manager
{
    /**
     * The currently authenticated user
     *
     * @var \Nodes\Backend\Auth\Contracts\Authenticatable
     */
    protected $user;

    /**
     * The user we last attempted to retrieve
     *
     * @var \Nodes\Backend\Auth\Contracts\Authenticatable
     */
    protected $lastAttempted;

    /**
     * Indicates if the user was authenticated via a recaller cookie
     *
     * @var boolean
     */
    protected $viaRemember = false;

    /**
     * Nodes backend authenticator
     *
     * @var \Nodes\Backend\Auth\Authenticator
     */
    protected $auth;

    /**
     * The session used by the guard
     *
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected $session;

    /**
     * The request instance
     *
     * @var \Symfony\Component\HttpFoundation\Request
     */
    protected $request;

    /**
     * Indicates if the logout method has been called
     *
     * @var boolean
     */
    protected $loggedOut = false;

    /**
     * Indicates if a token user retrieval has been attempted
     *
     * @var boolean
     */
    protected $tokenRetrievalAttempted = false;

    /**
     * Constructor
     *
     * @access public
     * @param  \Nodes\Backend\Auth\Authenticator                          $auth
     * @param  \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     * @param  \Symfony\Component\HttpFoundation\Request                  $request
     */
    public function __construct(Authenticator $auth, SessionInterface $session, Request $request = null)
    {
        $this->auth = $auth;
        $this->session = $session;
        $this->request = $request;
    }

    /**
     * Determine if the current user is authenticated
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function check()
    {
        return (bool) $this->user();
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
     * Get the currently authenticated user
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \Nodes\Backend\Auth\Contracts\Authenticatable|null
     */
    public function user()
    {
        // Do nothing if user is logged out
        if ($this->loggedOut) {
            return false;
        }

        // Use user from "cache"
        if (!empty($this->user)) {
            return $this->user;
        }

        // Retrieve user from session
        $userId = $this->session->get($this->getName());

        if (!empty($userId)) {
            $user = $this->auth->retrieveById($userId);
            if (!empty($user)) {
                // Set authenticated user
                $this->setUser($user);
                return $user;
            }
        }

        // Retrieve user from cookie
        $recaller = $this->getRecaller();
        if (!empty($recaller)) {
            $user = $this->getUserByRecaller($recaller);
            if (!empty($user)) {
                // Update session
                $this->updateSession($user->getAuthIdentifier());

                // Set authenticated user
                $this->setUser($user);
                return $user;
            }
        }

        return false;
    }

    /**
     * Log a user into the application without sessions or cookies
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  array $credentials
     * @return boolean
     */
    public function once(array $credentials = [])
    {
        if ($this->validate($credentials)) {
            $this->setUser($this->lastAttempted);
            return true;
        }

        return false;
    }

    /**
     * Attempt to authenticate a user using the given credentials
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
        $user = $this->auth->retrieveByCredentials($credentials);

        // Set last attempted user
        $this->lastAttempted = $user;

        // If an implementation of UserInterface was returned, we'll ask the provider
        // to validate the user against the given credentials, and if they are in
        // fact valid we'll log the users into the application and return true.
        if ((!empty($user) && $this->auth->validateCredentials($user, $credentials))) {
            if ($login) {
                $this->login($user, $remember);
            }
            return true;
        }

        return false;
    }

    /**
     * Validate a user's credentials
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  array  $credentials
     * @return boolean
     */
    public function validate(array $credentials = [])
    {
        return $this->attempt($credentials, false, false);
    }

    /**
     * Log a user into the nodes.backend
     *
     * @suthor Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Nodes\Backend\Auth\Contracts\Authenticatable $user
     * @param  boolean $remember
     * @return \Nodes\Backend\Auth\Manager
     */
    public function login(Authenticatable $user, $remember = false)
    {
        // Flush session
        Session::flush();

        // Update session with user ID
        $this->updateSession($user->getAuthIdentifier());

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
     * Log the given user ID into the application
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  mixed   $id
     * @param  boolean $remember
     * @return \Nodes\Backend\Auth\Contracts\Authenticatable
     */
    public function loginUsingId($id, $remember = false)
    {
        // Update session with user ID
        $this->session->set($this->getName(), $id);

        // Retrieve user by ID
        $user = $this->provider->retrieveById($id);

        // Authenticate user
        $this->login($user, $remember);

        return $user;
    }

    /**
     * Determine if the user was authenticated via "remember me" cookie
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function viaRemember()
    {
        return (bool) $this->viaRemember;
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
        $user = $this->user();

        // Clear session and delete cookie
        $this->clearUserDataFromStorage();

        // Refresh "remember me" token
        // as a security measure
        if (!empty($user)) {
            $this->refreshRememberToken($user);
        }

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
     * Update the session with the given ID
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  string  $id
     * @return \Nodes\Backend\Auth\Manager
     */
    protected function updateSession($id)
    {
        $this->session->set($this->getName(), $id);
        $this->session->migrate(true);
        return $this;
    }

    /**
     * Refresh the remember token for the user
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
        $user->setRememberToken($token);
        $this->auth->updateRememberToken($user, $token);

        return $this;
    }

    /**
     * Create a new remember token for the user if one doesn't already exist
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
     * Retrieve user by "remember me" cookie
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  string $recaller
     * @return \Nodes\Backend\Auth\Contracts\Authenticatable|null|boolean
     */
    protected function getUserByRecaller($recaller)
    {
        if (!$this->validRecaller($recaller) || $this->tokenRetrievalAttempted) {
            return false;
        }

        // Mark token retrieval attempt
        $this->tokenRetrievalAttempted = true;

        // Parse cookie
        list($id, $token) = explode('|', $recaller, 2);

        // Retrieve user by ID and token
        $user = $this->provider->retrieveByToken($id, $token);
        if (!empty($user)) {
             $this->viaRemember = true;
        }

        return $user;
    }

    /**
     * Get the user ID from the "remember me" cookie
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return string
     */
    protected function getRecallerId()
    {
        $recaller = $this->getRecaller();
        if ($this->validRecaller($recaller)) {
            return head(explode('|', $recaller));
        }
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
        $value = $user->getAuthIdentifier().'|'.$user->getRememberToken();

        CookieJar::queue($this->createRecaller($value));

        return $this;
    }

    /**
     * Determine if the "remember me" cookie is in a valid format
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  string $recaller
     * @return boolean
     */
    protected function validRecaller($recaller)
    {
        if (!is_string($recaller) || !str_contains($recaller, '|')) {
            return false;
        }

        $segments = explode('|', $recaller);
        return count($segments) == 2 && trim($segments[0]) !== '' && trim($segments[1]) !== '';
    }

    /**
     * Get the decrypted "remember me" cookie for the request
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return string|null
     */
    protected function getRecaller()
    {
        return $this->getRequest()->cookies->get($this->getRecallerName());
    }

    /**
     * Create a "remember me" cookie for a given ID
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @param  string  $value
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    protected function createRecaller($value)
    {
        return CookieJar::forever($this->getRecallerName(), $value);
    }

    /**
     * Get the session store used by the guard
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \Illuminate\Session\Store
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Get the current request instance
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \Symfony\Component\HttpFoundation\Request
     */
    public function getRequest()
    {
        return $this->request ?: Request::createFromGlobals();
    }

    /**
     * Set the current request instance
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Symfony\Component\HttpFoundation\Request
     * @return \Nodes\Backend\Auth\Manager
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Set authenticated user
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @param  \Nodes\Backend\Auth\Contracts\Authenticatable $user
     * @return \Nodes\Backend\Auth\Manager
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
        Session::put('user', $user);

        return $this;
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
     * Get the last user we attempted to authenticate
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return \Nodes\Backend\Auth\Contracts\Authenticatable
     */
    public function getLastAttempted()
    {
        return $this->lastAttempted;
    }

    /**
     * Get a unique identifier for the auth session value
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getName()
    {
        return 'nodes_backend_' . md5(get_class($this));
    }

    /**
     * Get the name of the cookie used to store the "recaller"
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return string
     */
    public function getRecallerName()
    {
        return 'nodes_backend_remember_' . md5(get_class($this));
    }
}
