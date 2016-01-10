<?php
namespace Nodes\Backend\Auth;

use Illuminate\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as IlluminateAuthServiceProvider;
use Nodes\Backend\Auth\Exception\InvalidUserModelException;
use Nodes\Backend\Auth\Exception\InvalidUserRepositoryException;
use Nodes\Backend\Models\User\User;
use Nodes\Backend\Models\User\UserRepository;

/**
 * Class ServiceProvider
 *
 * @package Nodes\Backend\Auth
 */
class ServiceProvider extends IlluminateAuthServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var boolean
     */
    protected $defer = false;

    /**
     * Boot service provider
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access public
     * @param \Illuminate\Contracts\Auth\Access\Gate $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        // Register gates for user types
        $this->registerPolicies($gate);

        $gate->before(function ($authedUser, $ability) {
            if (!$authedUser) {
                return false;
            }
        });

        // Define super admin
        $gate->define('developer', function ($authedUser) {
            return $authedUser->user_role == 'developer';
        });

        $gate->define('super-admin', function ($authedUser) {
            return in_array($authedUser->user_role, ['developer', 'super-admin']);
        });

        // Define admin
        $gate->define('admin', function ($authedUser) {
            return in_array($authedUser->user_role, ['developer', 'super-admin', 'admin']);
        });

        // Define can edit, should only be possible to edit higher level than your self
        $gate->define('edit-user', function ($authedUser, $user = null) {

            // If the user is empty, it means they are creating it
            if(empty($user)) {
                return true;
            }

            // Don't change anything in the manager user
            if($user->email == config('nodes.backend.manager.email')) {
                return false;
            }

            // Developer, yes
            if($authedUser->user_role == 'developer') {
                return true;
            }

            // Super admins, yes
            if($authedUser->user_role == 'super-admin') {
                return true;
            }

            // Admins can edit other admins and users
            if($authedUser->user_role == 'admin' && $user->user_role != 'super-admin') {
                return true;
            }

            // If your self
            if($authedUser->id == $user->id) {
                return true;
            }

            // Others cant
            return false;
        });
    }

    /**
     * Register the service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access public
     * @return void
     */
    public function register()
    {
        $this->registerAuthModel();
        $this->registerAuthRepository();
        $this->registerAuthenticator();
        $this->setupBinding();
        $this->registerGate();
    }

    /**
     * Register authenticator
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access protected
     * @return void
     */
    protected function registerAuthenticator()
    {
        $this->app->singleton('nodes.backend.auth', function ($app) {
            $authenticator = $app->make(\Nodes\Backend\Auth\Authenticator::class);
            return new Manager($authenticator, $app['session.store']);
        });
    }

    /**
     * Register authentication user model
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access protected
     * @return void
     */
    protected function registerAuthModel()
    {
        $this->app->singleton('nodes.backend.auth.model', function ($app) {
            // Try and instantiate nodes.backend.backend user model
            $userModel = !empty(config('nodes.backend.auth.model')) ? app(config('nodes.backend.auth.model')) : app(\Nodes\Backend\Models\User\User::class);

            // Validate user model instance
            if (empty($userModel) || !($userModel instanceof User)) {
                throw new InvalidUserModelException('Missing or invalid backend user model');
            }

            return $userModel;
        });
    }

    /**
     * Register authentication user repository
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function registerAuthRepository()
    {
        $this->app->singleton('nodes.backend.auth.repository', function ($app) {
            // Try and instantiate nodes.backend.backend user model
            $userRepository = !empty(config('nodes.backend.auth.repository')) ? app(config('nodes.backend.auth.repository')) : app('Nodes\Backend\Models\User\UserRepository');

            // Validate user repository instance
            if (empty($userRepository) || !($userRepository instanceof UserRepository)) {
                throw new InvalidUserRepositoryException('Missing or invalid backend user repository');
            }

            return $userRepository;
        });
    }

    /**
     * Setup container binding
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @access protected
     * @return void
     */
    protected function setupBinding()
    {
        $this->app->bind(\Nodes\Backend\Auth\Manager::class, function ($app) {
            return $app['nodes.backend.auth'];
        });

        $this->app->bind(\Nodes\Backend\Auth\Contracts\Authenticatable::class, function ($app) {
            return $app['nodes.backend.auth']->getUser();
        });
    }

    /**
     * Register backend user for Gates
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function registerGate()
    {
        $this->app->singleton(GateContract::class, function ($app) {
            return new Gate($app, function () use ($app) {
                return $app['nodes.backend.auth']->getUser();
            });
        });
    }
}