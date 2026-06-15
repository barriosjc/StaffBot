<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class SolicitudDestinatario extends Model
{
    use HasFactory;

    protected $fillable = [
        'solicitud_id',
        'empleado_id',
        'orden',
        'estado',
        'notificado_at',
        'respondido_at',
    ];

    protected $casts = [
        'notificado_at' => 'datetime:d/m/Y H:i',
        'respondido_at' => 'datetime:d/m/Y H:i',
    ];

    public function solicitud()
    {
        return $this->belongsTo(SolicitudCobertura::class, 'solicitud_id');
    }

    public function empleado()
    {
        return $this->belongsTo(User::class, 'empleado_id');
    }
}
