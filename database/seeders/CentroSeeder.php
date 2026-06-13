<?php

namespace Database\Seeders;

use App\Models\Centro;
use Illuminate\Database\Seeder;

class CentroSeeder extends Seeder
{
    public function run(): void
    {
        $centros = [
            ['nombre' => 'Bazterrica', 'codigo' => 'BZTR', 'activo' => true],
            ['nombre' => 'Santa Isabel', 'codigo' => 'STIB', 'activo' => true],
            ['nombre' => 'San Jorge', 'codigo' => 'SJRG', 'activo' => true],
        ];

        foreach ($centros as $data) {
            Centro::updateOrCreate(
                ['nombre' => $data['nombre']],
                $data
            );
        }
    }
}
