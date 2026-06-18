<?php
namespace App\Livewire\Horarios;

use App\Models\Centro;
use App\Models\Especialidad;
use App\Models\Horario;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;

class Form extends Component
{
    public ?int $horarioId = null;
    public string $centro_id = '';
    public string $especialidad_id = '';
    public string $dia_semana = '';
    public string $hora_inicio = '08:00';
    public string $hora_fin = '09:00';
    public bool $activo = true;
    
    public Collection $centros;
    public Collection $especialidades;
    
    protected array $dias = [
        'lunes' => 'Lunes',
        'martes' => 'Martes',
        'miercoles' => 'Miércoles',
        'jueves' => 'Jueves',
        'viernes' => 'Viernes',
        'sabado' => 'Sábado',
        'domingo' => 'Domingo',
    ];

    public function mount(mixed $horario = null): void
    {
        $this->centros = Centro::activos()->orderBy('nombre')->get();
        $this->especialidades = Especialidad::activas()->orderBy('nombre')->get();
        
        if ($horario instanceof Horario) {
            $horario = $horario->id;
        }

        $horarioId = $horario ? (int) $horario : null;

        if ($horarioId) {
            $this->horarioId = $horarioId;
            $h = Horario::findOrFail($horarioId);
            $this->centro_id = (string) $h->centro_id;
            $this->especialidad_id = (string) $h->especialidad_id;
            $this->dia_semana = strtolower($h->dia_semana ?? '');
            $this->hora_inicio = $h->hora_inicio;
            $this->hora_fin = $h->hora_fin;
            $this->activo = (bool) $h->activo;
        }
    }

    protected function rules(): array
    {
        return [
            'centro_id' => 'required|exists:centros,id',
            'especialidad_id' => 'required|exists:especialidades,id',
            'dia_semana' => 'required|in:'.implode(',', $this->dias),
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
            'activo' => 'boolean',
        ];
    }

    public function getDiasProperty(): array
    {
        return $this->dias;
    }

    public function guardar(): void
    {
        $this->validate();

        $data = [
            'centro_id' => $this->centro_id,
            'especialidad_id' => $this->especialidad_id,
            'dia_semana' => $this->dia_semana,
            'hora_inicio' => $this->hora_inicio,
            'hora_fin' => $this->hora_fin,
            'activo' => $this->activo,
        ];

        if ($this->horarioId) {
            Horario::findOrFail($this->horarioId)->update($data);
            session()->flash('notify_tipo', 'success');
            session()->flash('notify_mensaje', 'Horario actualizado.');
        } else {
            Horario::create($data);
            session()->flash('notify_tipo', 'success');
            session()->flash('notify_mensaje', 'Horario creado.');
        }

        $this->redirect(route('horarios.index'));
    }

    public function render(): View
    {
        return view('livewire.horarios.form', [
            'dias' => $this->dias,
        ])->layout('layouts.app');
    }
}
