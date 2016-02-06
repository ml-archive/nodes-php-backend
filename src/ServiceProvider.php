<?php
namespace Nodes\Backend;

use Nodes\AbstractServiceProvider as NodesAbstractServiceProvider;
use Nodes\Backend\Http\Middleware\Auth as NodesBackendHttpMiddlewareAuth;
use Nodes\Backend\Http\Middleware\SSL as NodesBackendHttpMiddlewareSSL;
use Nodes\Backend\Routing\Router as NodesBackendRouter;
use Nodes\Backend\Support\Facades\Backend as NodesBackendFacadeBackend;

/**
 * Class ServiceProvider
 *
 * @package Nodes
 */
class ServiceProvider extends NodesAbstractServiceProvider
{
    /**
     * Package name
     *
     * @var string
     */
    protected $package = 'backend';

    /**
     * Facades to install
     *
     * @var array
     */
    protected $facades = [
        'NodesBackend' => NodesBackendFacadeBackend::class
    ];

    /**
     * Array of configs to copy
     *
     * @var array
     */
    protected $configs = [
        'config/' => 'config/nodes/backend/'
    ];

    /**
     * Array of views to copy
     *
     * @var array
     */
    protected $views = [
        'resources/views/base.blade.php' => 'resources/views/vendor/nodes.backend/base.blade.php',
        'resources/views/layouts/base.blade.php' => 'resources/views/vendor/nodes.backend/layouts/base.blade.php',
        'resources/views/partials/sidebar/navigation.blade.php' => 'resources/views/vendor/nodes.backend/partials/sidebar/navigation.blade.php',
        'resources/views/errors' => 'resources/views/errors'
    ];

    /**
     * Array of assets to copy
     *
     * @var array
     */
    protected $assets = [
        'public/images' => 'public/vendor/nodes/backend/images',
        'resources/assets' => 'resources/assets'
    ];

    /**
     * Array of migrations to copy
     *
     * @var array
     */
    protected $migrations = [
        'database/migrations/users' => 'database/migrations',
        'database/migrations/reset-password' => 'database/migrations',
    ];

    /**
     * Array of seeders to copy
     *
     * @var array
     */
    protected $seeders = [
        'database/seeds/NodesBackendSeeder.php' => 'database/seeds/NodesBackendSeeder.php',
        'database/seeds/users' => 'database/seeds'
    ];

    /**
     * Array of custom files to copy
     *
     * @var array
     */
    protected $customFiles = [
        'bower/.bowerrc' => '.bowerrc',
        'bower/bower.json' => 'bower.json',
        'gulp/tasks' => 'gulp/tasks',
        'gulp/config.json' => 'gulp/config.json',
        'gulp/gulpfile.js' => 'gulpfile.js',
        'gulp/package.json' => 'package.json'
    ];

    /**
     * Boot the service provider
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return void
     */
    public function boot()
    {
        // Register namespace for backend views
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'nodes.backend');

        // Register middlewares
        $this->app['router']->middleware('backend.auth', NodesBackendHttpMiddlewareAuth::class);
        $this->app['router']->middleware('backend.ssl', NodesBackendHttpMiddlewareSSL::class);
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
        // Register router
        $this->registerRouter();

        // Register auth service provider
        $this->app->register(\Nodes\Backend\Auth\ServiceProvider::class);
    }

    /**
     * Register backend router
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function registerRouter()
    {
        $this->app->singleton('nodes.backend.router', function ($app) {
            return new NodesBackendRouter($app['router']);
        });

        $this->app->bind('Nodes\Backend\Routing\Router', function ($app) {
            return $app['nodes.backend.router'];
        });
    }

    /**
     * Install scaffolding
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function installScaffolding()
    {
        // Copy backend routes to application
        $this->copyFilesAndDirectories(['routes/' => 'project/Routes/Backend']);
    }

    /**
     * Install custom files
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function installCustom()
    {
        // Copy required custom files to application
        $this->copyFilesAndDirectories($this->customFiles);

        // Add "admin/manager_auth" to except array in "VerifyCsrfToken" middlware
        // to always bypass the CSRF token validation on POST requests
        $this->bypassCsrfToken();
    }

    /**
     * Bypass CSRF validation for API routes
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access private
     * @return boolean
     */
    private function bypassCsrfToken()
    {
        $file = file(app_path('Http/Middleware/VerifyCsrfToken.php'));

        $locateExceptArray = array_keys(preg_grep('|protected \$except = \[|', $file));
        if (empty($locateExceptArray[0])) {
            return false;
        }

        // Bypass URL
        $bypassUrl = 'admin/manager_auth';

        for ($i = $locateExceptArray[0]+2; $i < count($file); $i++) {
            // Remove whitespace from line
            $value = trim($file[$i]);

            if (!empty($value)) {
                // If we're on the outcommented line (which is there out-of-the-box)
                // we'll replace this line instead of inserting it before.
                if ($value == '//') {
                    $file[$i] =  str_repeat("\t", 2) . sprintf('\'%s\',', $bypassUrl) . "\n";
                    break;
                }

                // Remove single quotes from URL for comparison
                $currentBypassUrl = substr($value, 1, strrpos($value, '\''));

                // If we're on the last line of the $except array
                // or if our bypass URL comes before current line
                // - if sorted alphabetically - we'll insert on this line
                if ($value == '];' || strnatcmp($currentBypassUrl, $bypassUrl) > 0) {
                    array_splice($file, $i, 0, [
                        str_repeat("\t", 2) . sprintf('\'%s\',', $bypassUrl) . "\n"
                    ]);
                    break;
                }
            }
        }

        // Update existing file
        file_put_contents(app_path('Http/Middleware/VerifyCsrfToken.php'), implode('', $file));

        return true;
    }

    /**
     * Finish install
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function finishInstall()
    {
        $this->getCommand()->comment('Installing node modules (be patient, this could take while) ...');
        passthru('sudo npm install');

        $this->getCommand()->comment('Installing bower components ...');
        passthru('bower install');

        $this->getCommand()->comment('Running first gulp build ...');
        passthru('gulp build');
    }
}
