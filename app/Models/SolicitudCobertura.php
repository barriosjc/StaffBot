<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Centro;
use App\Models\Especialidad;
use App\Models\Horario;

class SolicitudCobertura extends Model
{
    use HasFactory;

    protected $fillable = [
        'centro_id',
        'especialidad_id',
        'horario_id',
        'supervisor_id',
        'fecha_inicio',
        'hora_inicio',
        'fecha_fin',
        'hora_fin',
        'modo_envio',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'hora_inicio' => 'datetime:H:i',
        'hora_fin' => 'datetime:H:i',
    ];

    public function getFechaInicioAttribute($value): string
    {
        return $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '';
    }

    public function getFechaFinAttribute($value): string
    {
        return $value ? \Carbon\Carbon::parse($value)->format('d/m/Y') : '';
    }

    public function centro()
    {
        return $this->belongsTo(Centro::class, 'centro_id');
    }

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class, 'especialidad_id');
    }

    public function horario()
    {
        return $this->belongsTo(Horario::class, 'horario_id');
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function destinatarios()
    {
        return $this->hasMany(SolicitudDestinatario::class, 'solicitud_id');
    }
}
