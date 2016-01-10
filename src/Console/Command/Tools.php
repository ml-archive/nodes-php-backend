<?php
namespace Nodes\Backend\Console\Command;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class Tools
 *
 * @package Nodes\Backend\Console\Command
 */
class Tools extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nodes:backend:tools';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install backend tools to compile CSS and Javascript.';

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
        // Make sure user really want to install the nodes.backend
        if (!$this->option('force') && !$this->confirm('Are you sure you want to install backend tools? It will overwrite any existing files!', true)) {
            return false;
        }

        $this->comment('Installing backend tools ...');
        $this->call('vendor:publish', ['--tag' => ['nodes-backend-tools'], '--force' => true]);

        // Update project names
        $this->updateProjectName();

        // Ask to install node moduels
        if ($this->option('force') || $this->confirm('Do you wish to install the required node modules?', true)) {
            // Execute command to determine nodejs version
            exec('node -v', $nodeVersionResponse);
            if ((empty($nodeVersionResponse) || !is_array($nodeVersionResponse)) || !preg_match('/v(\d+)\.(\d+)\.(\d+)/i', $nodeVersionResponse[0], $nodeVersionMatch)) {
                $this->error(sprint('Could not determine nodejs version. Response: %s', $nodeVersionResponse));
                return false;
            }

            // Handle version matches
            $nodeVersionMatch = array_slice($nodeVersionMatch, 1);

            // Validate node version
            $nodeVersion = implode('', $nodeVersionMatch);
            if ($nodeVersion < 0122) {
                $this->error(sprintf('Your current node version [%s] does not meet the minimum required version [0.12.2]', implode('.', $nodeVersionMatch)));
                return false;
            }

            $this->info('Installing node modules ...');
            $this->comment('Note: This could take a while. So hang tight.');
            exec('sudo npm install');
        }

        // Ask to install bower modules
        if ($this->option('force') || $this->confirm('Do you wish to install the required bower components?', true)) {
            // Execute command to determine nodejs version
            exec('bower -v', $bowerVersionResponse);
            if ((empty($bowerVersionResponse) || !is_array($bowerVersionResponse)) || !preg_match('/(\d+)\.(\d+)\.(\d+)/i', $bowerVersionResponse[0], $bowerVersionMatch)) {
                $this->error(sprintf('Could not determine bower version. Response: %s', $bowerVersionResponse));
                return false;
            }

            // Handle version matches
            $bowerVersionMatch = array_slice($bowerVersionMatch, 1);

            // Validate node version
            $bowerVersion = implode('', $bowerVersionMatch);
            if ($bowerVersion < 153) {
                $this->error(sprint('Your current bower version [%s] does not meet the minimum required version [1.5.3]', implode('.', $bowerVersionMatch)));
                return false;
            }

            $this->info('Installing bower components ...');
            $this->comment('Note: This could take a while. So hang tight.');
            exec('bower install');

	    if ($this->option('force') || $this->confirm('Do you wish to create resources/assets/scss/project.scss?', true)) {
		mkdir('resources/assets/scss');
	    	file_put_contents('resources/assets/scss/project.scss', '');
	    }
	    if ($this->option('force') || $this->confirm('Do you wish to run gulp?', true)) {
	    	exec('gulp');
	    }
        }

        $this->comment('Backend tools was successfully installed.');
        return true;
    }

    /**
     * Update project name in package.json & bower.json
     *
     * @author Morten Rugaard <moru@nodes.dk>
     * @date   15-10-2015
     *
     * @access private
     * @return boolean
     */
    private function updateProjectName()
    {
        // Retrieve project name from base path
        preg_match('|.*www\/(.*)/|is', base_path(), $projectName);

        // Update name in package.json
        $package = json_decode(file_get_contents(base_path('package.json')));
        $package->name = $projectName[1];
        file_put_contents(base_path('package.json'), json_encode($package, JSON_PRETTY_PRINT));

        // Update name in bower.json
        $bower = json_decode(file_get_contents(base_path('bower.json')));
        $bower->name = $projectName[1];
        file_put_contents(base_path('bower.json'), json_encode($bower, JSON_PRETTY_PRINT));

        return true;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['force', null, InputOption::VALUE_NONE, 'Bypass confirmation question', null]
        ];
    }
}
