<?php

namespace App\Livewire\Horarios;

use App\Models\Horario;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $busqueda = '';
    public string $filtroActivo = '';
    public string $ordenarPor = 'id';
    public string $ordenDir = 'asc';

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroActivo(): void
    {
        $this->resetPage();
    }

    public function ordenar(string $campo): void
    {
        if ($this->ordenarPor === $campo) {
            $this->ordenDir = $this->ordenDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->ordenarPor = $campo;
            $this->ordenDir   = 'asc';
        }
    }
    public function toggleActivo(int $id): void
    {
        $h = Horario::findOrFail($id);
        $h->update(['activo' => ! $h->activo]);
    }

    public function eliminar(int $id): void
    {
        Horario::findOrFail($id)->delete();
        $this->dispatch('notify', tipo: 'success', mensaje: 'Horario eliminado.');
    }

    public function solicitarEliminar(int $id): void
    {
        $this->dispatch('pedir-confirmacion-eliminar', id: $id);
    }

    public function render()
    {
        $dir = $this->ordenDir === 'desc' ? 'desc' : 'asc';
        $horarios = Horario::with(['centro', 'especialidad'])
            ->join('centros', 'centros.id', '=', 'horarios.centro_id')
            ->join('especialidades', 'especialidades.id', '=', 'horarios.especialidad_id')
            ->select('horarios.*')
            ->when($this->busqueda, function ($query) {
                $query->where(function ($q) {
                    $q->where('horarios.dia_semana', 'like', '%' . $this->busqueda . '%')
                        ->orWhere('centros.nombre', 'like', '%' . $this->busqueda . '%')
                        ->orWhere('especialidades.nombre', 'like', '%' . $this->busqueda . '%');
                });
            })
            ->when($this->filtroActivo !== '', fn($q) => $q->where('horarios.activo', (bool) $this->filtroActivo))
            ->orderBy($this->campoOrden(), $this->ordenDir)
            ->paginate(10);

        return view('livewire.horarios.index', compact('horarios'))
            ->layout('layouts.app');
    }

    private function campoOrden(): string
    {
        return match ($this->ordenarPor) {
            'centro'       => 'centros.nombre',
            'especialidad' => 'especialidades.nombre',
            'dia'          => 'horarios.dia_semana',
            default        => 'horarios.id',
        };
    }
}
