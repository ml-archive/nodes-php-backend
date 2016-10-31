<?php

use Illuminate\Database\Seeder;
use Nodes\Backend\Models\Role\RoleRepository;

/**
 * Class BackendRolesSeeder.
 */
class BackendRolesSeeder extends Seeder
{
    /**
     * Roles.
     * @var array
     */
    protected $roles = [
        'developer' => 'Developer',
        'super-admin' => 'Super admin',
        'admin' => 'Admin',
        'user' => 'User',
    ];

    /**
     * @author Casper Rasmussen <cr@nodes.dk>
     */
    public function run()
    {
        // Inform user
        $this->command->info('Seeding backend roles ...');

        // Init user repository
        $roleRepository = app(RoleRepository::class);

        foreach ($this->roles as $slug => $title) {
            try {
                $roleRepository->create([
                    'title' => $title,
                    'slug' => $slug,
                    'default' => ($slug == 'user') ? 1 : 0,
                ]);
            } catch (\Exception $e) {
                $this->command->error($e->getMessage());
            }
        }

        $this->command->info('Backend roles seeded!');
    }
}
