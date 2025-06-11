<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        User::truncate();

        User::create(
            [
                'username' => 'Super Admin',
                'email' => 'super.admin@gmail.com',
                'password' => bcrypt('12345678'),
                'phone' => '015232269',
                'type' => 'admin',
                'role' => 'super_admin',
                'status' => 1,
                'remember_token' => Str::random(10),
            ]
        );

        User::create(
            [
                'username' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('12345678'),
                'phone' => '0123456789',
                'type' => 'admin',
                'role' => 'admin',
                'status' => 1,
                'remember_token' => Str::random(10),
            ]
        );
    }
}
