<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Usuario Admin',
            'email' => 'admin@telsur.cl',
            'password' => bcrypt('password'),
            'is_admin' => true,
        ]);

        User::create([
            'name' => 'Usuario no admin',
            'email' => 'noadmin@telsur.cl',
            'password' => bcrypt('password'),
            'is_admin' => false,
        ]);
    }
}
