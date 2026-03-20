<?php

namespace Database\Seeders;

use App\Models\Comment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $comment1 = Comment::create([
            'body' => 'Este es el primer comentario sobre el error de login',
            'user_id' => 2,
            'incidence_id' => 1,
            'parent_id' => null,
        ]);
        $comment1_1 = Comment::create([
            'body' => 'Respuesta de Salem al primer comentario',
            'user_id' => 1,
            'incidence_id' => 1,
            'parent_id' => $comment1->id,
        ]);
        Comment::create([
            'body' => 'Respuesta anidada de Malo Mentalo',
            'user_id' => 2,
            'incidence_id' => 1,
            'parent_id' => $comment1_1->id,
        ]);
        Comment::create([
            'body' => 'Segundo comentario sobre el error de login',
            'user_id' => 2,
            'incidence_id' => 1,
            'parent_id' => null,
        ]);
        Comment::create([
            'body' => 'Comentario sobre el modo oscuro',
            'user_id' => 1,
            'incidence_id' => 2,
            'parent_id' => null,
        ]);
    }
}
