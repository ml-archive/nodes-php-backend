<?php

namespace Nodes\Backend\Auth;

use Illuminate\Auth\Access\Gate;
use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as IlluminateAuthServiceProvider;
use Nodes\Backend\Auth\Exceptions\InvalidUserModelException;
use Nodes\Backend\Auth\Exceptions\InvalidUserRepositoryException;
use Nodes\Backend\Http\Middleware\ApiAuth as NodesBackendHttpMiddlewareApiAuth;
use Nodes\Backend\Models\User\User;
use Nodes\Backend\Models\User\UserRepository;

/**
 * Class ServiceProvider.
 */
class ServiceProvider extends IlluminateAuthServiceProvider
{
    /**
     * Boot service provider.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @param \Illuminate\Contracts\Auth\Access\Gate $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        // Register middlewares with router
        $this->app['router']->aliasMiddleware('backend.api.auth', NodesBackendHttpMiddlewareApiAuth::class);

        // Define gates and policies
        if (config('nodes.backend.auth.gates.define', true)) {
            $this->defineGates($gate);
        }
    }

    /**
     * Register the service provider.
     *
     * @author Morten Rugaard <moru@nodes.dk>
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
     * Register authenticator.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @return void
     */
    protected function registerAuthenticator()
    {
        $this->app->singleton('nodes.backend.auth', function ($app) {
            $providers = prepare_config_instances(config('nodes.backend.auth.providers'));

            return new Manager($app['nodes.backend.auth.model'], $app['session.store'], $app['router'], $providers);
        });
    }

    /**
     * Register authentication user model.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function registerAuthModel()
    {
        $this->app->singleton('nodes.backend.auth.model', function ($app) {
            // Try and instantiate nodes.backend.backend user model
            $userModel = ! empty(config('nodes.backend.auth.model')) ? prepare_config_instance(config('nodes.backend.auth.model')) : app(\Nodes\Backend\Models\User\User::class);

            // Validate user model instance
            if (empty($userModel) || ! $userModel instanceof User) {
                throw new InvalidUserModelException('Missing or invalid backend user model');
            }

            return $userModel;
        });
    }

    /**
     * Register authentication user repository.
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @return void
     */
    protected function registerAuthRepository()
    {
        $this->app->singleton('nodes.backend.auth.repository', function ($app) {
            // Try and instantiate nodes.backend.backend user model
            $userRepository = ! empty(config('nodes.backend.auth.repository')) ? app(config('nodes.backend.auth.repository')) : app(\Nodes\Backend\Models\User\UserRepository::class);

            // Validate user repository instance
            if (empty($userRepository) || ! ($userRepository instanceof UserRepository)) {
                throw new InvalidUserRepositoryException('Missing or invalid backend user repository');
            }

            return $userRepository;
        });
    }

    /**
     * Setup container binding.
     *
     * @author Morten Rugaard <moru@nodes.dk>
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
     * Register authenticated backend user for Gates.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
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

    /**
     * Define gates for default user roles.
     *
     * @author Casper Rasmussen <cr@nodes.dk>
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate $gate
     */
    private function defineGates(GateContract $gate)
    {
        // Register gates for user types
        $this->registerPolicies($gate);

        // Make sure we have an authenticated user at the gate
        $gate->before(function ($authedUser, $ability) {
            if (empty($authedUser)) {
                return false;
            }
        });

        // Define gate to check if authenticated user is a developer
        $gate->define('backend-developer', function (User $authedUser) {
            return $authedUser->user_role == 'developer';
        });

        // Define gate to check if authenticated user is a SUPER admin
        $gate->define('backend-super-admin', function (User $authedUser) {
            return in_array($authedUser->user_role, ['developer', 'super-admin']);
        });

        // Define gate to check if authenticated user is an admin
        $gate->define('backend-admin', function (User $authedUser) {
            return in_array($authedUser->user_role, ['developer', 'super-admin', 'admin']);
        });

        // Define gate that checks if authenticated user can edit a specific user
        $gate->define('backend-edit-backend-user', function (User $authedUser, $user = null) {
            // If we do no have a user, it means
            // we're creating one instead of editing
            if (empty($user)) {
                return true;
            }

            // It's not possible to edit the manager user.
            if ($user->email == config('nodes.backend.manager.email')) {
                return false;
            }

            // Developers are the ultimate gods
            // and can do whatever they feel like doing
            if ($authedUser->user_role == 'developer') {
                return true;
            }

            // Super admins can edit all users - except developers
            if ($authedUser->user_role == 'super-admin' && $user->user_role != 'developer') {
                return true;
            }

            // Admins can edit everyone - except super admins and developers
            if ($authedUser->user_role == 'admin' && $user->user_role == 'user') {
                return true;
            }

            // Users can always edit themselves
            if ($authedUser->id == $user->id) {
                return true;
            }

            return false;
        });
    }
}
