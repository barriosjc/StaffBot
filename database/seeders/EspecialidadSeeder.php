<?php

namespace Database\Seeders;

use App\Models\Especialidad;
use Illuminate\Database\Seeder;

class EspecialidadSeeder extends Seeder
{
    public function run(): void
    {
        $especialidades = [
            ['nombre' => 'Tec. Laboratorio', 'descripcion' => 'Técnico de laboratorio clínico', 'activo' => true],
            ['nombre' => 'Extraccionista', 'descripcion' => 'Extracción de muestras', 'activo' => true],
            ['nombre' => 'Enfermería', 'descripcion' => 'Atención y cuidados de enfermería', 'activo' => true],
        ];

        foreach ($especialidades as $data) {
            Especialidad::updateOrCreate(
                ['nombre' => $data['nombre']],
                $data
            );
        }
    }
}
