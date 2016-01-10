<?php
namespace Nodes\Backend\Console\Command;

use Illuminate\Console\Command;

/**
 * Class Install
 *
 * @package Nodes\Backend\Console\Command
 */
class Install extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nodes:backend:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install Nodes Backend within your project';

    /**
     * Config received during execution
     *
     * @var array
     */
    protected $config = [];

    /**
     * Execute the console command
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return boolean
     */
    public function fire()
    {
        // Make sure user really want to install the nodes.backend.backend
        if (!$this->confirm('Do you wish to install the Nodes Backend into your project?', true)) {
            return false;
        }

        // Publish nodes.backend.backend config
        $this->comment('Publishing backend config ...');
        $this->call('vendor:publish', ['--tag' => ['nodes-backend-config']]);

        // Publish assets
        $this->comment('Publishing assets ...');
        $this->call('vendor:publish', ['--tag' => ['nodes-backend-assets']]);

        // Generate frontend scaffold
        $this->comment('Generating frontend scaffolding ...');
        $this->generateFrontendScaffold();

        // Publish, migrate and seed user system
        $this->comment('Publishing user system ...');
        $this->call('vendor:publish', ['--tag' => ['nodes-backend-users']]);
        $this->call('migrate');

        // Setup reset password for nodes.backend.backend
        // Publish, migrate and setup reset password for nodes.backend.backend
        $this->comment('Configuring reset password for backend ...');
        $this->call('vendor:publish', ['--tag' => ['nodes-backend-reset-password']]);
        $this->call('migrate');
        $this->setupResetPassword();

        // Move seeders to project and seed the database
        $this->comment('Publishing seeders ...');
        $this->call('vendor:publish', ['--tag' => ['nodes-backend-seeders']]);

        // Load seeder to request
        load_directory(database_path('/seeds/'));

        // Re-build composer's autoloader
        exec('composer dump-autoload');

        // Seed database
//        $this->call('db:seed', ['--class' => 'NodesBackendSeeder']);

        // Copy nodes.backend.backend routes to project folder
        $this->comment('Copying route files to project folder ...');
        $this->call('vendor:publish', ['--tag' => ['nodes-backend-routes']]);

        // Ask to install backend tools
        $this->call('nodes:backend:tools');

        $this->comment('Nodes Backend has successfully been installed.');
    }

    /**
     * Generate frontend scaffolding
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access private
     * @return void
     */
    private function generateFrontendScaffold()
    {
        // Create empty CSS file for frontend to use
        if (!file_exists(public_path('/css'))) {
            mkdir(public_path('/css'), 0775, true);
        }
        file_put_contents(public_path('/css/project.css'), '/* Here should all project specific CSS go */');

        // Create empty JS file for frontend to use
        if (!file_exists(public_path('/js'))) {
            mkdir(public_path('/js'), 0775, true);
        }
        file_put_contents(public_path('/js/project.js'), '/* Here should all project specific JavaScript go */');

        $this->comment('Frontend scaffolding complete.');
    }

    /**
     * Setup reset passsword for nodes.backend.backend
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access private
     * @return void
     */
    private function setupResetPassword()
    {
        // Ask if user want's to change default settings for reset password e-mails
        if ($this->confirm('Do you wish to change the default backend reset password settings?', false)) {
            // Ask about details for reset password e-mails
            $senderName = $this->ask('Sender name of reset password e-mails?', 'Nodes');
            $senderEmail = $this->ask('Sender e-mail of reset password e-mails?', 'no-reply@nodes.dk');
            $subject = $this->ask('Subject of reset password e-mails?', 'Reset password request');

            // Update reset password config
            $resetPasswordConfig = file_get_contents(config_path('nodes/backend/reset-password.php'));
            $resetPasswordConfig = str_replace('\'name\' => \'Nodes\'', '\'name\' => \'' . $senderName . '\'', $resetPasswordConfig);
            $resetPasswordConfig = str_replace('\'email\' => \'no-reply@nodes.dk\'', '\'email\' => \'' . $senderEmail . '\'', $resetPasswordConfig);
            $resetPasswordConfig = str_replace('\'subject\' => \'Reset password request\'', '\'subject\' => \'' . $subject . '\'', $resetPasswordConfig);
            file_put_contents(config_path('nodes/backend/reset-password.php'), $resetPasswordConfig);
        }

        $this->comment('Configuring of reset password for backend complete.');
    }
}
