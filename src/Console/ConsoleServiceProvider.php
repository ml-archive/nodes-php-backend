<?php
namespace Nodes\Backend\Console;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Providers\ArtisanServiceProvider;
use Nodes\Backend\Console\Command\Install;
use Nodes\Backend\Console\Command\Assets;
use Nodes\Backend\Console\Command\Routes;
use Nodes\Backend\Console\Command\Tools;
use Nodes\Backend\Console\Command\Views;

/**
 * Class ConsoleServiceProvider
 *
 * @package Nodes\Backend\Console
 */
class ConsoleServiceProvider extends ArtisanServiceProvider
{
    // @todo
    protected $defer = false;

    /**
     * The commands to be registered
     *
     * @var array
     */
    protected $nodesCommands = [
        'NodesBackendInstall' => 'command.nodes.backend.install',
        'NodesBackendAssets' => 'command.nodes.backend.assets',
        'NodesBackendRoutes' => 'command.nodes.backend.routes',
        'NodesBackendTools' => 'command.nodes.backend.tools',
        'NodesBackendViews' => 'command.nodes.backend.views',
    ];

    /**
     * Constructor
     *
     * @access public
     * @param \Illuminate\Contracts\Foundation\Application $app
     */
    public function __construct(Application $app)
    {
        $this->addNodesCommands();
        parent::__construct($app);
    }


    /**
     * Bootstrap any application services
     *
     * @access public
     * @return void
     */
    public function boot()
    {
        // Configs
        $this->publishes([
            __DIR__ . '/../config/auth.php' => config_path('nodes/backend/auth.php'),
            __DIR__ . '/../config/dashboard.php' => config_path('nodes/backend/dashboard.php'),
            __DIR__ . '/../config/general.php' => config_path('nodes/backend/general.php'),
            __DIR__ . '/../config/nstack.php' => config_path('nodes/backend/nstack.php'),
            __DIR__ . '/../config/reset-password.php' => config_path('nodes/backend/reset-password.php'),
            __DIR__ . '/../config/welcome.php' => config_path('nodes/backend/welcome.php')
        ], 'nodes-backend-config');

        // User system
        $this->publishes([
            __DIR__ . '/../database/migrations/users' => database_path('/migrations'),
            __DIR__ . '/../database/seeds/users' => database_path('/seeds')
        ], 'nodes-backend-users');

        // Reset password
        $this->publishes([
            __DIR__ . '/../database/migrations/reset-password' => database_path('/migrations')
        ], 'nodes-backend-reset-password');

        // Seeder
        $this->publishes([
            __DIR__ . '/../database/seeds/NodesBackendSeeder.php' => database_path('/seeds/NodesBackendSeeder.php')
        ], 'nodes-backend-seeders');

        // Backend routes
        $this->publishes([
            __DIR__ . '/../routes/BackendUser/auth.php' => base_path() . '/project/Routes/Backend/BackendUser/auth.php',
            __DIR__ . '/../routes/BackendUser/backend-users.php' => base_path() . '/project/Routes/Backend/BackendUser/backend-users.php',
            __DIR__ . '/../routes/BackendUser/roles.php' => base_path() . '/project/Routes/Backend/BackendUser/roles.php',
            __DIR__ . '/../routes/BackendUser/reset-password.php' => base_path() . '/project/Routes/Backend/BackendUser/reset-password.php',
            __DIR__ . '/../routes/dashboard.php' => base_path() . '/project/Routes/Backend/dashboard.php',
            __DIR__ . '/../routes/nstack.php' => base_path() . '/project/Routes/Backend/nstack.php',
            __DIR__ . '/../routes/welcome.php' => base_path() . '/project/Routes/Backend/welcome.php',
        ], 'nodes-backend-routes');

        // Backend views
        $this->publishes([
            __DIR__ . '/../resources/views' => base_path() . '/resources/views/vendor/nodes.backend'
        ], 'nodes-backend-views');

        // Assets
        $this->publishes([__DIR__ . '/../public' => public_path('vendor/nodes/backend')], 'nodes-backend-assets');

        // Tools
        $this->publishes([
            __DIR__ . '/../gulp/gulpfile.js' => base_path('gulpfile.js'),
            __DIR__ . '/../gulp/package.json' => base_path('package.json'),
            __DIR__ . '/../bower/.bowerrc' => base_path('.bowerrc'),
            __DIR__ . '/../bower/bower.json' => base_path('bower.json')
        ], 'nodes-backend-tools');
    }

    /**
     * Add Nodes command
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return boolean
     */
    protected function addNodesCommands()
    {
        foreach ($this->nodesCommands as $key => $value) {
            $this->commands[$key] = $value;
        }
    }

    /**
     * Register Install command
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function registerNodesBackendInstallCommand()
    {
        $this->app->singleton('command.nodes.backend.install', function($app)
        {
            return new Install;
        });
    }

    /**
     * Register Routes command
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function registerNodesBackendRoutesCommand()
    {
        $this->app->singleton('command.nodes.backend.routes', function($app)
        {
            return new Routes;
        });
    }

    /**
     * Register Assets command
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function registerNodesBackendAssetsCommand()
    {
        $this->app->singleton('command.nodes.backend.assets', function($app)
        {
            return new Assets;
        });
    }

    /**
     * Register Views command
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function registerNodesBackendViewsCommand()
    {
        $this->app->singleton('command.nodes.backend.views', function($app)
        {
            return new Views;
        });
    }

    /**
     * Register Tools command
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function registerNodesBackendToolsCommand()
    {
        $this->app->singleton('command.nodes.backend.tools', function($app)
        {
            return new Tools;
        });
    }
}
