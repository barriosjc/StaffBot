<?php

namespace App\Livewire\Usuarios;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $busqueda       = '';
    public string $filtroRol      = '';   // 'sup' | 'emp' | ''
    public string $filtroActivo   = '';   // '1' | '0' | ''
    public string $ordenarPor     = 'name';
    public string $ordenDir       = 'asc';

    protected $listeners = [
        'usuarioGuardado' => '$refresh',
    ];

    public function updatingBusqueda(): void
    {
        $this->resetPage();
    }

    public function updatingFiltroRol(): void
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
        $usuario = User::findOrFail($id);
        $usuario->update(['activo' => ! $usuario->activo]);
    }

    /**
     * No elimina directamente: emite evento JS para que SweetAlert confirme,
     * y el JS llama de vuelta a confirmarEliminar() si el usuario acepta.
     */
    public function solicitarEliminar(int $id): void
    {
        $this->dispatch('pedir-confirmacion-eliminar', id: $id);
    }

    public function eliminar(int $id): void
    {
        $usuario = User::findOrFail($id);

        // Eliminar relaciones pivot antes de borrar
        $usuario->centrosSupervisor()->detach();
        $usuario->empleadoCentroEspecialidades()->delete();
        $usuario->delete();

        $this->dispatch('notify', tipo: 'success', mensaje: 'Usuario eliminado correctamente.');
    }

    public function mount(): void
    {
        $this->filtroRol      = '';
        $this->filtroActivo   = '';
        $this->ordenarPor     = 'name';
        $this->ordenDir       = 'asc';
    }

    public function render()
    {
        $query = User::query()
            ->with(['centrosSupervisor', 'empleadoCentroEspecialidades.centro', 'empleadoCentroEspecialidades.especialidad'])
            ->when($this->busqueda, function ($q) {
                $q->where(function ($sub) {
                    $sub->Where('name', 'like', "%{$this->busqueda}%")
                        ->orWhere('email',    'like', "%{$this->busqueda}%")
                        ->orWhere('telefono', 'like', "%{$this->busqueda}%");
                });
            })
            ->when($this->filtroActivo !== '', fn ($q) => $q->where('activo', (bool) $this->filtroActivo));

        // Filtro por rol (sup/emp) a nivel PHP luego de la query
        // porque el rol se determina por las pivot, no por columna directa
        $todos = $query->orderBy($this->ordenarPor, $this->ordenDir)->get();

        if ($this->filtroRol === 'sup') {
            $todos = $todos->filter(fn ($u) => $u->centrosSupervisor->isNotEmpty());
        } elseif ($this->filtroRol === 'emp') {
            $todos = $todos->filter(fn ($u) => $u->empleadoCentroEspecialidades->isNotEmpty());
        }

        // Paginación manual sobre colección filtrada
        $pagina   = $this->getPage();
        $perPage  = 10;
        $total    = $todos->count();
        $usuarios = new \Illuminate\Pagination\LengthAwarePaginator(
            $todos->slice(($pagina - 1) * $perPage, $perPage)->values(),
            $total,
            $perPage,
            $pagina,
            ['path' => request()->url()]
        );

        return view('livewire.usuarios.index', compact('usuarios'))
            ->layout('layouts.app');
    }
}
