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
                    'status' => 'Trip pending',
                ],
                [
                    'code' => 2,
                    'status' => 'Cancellation pending',
                ],
                [
                    'code' => 3,
                    'status' => 'In progress',
                ],
                [
                    'code' => 4,
                    'status' => 'Completed',
                ],
                [
                    'code' => 5,
                    'status' => 'Cancelled',
                ]
            ]
        );
    }
}
