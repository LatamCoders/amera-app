<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::insert(
            [
                [
                    'role' => 'Super Admin'
                ],
                [
                    'role' => 'Admin'
                ],
                [
                    'role' => 'Corporate Account'
                ]
            ]
        );
    }
}
