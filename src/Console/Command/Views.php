<?php
namespace Nodes\Backend\Console\Command;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class Views
 *
 * @package Nodes\Backend\Console\Command
 */
class Views extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nodes:backend:views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy backend views to project folder';

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
        if (!$this->option('force') && !$this->confirm('Are you sure you want to copy all backend views to project folder? Note: It will overwrite any existing files!', true)) {
            return false;
        }

        $this->comment('Copying backend views to project folder ...');
        $this->call('vendor:publish', ['--tag' => ['nodes-backend-views'], '--force' => true]);
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
