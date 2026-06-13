<?php

namespace App\Models;

use App\Models\EmpleadoCentroEspecialidad;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Especialidad extends Model
{
    protected $fillable = [
        'nombre',
        'descripcion',
        'activo',
    ];

    protected $table = 'especialidades';
    
    protected $casts = [
        'activo' => 'boolean',
    ];

    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function empleadoCentroEspecialidades(): HasMany
    {
        return $this->hasMany(EmpleadoCentroEspecialidad::class, 'especialidad_id');
    }
}
