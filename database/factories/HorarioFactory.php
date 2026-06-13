<?php

namespace Database\Factories;

use App\Models\Horario;
use Illuminate\Database\Eloquent\Factories\Factory;

class HorarioFactory extends Factory
{
    protected $model = Horario::class;

    public function definition()
    {
        $days = ['lunes','martes','miercoles','jueves','viernes','sabado','domingo'];
        $inicio = $this->faker->time('H:i:s', '08:00:00');
        $fin = date('H:i:s', strtotime($inicio) + 3600 * rand(1, 4));

        return [
            'nombre' => $this->faker->words(2, true),
            'centro_id' => \App\Models\Centro::inRandomOrder()->value('id'),
            'especialidad_id' => \App\Models\Especialidad::inRandomOrder()->value('id'),
            'dia_semana' => $this->faker->randomElement($days),
            'hora_inicio' => $inicio,
            'hora_fin' => $fin,
            'activo' => $this->faker->boolean(85),
        ];
    }
}
