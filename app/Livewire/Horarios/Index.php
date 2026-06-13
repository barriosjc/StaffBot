<?php

namespace App\Livewire\Horarios;

use App\Models\Horario;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $busqueda = '';

    public function updatingBusqueda(): void
    {
        $this->resetPage();
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
        $query = Horario::query()
            ->with(['centro', 'especialidad'])
            ->when($this->busqueda, fn($q) => $q->whereHas('centro', fn($qc) => $qc->where('nombre', 'like', "%{$this->busqueda}%"))
                ->orWhere('dia_semana', 'like', "%{$this->busqueda}%")
            )
            ->orderBy('dia_semana')
            ->orderBy('hora_inicio');

        $horarios = $query->paginate(10);

        return view('livewire.horarios.index', compact('horarios'))->layout('layouts.app');
    }
}
