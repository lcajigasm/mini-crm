<?php

namespace Database\Seeders;

use App\Models\Pipeline;
use App\Models\Stage;
use Illuminate\Database\Seeder;

class PipelinesSeeder extends Seeder
{
    public function run(): void
    {
        $pipelines = [
            [
                'name' => 'Preventa',
                'slug' => 'preventa',
                'stages' => [
                    ['name' => 'Nuevo', 'slug' => 'nuevo'],
                    ['name' => 'Contactado', 'slug' => 'contactado'],
                    ['name' => 'Calificado', 'slug' => 'calificado'],
                    ['name' => 'Cita agendada', 'slug' => 'cita-agendada'],
                ],
            ],
            [
                'name' => 'Venta',
                'slug' => 'venta',
                'stages' => [
                    ['name' => 'En tratamiento', 'slug' => 'en-tratamiento'],
                    ['name' => 'Seguimiento', 'slug' => 'seguimiento'],
                    ['name' => 'Cerrado ganado', 'slug' => 'ganado'],
                    ['name' => 'Cerrado perdido', 'slug' => 'perdido'],
                ],
            ],
        ];

        foreach ($pipelines as $pipelineData) {
            $pipeline = Pipeline::firstOrCreate(
                ['slug' => $pipelineData['slug']],
                ['name' => $pipelineData['name']]
            );

            foreach ($pipelineData['stages'] as $index => $stageData) {
                Stage::firstOrCreate(
                    ['pipeline_id' => $pipeline->id, 'slug' => $stageData['slug']],
                    ['name' => $stageData['name'], 'display_order' => $index]
                );
            }
        }
    }
}


