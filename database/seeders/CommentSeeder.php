<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Incidence;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $incidence1 = Incidence::first();
        $incidence2 = Incidence::skip(1)->first();

        if (! $incidence1 || ! $incidence2) {
            return;
        }

        $comment1 = Comment::create([
            'body' => 'Este es el primer comentario sobre el error de login',
            'user_id' => 2,
            'incidence_id' => $incidence1->id,
            'parent_id' => null,
        ]);
        $comment1_1 = Comment::create([
            'body' => 'Respuesta al primer comentario',
            'user_id' => 1,
            'incidence_id' => $incidence1->id,
            'parent_id' => $comment1->id,
        ]);
        Comment::create([
            'body' => 'Respuesta anidada al primer comentario',
            'user_id' => 2,
            'incidence_id' => $incidence1->id,
            'parent_id' => $comment1_1->id,
        ]);
        Comment::create([
            'body' => 'Segundo comentario sobre el error de login',
            'user_id' => 2,
            'incidence_id' => $incidence1->id,
            'parent_id' => null,
        ]);
        Comment::create([
            'body' => 'Comentario sobre el modo oscuro',
            'user_id' => 1,
            'incidence_id' => $incidence2->id,
            'parent_id' => null,
        ]);
    }
}
