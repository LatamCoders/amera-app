<?php

namespace Database\Seeders;

use App\Models\ContactUs;
use Illuminate\Database\Seeder;


class ContactUsSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ContactUs::insert([
            'email' => 'amera@myamera.com',
            'website' => 'https://www.myamera.com',
            'phone_number' => '8552637215'
        ]);
    }
}
