<?php

namespace App\Livewire\SolicitudCoberturas;

use App\Models\SolicitudCobertura;
use App\Models\SolicitudDestinatario;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public string $busqueda = '';
    public array $filtroEstado = [];
    public string $ordenarPor = 'fecha_inicio';
    public string $ordenDir = 'desc';
    public ?int $expandedSolicitud = null;

    protected $listeners = [
        'solicitudGuardada' => '$refresh',
    ];

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroEstado(): void
    {
        $this->resetPage();
    }

    public function ordenar(string $campo): void
    {
        if ($this->ordenarPor === $campo) {
            $this->ordenDir = $this->ordenDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->ordenarPor = $campo;
            $this->ordenDir = 'asc';
        }
    }

    public function toggleActivo(int $id): void
    {
        // No aplica para solicitudes de cobertura
    }

    public function reenviar(int $id): void
    {
        $this->redirect(route('solicitud-coberturas.create', ['solicitudId' => $id, 'modo' => 'reenviar']));
    }

    public function enviarOtro(int $id): void
    {
        $this->redirect(route('solicitud-coberturas.create', ['solicitudId' => $id, 'modo' => 'enviar_otro']));
    }

    public function cancelar(int $id): void
    {
        $solicitud = SolicitudCobertura::findOrFail($id);
        $maxOrden = $solicitud->destinatarios()->max('orden') ?? 0;
        SolicitudDestinatario::create([
            'solicitud_id'  => $solicitud->id,
            'empleado_id'   => Auth::id(),
            'orden'         => $maxOrden + 1,
            'estado'        => 'cancelado',
            'respondido_at' => now(),
        ]);
        $solicitud->update(['estado' => 'cancelado']);
        $this->dispatch('notify', tipo: 'success', mensaje: 'Solicitud cancelada correctamente.');
    }

    public function solicitarCancelar(int $id): void
    {
        $this->dispatch('pedir-confirmacion-cancelar', id: $id);
    }

    public function toggleDestinatarios(int $id): void
    {
        $this->expandedSolicitud = $this->expandedSolicitud === $id ? null : $id;
    }

    public function mount(): void
    {
        $this->filtroEstado = ['pendiente', 'aceptada', 'rechazada', 'cancelado'];
        $this->ordenarPor = 'created_at';
        $this->ordenDir = 'asc';
    }

    public function render()
    {
        $query = SolicitudCobertura::query()
            ->with(['centro', 'especialidad', 'horario', 'supervisor', 'destinatarios.empleado'])
            ->when($this->busqueda, function ($q) {
                $q->where(function ($sub) {
                    $sub->whereHas('centro', fn($q) => $q->where('nombre', 'like', "%{$this->busqueda}%"))
                        ->orWhereHas('especialidad', fn($q) => $q->where('nombre', 'like', "%{$this->busqueda}%"))
                        ->orWhereHas('supervisor', fn($q) => $q->where('name', 'like', "%{$this->busqueda}%"));
                });
            })
            ->when(count($this->filtroEstado) < 4, fn ($q) => $q->whereIn('estado', $this->filtroEstado));

        $solicitudes = $query->orderBy($this->campoOrden(), $this->ordenDir)->paginate(10);

        return view('livewire.solicitud-coberturas.index', compact('solicitudes'))
            ->layout('layouts.app');
    }

    private function campoOrden(): string
    {
        return match ($this->ordenarPor) {
            'centro' => 'centro_id',
            'especialidad' => 'especialidad_id',
            'supervisor' => 'supervisor_id',
            'estado' => 'estado',
            'fecha_inicio' => 'fecha_inicio',
            'created_at' => 'created_at',
            default => 'created_at',
        };
    }
}
