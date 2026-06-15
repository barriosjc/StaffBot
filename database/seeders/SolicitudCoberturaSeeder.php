<?php

namespace Database\Seeders;

use App\Models\SolicitudCobertura;
use App\Models\SolicitudDestinatario;
use App\Models\User;
use App\Models\Centro;
use App\Models\Especialidad;
use App\Models\Horario;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SolicitudCoberturaSeeder extends Seeder
{
    public function run(): void
    {
        $centro = Centro::first();
        $especialidad = Especialidad::first();
        $horario = Horario::first();
        $supervisor = User::where('tipo_rol', 'sup')->first();
        $empleado = User::where('tipo_rol', 'emp')->first();

        if (!$centro || !$especialidad || !$horario || !$supervisor || !$empleado) {
            return;
        }

        $now = Carbon::now();

        $solicitudAceptada = SolicitudCobertura::create([
            'centro_id'       => $centro->id,
            'especialidad_id' => $especialidad->id,
            'horario_id'      => $horario->id,
            'supervisor_id'   => $supervisor->id,
            'fecha_inicio'    => $now->toDateString(),
            'hora_inicio'     => $horario->hora_inicio,
            'fecha_fin'       => $now->toDateString(),
            'hora_fin'        => $horario->hora_fin,
            'modo_envio'      => 'manual_uno',
            'estado'          => 'aceptada',
        ]);

        SolicitudDestinatario::create([
            'solicitud_id'  => $solicitudAceptada->id,
            'empleado_id'   => $empleado->id,
            'orden'         => 1,
            'estado'        => 'aceptada',
            'notificado_at' => $now,
            'respondido_at' => (clone $now)->addHour(),
        ]);

        $solicitudRechazada = SolicitudCobertura::create([
            'centro_id'       => $centro->id,
            'especialidad_id' => $especialidad->id,
            'horario_id'      => $horario->id,
            'supervisor_id'   => $supervisor->id,
            'fecha_inicio'    => (clone $now)->addDay()->toDateString(),
            'hora_inicio'     => $horario->hora_inicio,
            'fecha_fin'       => (clone $now)->addDay()->toDateString(),
            'hora_fin'        => $horario->hora_fin,
            'modo_envio'      => 'manual_uno',
            'estado'          => 'rechazada',
        ]);

        SolicitudDestinatario::create([
            'solicitud_id'  => $solicitudRechazada->id,
            'empleado_id'   => $empleado->id,
            'orden'         => 1,
            'estado'        => 'rechazada',
            'notificado_at' => $now,
            'respondido_at' => (clone $now)->addHour(),
        ]);
    }
}
