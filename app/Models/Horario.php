<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Centro;
use App\Models\Especialidad;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'centro_id',
        'especialidad_id',
        'dia_semana',
        'hora_inicio',
        'hora_fin',
        'activo',
    ];

    public function getDiaSemanaLabelAttribute(): string
    {
        $map = [
            'lunes' => 'Lunes',
            'martes' => 'Martes',
            'miercoles' => 'Miércoles',
            'jueves' => 'Jueves',
            'viernes' => 'Viernes',
            'sabado' => 'Sábado',
            'domingo' => 'Domingo',
        ];

        return $map[$this->dia_semana] ?? ucfirst($this->dia_semana ?? '');
    }

    public function centro()
    {
        return $this->belongsTo(Centro::class, 'centro_id');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'especialidad_id');
    }
}
