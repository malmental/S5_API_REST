<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@telsur.cl'],
            [
                'name' => 'Usuario Admin',
                'password' => bcrypt('password'),
                'is_admin' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'noadmin@telsur.cl'],
            [
                'name' => 'Usuario no admin',
                'password' => bcrypt('password'),
                'is_admin' => false,
            ]
        );
    }
}
