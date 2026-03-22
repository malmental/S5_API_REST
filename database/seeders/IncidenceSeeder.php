<?php

namespace Database\Seeders;

use App\Models\Incidence;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class IncidenceSeeder extends Seeder
{
    public function run(): void
    {
        $tags = [
            ['name' => 'bug', 'user_id' => 1],
            ['name' => 'feature', 'user_id' => 1],
            ['name' => 'urgent', 'user_id' => 2],
            ['name' => 'database', 'user_id' => 2],
            ['name' => 'frontend', 'user_id' => 1],
            ['name' => 'backend', 'user_id' => 2],
            ['name' => 'security', 'user_id' => 1],
            ['name' => 'performance', 'user_id' => 2],
            ['name' => 'ui', 'user_id' => 1],
            ['name' => 'ux', 'user_id' => 2],
        ];

        foreach ($tags as $tagData) {
            Tag::firstOrCreate(['name' => $tagData['name']], $tagData);
        }

        $incidences = [
            [
                'title' => 'Error en login de usuarios',
                'description' => 'Los usuarios no pueden iniciar sesión con Google OAuth',
                'status' => 'open',
                'priority' => 'critical',
                'user_id' => 2,
            ],
            [
                'title' => 'Añadir modo oscuro',
                'description' => 'Implementar tema oscuro en toda la aplicación',
                'status' => 'in_progress',
                'priority' => 'medium',
                'user_id' => 1,
            ],
            [
                'title' => 'Optimizar consultas lentas',
                'description' => 'El dashboard tarda 5 segundos en cargar',
                'status' => 'open',
                'priority' => 'high',
                'user_id' => 2,
            ],
            [
                'title' => 'Traducir interfaz al español',
                'description' => 'Añadir soporte para idioma español',
                'status' => 'resolved',
                'priority' => 'low',
                'user_id' => 2,
            ],
            [
                'title' => 'Fallo en móviles',
                'description' => 'El menú no se cierra en dispositivos iOS',
                'status' => 'closed',
                'priority' => 'medium',
                'user_id' => 1,
            ],
        ];

        foreach ($incidences as $incidenceData) {
            Incidence::create($incidenceData);
        }
    }
}
