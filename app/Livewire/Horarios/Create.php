<?php

namespace App\Livewire\Horarios;

use App\Models\Centro;
use App\Models\Especialidad;
use App\Models\Horario;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class Create extends Component
{
    public Collection $centros;
    public Collection $especialidades;
    public array $dias = [];

    public string $centro_id = '';
    public string $especialidad_id = '';
    public string $dia_semana = '';
    public string $hora_inicio = '08:00';
    public string $hora_fin = '09:00';
    public bool $activo = true;

    public function mount(): void
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

    protected function rules(): array
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

    public function render(): View
    {
        return view('livewire.horarios.create');
    }
}
