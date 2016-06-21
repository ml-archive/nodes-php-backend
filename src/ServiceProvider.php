<?php
namespace Nodes\Backend;

use Nodes\AbstractServiceProvider;
use Nodes\Backend\Http\Middleware\Auth as NodesBackendHttpMiddlewareAuth;
use Nodes\Backend\Http\Middleware\SSL as NodesBackendHttpMiddlewareSSL;
use Nodes\Backend\Routing\Router as NodesBackendRouter;

/**
 * Class ServiceProvider
 *
 * @package Nodes
 */
class ServiceProvider extends AbstractServiceProvider
{
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

        // Publish groups
        $this->publishGroups();
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
        $this->app->register(\Collective\Html\HtmlServiceProvider::class);
    }

    /**
     * Register publish groups
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access protected
     * @return void
     */
    protected function publishGroups()
    {
        // Config files
        $this->publishes([
            __DIR__ . '/../config' => config_path('nodes/backend'),
        ], 'config');

        // View files
        $this->publishes([
            __DIR__ . '/../resources/views/base.blade.php' => resource_path('views/vendor/nodes.backend/base.blade.php'),
            __DIR__ . '/../resources/views/layouts/base.blade.php' => resource_path('views/vendor/nodes.backend/layouts/base.blade.php'),
            __DIR__ . '/../resources/views/partials/sidebar/navigation.php' => resource_path('views/vendor/nodes.backend/partials/sidebar/navigation.php'),
            __DIR__ . '/../resources/views/errors' => resource_path('views/errors'),
        ], 'views');

        // Assets files
        $this->publishes([
            __DIR__ . '/../resources/assets/js' => resource_path('assets/js'),
            __DIR__ . '/../resources/assets/scss' => resource_path('assets/scss'),
            __DIR__ . '/../public/images' => public_path('vendor/nodes/backend/images'),
        ], 'assets');

        // Database files
        $this->publishes([
            __DIR__ . '/../database/migrations/reset-password' => database_path('migrations'),
            __DIR__ . '/../database/migrations/users' => database_path('migrations'),
            __DIR__ . '/../database/seeds/users' => database_path('seeds'),
            __DIR__ . '/../database/seeds/NodesBackendSeeder.php' => database_path('seeds/NodesBackendSeeder.php'),
        ], 'database');

        // Frontend files
        $this->publishes([
            __DIR__ . '/../bower/.bowerrc' => base_path('.bowerrc'),
            __DIR__ . '/../bower/bower.json' => base_path('bower.json'),
            __DIR__ . '/../gulp/tasks' => base_path('gulp/tasks'),
            __DIR__ . '/../gulp/config.json' => base_path('gulp/config.json'),
            __DIR__ . '/../gulp/gulpfile.js' => base_path('gulpfile.js'),
            __DIR__ . '/../gulp/package.json' => base_path('package.json')
        ], 'frontend');
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

        // Add route folders to Nodes autoload config
        add_to_autoload_config('project/Routes/Backend/');

        // Add to Composer's autoload
        add_to_composer_autoload('classmap', 'project');
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
        // Make user confirm before running time-consuming task
        if (!$this->getCommand()->confirm(sprintf('Do you wish to install required <comment>[%s]</comment> components for generating CSS/JS?', $this->getInstaller()->getVendorPackageName()), true)) {
            return;
        }

        // Install node.js components
        $this->getCommand()->comment('Installing node modules (be patient, this could take while) ...');
        passthru('npm install');

        // Install bower components
        $this->getCommand()->comment('Installing bower components ...');
        passthru('bower install');

        // Build first version of CSS/JS by running gulp
        $this->getCommand()->comment('Running initial gulp build ...');
        passthru('gulp build');
    }
}
