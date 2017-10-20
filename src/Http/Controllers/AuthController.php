<?php

namespace Nodes\Backend\Http\Controllers;

use Exception;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Request;
use Nodes\Backend\Support\FlashRestorer;
use Nodes\Database\Exceptions\EntityNotFoundException;
use Illuminate\Support\Facades\Cookie;

/**
 * Class AuthController.
 */
class AuthController extends Controller
{
    /**
     * User repository.
     *
     * @var \Nodes\Backend\Models\User\UserRepository
     */
    protected $userRepository;

    /**
     * Constructor.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     */
    public function __construct()
    {
        $this->userRepository = app('nodes.backend.auth.repository');
    }

    /**
     * Login form.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function login()
    {
        if ($redirectUrl = \Input::get('redirect_url')) {
            \Cookie::queue(\Cookie::make('url_to_redirect_to_after_user_login', $redirectUrl, 5));
        } elseif ($redirectUrl = session('url_to_redirect_to_after_user_login')) {
            \Cookie::queue(\Cookie::make('url_to_redirect_to_after_user_login', $redirectUrl, 5));
        } else {
            \Cookie::queue(\Cookie::make('url_to_redirect_to_after_user_login', null, 5));
        }

        // If user is already authenticated,
        // redirect user to dashboard
        if (backend_user_check()) {
            return $this->redirectSuccess(new FlashRestorer);
        }

        return view('nodes.backend::login.default');
    }

    /**
     * Authenticate user.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function authenticate()
    {
        $urlToRedirectToAfterUserLogin = Cookie::get('url_to_redirect_to_after_user_login');

        // Retrieve credentials
        $data = Request::only('email', 'password', 'remember');

        // Authenticate user
        if (! backend_attempt(['email' => $data['email'], 'password' => $data['password']], isset($data['remember'])) {
            return redirect()->route('nodes.backend.login.form', [
                'redirect_url' => $urlToRedirectToAfterUserLogin
            ])->with('error', 'Invalid login. Try again.');
        }

        return $this->redirectSuccess($flashAlert = null, $urlToRedirectToAfterUserLogin);
    }

    /**
     * SSO login form.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sso()
    {
        $urlToRedirectToAfterUserLogin = Cookie::get('url_to_redirect_to_after_user_login');

        // Check for disabled feature
        if (! config('nodes.backend.manager.active', true)) {
            return redirect()->route('nodes.backend.login.form')->with('error', 'Manager auth is disabled.');
        }

        // Just login local
        if (env('APP_ENV') == 'local') {
            try {
                $user = $this->userRepository->getManagerUser();
            } catch (EntityNotFoundException $e) {
                return redirect()->route('nodes.backend.login.form')->with('error', 'Manager user was not found.');
            }

            // Authenticate user
            backend_user_login($user);

            return $this->redirectSuccess($flashAlert = null, $urlToRedirectToAfterUserLogin);
        }

        return redirect()->away(sprintf(env('NODES_MANAGER_URL'), env('APP_NAME'), env('APP_ENV')));
    }

    /**
     * Authenticate Nodes Manager.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function manager()
    {
        // Check for disabled feature
        if (! config('nodes.backend.manager.active', true)) {
            return redirect()->route('nodes.backend.login.form')->with('error', 'Manager auth is disabled.');
        }

        // Check the passed token vs a hash of email, constant and server token for current build
        if (hash('sha256', sprintf(env('NODES_MANAGER_SALT'), Request::get('email'), env('NODES_MANAGER_TOKEN'))) != Request::get('token')) {
            return redirect()->route('nodes.backend.login.form')->with('error', 'Manager token did not match');
        }

        try {
            // Retrieve the Nodes user
            $user = $this->userRepository->loginUserFromManager(Request::all());

            // Authenticate user
            backend_user_login($user);

            // Redirect into backend
            return $this->redirectSuccess(null, Cookie::get('url_to_redirect_to_after_user_login'));
        } catch (Exception $e) {
            try {
                // Notify bugsnag
                app('nodes.bugsnag')->notifyException($e, null, 'error');
            } catch (Exception $e) {
                // Fail silent
            }

            // Redirect to login form
            return redirect()->route('nodes.backend.login.form')->with('error', 'Failed to login through manager');
        }
    }

    /**
     * Log authenticated user out.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
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
     * Redirect user upon successful authentication.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @param  \Nodes\Backend\Support\FlashRestorer $flashAlert
     * @param string|null $urlToRedirectToAfterUserLogin
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function redirectSuccess($flashAlert = null, $urlToRedirectToAfterUserLogin = null)
    {
        // Retrieve authenticated backend user
        $backendUser = backend_user();

        // If backend user is required to change his/her password
        // we'll redirect the user to the "change password" form
        //
        // Otherwise we'll redirect the user to the designated route
        // based on the route alias from the backend config file
        if ($backendUser->change_password) {
            $redirectResponse = redirect()->route('nodes.backend.users.change-password')
                ->with('info', 'Please update your password');
        } else {
            if ($urlToRedirectToAfterUserLogin) {
                // redirect to previously visited page if available
                $redirectResponse = redirect()->to($urlToRedirectToAfterUserLogin)
                    ->with('success', 'Logged in as: '.$backendUser->email);
            } else {
                // redirect to success route from config
                $route = config('nodes.backend.auth.routes.success');
                $redirectResponse = ! empty($route) ? redirect()->route($route)->with('success',
                    'Logged in as: '.$backendUser->email) : redirect()->to('/admin');
            }
        }

        // Apply flash messages from previous route, if they are passed
        if ($flashAlert && $flashAlert instanceof FlashRestorer) {
            $flashAlert->apply($redirectResponse);
        }

        return $redirectResponse;
    }
}
