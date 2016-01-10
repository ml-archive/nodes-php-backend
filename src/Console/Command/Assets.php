<?php
namespace Nodes\Backend\Console\Command;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class Assets
 *
 * @package Nodes\Backend\Console\Command
 */
class Assets extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'nodes:backend:assets';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy backend assets to public folder';

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
        if (!$this->option('force') && !$this->confirm('Are you sure you want to copy all backend assets to the public folder? Note: It will overwrite any existing files!', true)) {
            return false;
        }

        $this->comment('Copying backend assets to public folder ...');
        $this->call('vendor:publish', ['--tag' => ['nodes-backend-assets'], '--force' => true]);
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
