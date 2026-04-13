<?php

namespace Database\Seeders;

use App\Models\Tag;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tags = [
            ['name' => 'bug', 'user_id' => 2],
            ['name' => 'feature', 'user_id' => 2],
            ['name' => 'urgent', 'user_id' => 1],
            ['name' => 'database', 'user_id' => 1],
            ['name' => 'frontend', 'user_id' => 2],
            ['name' => 'backend', 'user_id' => 2],
            ['name' => 'security', 'user_id' => 1],
            ['name' => 'performance', 'user_id' => 1],
            ['name' => 'ui', 'user_id' => 2],
            ['name' => 'ux', 'user_id' => 2],
        ];

        foreach ($tags as $tag) {
            Tag::firstOrCreate(['name' => $tag['name']], $tag);
        }
    }
}
