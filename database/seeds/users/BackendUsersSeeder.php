<?php
use Illuminate\Database\Seeder;
use Nodes\Backend\Models\User\UserRepository;

/**
 * Class BackendUsersSeeder
 */
class BackendUsersSeeder extends Seeder
{
    /**
     * Execute seeding
     *
     * @author Morten Rugaard <moru@nodes.dk>
     *
     * @access public
     * @return void
     */
    public function run()
    {
        // Inform user
        $this->command->info('Creating Nodes user ...');

        // Seed Nodes "super user"
        try {
            app(UserRepository::class)->createUser([
                'name' => 'Nodes ApS',
                'email' => 'tech@nodes.dk',
                'password' => str_random(),
                'user_role' => 'developer',
            ]);
        } catch (\Exception $e) {
            $this->command->error($e->getMessage());
        }

        // Inform user
        $this->command->info('Nodes user successfully created!');
    }

}
