<?php
namespace Nodes\Backend\Tests;

use Illuminate\Foundation\Console\Kernel;
use Illuminate\Foundation\Testing\TestCase as LaravelUnitTest;

abstract class TestCase extends LaravelUnitTest
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../../../../bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();

        return $app;
    }
}
