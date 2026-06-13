<?php

namespace App\Livewire\Horarios;

use Livewire\Component;
use App\Models\Centro;
use App\Models\Especialidad;
use App\Models\Horario;

class Create extends Component
{
    public $centros = [];
    public $especialidades = [];
    public $dias = [];

    public $centro_id;
    public $especialidad_id;
    public $dia_semana;
    public $hora_inicio = '08:00';
    public $hora_fin = '09:00';
    public $activo = true;

    public function mount()
    {
        $this->centros = Centro::orderBy('nombre')->get();
        $this->especialidades = Especialidad::orderBy('nombre')->get();
        $this->dias = [
            'lunes' => 'Lunes',
            'martes' => 'Martes',
            'miercoles' => 'Miércoles',
            'jueves' => 'Jueves',
            'viernes' => 'Viernes',
            'sabado' => 'Sábado',
            'domingo' => 'Domingo',
        ];
    }

    protected function rules()
    {
        $diasList = implode(',', array_keys($this->dias));

        return [
            'centro_id' => 'required|exists:centros,id',
            'especialidad_id' => 'required|exists:especialidades,id',
            'dia_semana' => 'required|in:'.$diasList,
            'hora_inicio' => 'required',
            'hora_fin' => 'required',
            'activo' => 'boolean',
        ];
    }

    public function save()
    {
        $data = $this->validate();
        $data['activo'] = $this->activo ? 1 : 0;

        Horario::create([
            'centro_id' => $data['centro_id'],
            'especialidad_id' => $data['especialidad_id'],
            'dia_semana' => $data['dia_semana'],
            'hora_inicio' => $data['hora_inicio'],
            'hora_fin' => $data['hora_fin'],
            'activo' => $data['activo'],
        ]);

        session()->flash('success', 'Horario creado correctamente');

        return redirect()->route('horarios.index');
    }

    public function render()
    {
        return view('livewire.horarios.create');
    }
}
