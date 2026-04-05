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

        $statuses = ['open', 'in_progress', 'resolved', 'closed'];

        $priorities = ['low', 'medium', 'high'];

        $titles = [
            'Error en el sistema de login',
            'Fallo en la carga de imágenes',
            'Optimizar tiempo de respuesta',
            'Añadir nuevo campo al formulario',
            'Corregir error de JavaScript',
            'Mejora en la interfaz de usuario',
            'Actualizar dependencias del proyecto',
            'Añadir filtros de búsqueda',
            'Problema con las notificaciones',
            'Revisar seguridad de la API',
            'Implementar caché',
            'Corregir bug en móviles',
            'Añadir soporte multi-idioma',
            'Mejora de rendimiento',
            'Documentar nuevos endpoints',
            'El intranet necesita una actualización',
            'Error al exportar datos',
            'Problema con la autenticación de dos factores',
            'Fallo en la integración con terceros',
        ];

        for ($i = 1; $i <= 20; $i++) {
            $title = $titles[array_rand($titles)].' #'.$i;
            $description = 'Descripción de prueba para la incidencia #'.$i.'. Este es un texto aleatorio para la base de datos.';

            Incidence::create([
                'title' => $title,
                'description' => $description,
                'status' => $statuses[array_rand($statuses)],
                'priority' => $priorities[array_rand($priorities)],
                'user_id' => rand(1, 2),
                'assigned_to' => rand(1, 2),
            ]);
        }
    }
}
