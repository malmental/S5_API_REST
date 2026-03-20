<?php

namespace Database\Seeders;

use App\Models\User;
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

        User::create([
            'name' => 'Malo Mentalo',
            'email' => 'malmental@telsur.cl',
            'password' => bcrypt('password'),
        ]);
    }
}
