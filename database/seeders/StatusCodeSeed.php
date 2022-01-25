<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\StatusCode;

class StatusCodeSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        StatusCode::insert(
            [
                [
                    'code' => 0,
                    'status' => 'Pending',
                ],
                [
                    'code' => 1,
                    'status' => 'In progress',
                ],
                [
                    'code' => 2,
                    'status' => 'Completed',
                ],
                [
                    'code' => 3,
                    'status' => 'Cancelled',
                ]
            ]
        );
    }
}
