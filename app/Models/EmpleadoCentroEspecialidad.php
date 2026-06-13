<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmpleadoCentroEspecialidad extends Model
{
    protected $table = 'empleado_centro_especialidad';

    protected $fillable = [
        'user_id',
        'centro_id',
        'especialidad_id',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function centro(): BelongsTo
    {
        return $this->belongsTo(Centro::class);
    }

    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class);
    }
}
