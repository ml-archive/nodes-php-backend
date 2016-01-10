<?php
namespace Nodes\Backend\Http\Controllers;

use Illuminate\Routing\Controller;
use Nodes\Database\Exceptions\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class AuthController
 *
 * @package Nodes\Backend\Http\Controllers
 */
class AuthController extends Controller
{
    /**
     * User repository
     *
     * @var \Nodes\Backend\Models\User\UserRepository
     */
    protected $userRepository;

    /**
     * Session
     *
     * @var \Symfony\Component\HttpFoundation\Session\SessionInterface
     */
    protected $session;

    /**
     * Constructor
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access public
     * @param  \Symfony\Component\HttpFoundation\Session\SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->userRepository = app('nodes.backend.auth.repository');
        $this->session = $session;
    }

    /**
     * Login form
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     * @access public
     * @return \Illuminate\View\View
     */
    public function login()
    {
        // If user is already authenticated,
        // redirect user to dashboard
        if (backend_user_check()) {
            return $this->redirectSuccess();
        }

        return view('nodes.backend::login.default');
    }

    /**
     * Authenticate user
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access public
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate()
    {
        // Retrieve credentials
        $data = \Input::only('email', 'password', 'remember');

        // Authenticate user
        if (!backend_attempt(['email' => $data['email'], 'password' => $data['password']], (bool)$data['remember'])) {
            return redirect()->back()->with('error', 'Invalid login. Try again.');
        }

        return $this->redirectSuccess();
    }

    /**
     * SSO login form
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access public
     * @return \Illuminate\View\View
     */
    public function sso()
    {
        // Just login local
        if (env('APP_ENV') == 'local') {
            try {
                $user = $this->userRepository->getManagerUser();
            } catch (EntityNotFoundException $e) {
                return redirect()->back()->with('error', 'Manager user was not found.');
            }

            // Authenticate user
            backend_user_login($user);

            return $this->redirectSuccess();
        }

        return redirect()->away(sprintf(env('NODES_MANAGER_URL'), env('APP_NAME'), env('APP_ENV')));
    }

    /**
     * Authenticate Nodes Manager
     *
     * @author cr@nodes.dk
     * @return \Illuminate\Http\RedirectResponse
     */
    public function manager()
    {
        // Check the passed token vs a hash of email, constant and server token for current build
        if (hash('sha256', sprintf(env('NODES_MANAGER_SALT'), \Input::get('email'), env('NODES_MANAGER_TOKEN'))) != \Input::get('token')) {
            return redirect()->route('nodes.backend.login.form')->with('error', 'Manager token did not match');
        }

        try {
            // Retrieve the Nodes user
            $user = $this->userRepository->loginUserFromManager(\Input::get());

            // Authenticate user
            backend_user_login($user);

            // Redirect into backend
            return $this->redirectSuccess();

        } catch(\Exception $e) {
            // Notify bugsnag
            try {
                app('nodes.bugsnag')->notifyException($e, null, 'error');
            } catch(\Exception $e) {
                // Fail silent
            }

            // Redirect to login form
            return redirect()->route('nodes.backend.login.form')->with('error', 'Failed to login through manager');
        }
    }

    /**
     * Log authenticated user out
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access public
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout()
    {
        // Clear user session
        backend_user_logout();

        // Redirect to login form
        return redirect()->route('nodes.backend.login.form')->with('info', 'You are now logged out.');
    }

    /**
     * Redirect user upon successfully authenticate
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access protected
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectSuccess()
    {
        if(backend_user()->change_password) {
            return redirect()->route('nodes.backend.users.change-password')->with('info', 'Please update your password');
        }

        $route = config('nodes.backend.auth.routes.success');
        return $route ? redirect()->route($route)->with('success', 'Logged in as: ' . backend_user()->email) : redirect()->to('/admin');
    }
}
