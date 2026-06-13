<?php

namespace App\Models;

use App\Models\EmpleadoCentroEspecialidad;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Centro extends Model
{
    protected $fillable = [
        'nombre',
        'codigo',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    public function supervisores(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'supervisor_centro', 'centro_id', 'user_id')
            ->withTimestamps();
    }

    public function empleadoCentroEspecialidades(): HasMany
    {
        return $this->hasMany(EmpleadoCentroEspecialidad::class, 'centro_id');
    }
}
