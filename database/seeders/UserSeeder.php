<?php

namespace Database\Seeders;

use app\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Salem',
            'email' => 'salem@telsur.cl',
            'password' => bcrypt('password'),
        ]);
    }
}
