<?php

namespace App\Http\Controllers;

use App\Models\Centro;
use App\Models\Especialidad;
use App\Models\Horario;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function create()
    {
        $centros = Centro::activos()->orderBy('nombre')->get();
        $especialidades = Especialidad::activas()->orderBy('nombre')->get();
        $dias = ['Lunes','Martes','Miércoles','Jueves','Viernes','Sábado','Domingo'];

        return view('horarios.create', compact('centros', 'especialidades', 'dias'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'centro_id' => 'required|exists:centros,id',
            'especialidad_id' => 'required|exists:especialidades,id',
                'dia_semana' => 'required|in:lunes,martes,miercoles,jueves,viernes,sabado,domingo',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'activo' => 'sometimes|boolean',
        ]);

        $data['activo'] = $request->has('activo') ? (bool) $request->input('activo') : true;

        Horario::create($data);

        return redirect()->route('horarios.index')->with('notify_tipo', 'success')->with('notify_mensaje', 'Horario creado.');
    }
}
