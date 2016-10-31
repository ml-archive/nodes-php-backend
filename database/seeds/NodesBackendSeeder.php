<?php

use Illuminate\Database\Seeder;

/**
 * Class NodesBackendSeeder.
 */
class NodesBackendSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * @return void
     */
    public function run()
    {
        // User related seeders
        $this->call('BackendRolesSeeder');
        $this->call('BackendUsersSeeder');
    }
}
