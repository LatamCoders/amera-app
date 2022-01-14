<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(StatusCodeSeed::class);
        $this->call(RoleSeed::class);

        $this->command->info('Status code seed created');
        $this->command->info('Role seed created');
    }
}
