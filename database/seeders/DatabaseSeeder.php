<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            PassportSeeder::class,
            UserSeeder::class,
            IncidenceSeeder::class,
            CommentSeeder::class,
            // TagSeeder::class,
        ]);
    }
}
