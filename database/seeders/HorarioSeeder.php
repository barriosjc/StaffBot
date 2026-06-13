<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HorarioSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        $centros = [1, 2]; // Bazterrica, Santa Isabel
        $especialidades = [1, 2]; // Tec. Laboratorio, Extraccionista

        $dias = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes'];

        $turnos = [
            ['hora_inicio' => '07:00:00', 'hora_fin' => '13:00:00'],
            ['hora_inicio' => '13:00:00', 'hora_fin' => '19:00:00'],
            ['hora_inicio' => '19:00:00', 'hora_fin' => '07:00:00'], // nocturno
        ];

        $registros = [];

        foreach ($centros as $centro_id) {
            foreach ($especialidades as $especialidad_id) {
                foreach ($dias as $dia) {
                    foreach ($turnos as $turno) {
                        $registros[] = [
                            'centro_id'       => $centro_id,
                            'especialidad_id' => $especialidad_id,
                            'dia_semana'      => $dia,
                            'hora_inicio'     => $turno['hora_inicio'],
                            'hora_fin'        => $turno['hora_fin'],
                            'activo'          => 1,
                            'created_at'      => $now,
                            'updated_at'      => $now,
                        ];
                    }
                }
            }
        }

        DB::table('horarios')->insert($registros);
    }
}