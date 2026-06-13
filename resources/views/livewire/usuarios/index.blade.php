<div>
    {{-- ────────────────────────────────────────
         Notificación flash (viene desde Form)
    ──────────────────────────────────────── --}}
    @if (session('notify_mensaje'))
        <div
            x-data="{ show: true }"
            x-init="
                Swal.fire({
                    icon: '{{ session('notify_tipo', 'success') }}',
                    title: '{{ session('notify_mensaje') }}',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3500,
                    timerProgressBar: true,
                });
            "
        ></div>
    @endif

    {{-- Notificación desde eventos Livewire --}}
    <div
        x-data="{}"
        x-on:notify.window="
            Swal.fire({
                icon: $event.detail.tipo,
                title: $event.detail.mensaje,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3500,
                timerProgressBar: true,
            })
        "
    ></div>

    {{-- ────────────────────────────────────────
         Header de página
    ──────────────────────────────────────── --}}
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0"><i class="fas fa-users me-2 text-primary"></i>Usuarios</h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                        <li class="breadcrumb-item active">Usuarios</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('usuarios.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Nuevo usuario
            </a>
        </div>

        {{-- ────────────────────────────────────────
             Filtros
        ──────────────────────────────────────── --}}
        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small mb-1">Buscar</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Nombre, email, teléfono..."
                                wire:model.live.debounce.400ms="busqueda"
                            >
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Rol</label>
                        <select class="form-select form-select-sm" wire:model.live="filtroRol">
                            <option value="">Todos</option>
                            <option value="sup">Supervisores</option>
                            <option value="emp">Empleados</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small mb-1">Estado</label>
                        <select class="form-select form-select-sm" wire:model.live="filtroActivo">
                            <option value="">Todos</option>
                            <option value="1">Activos</option>
                            <option value="0">Inactivos</option>
                        </select>
                    </div>
                    <div class="col-md-3 text-end">
                        <button class="btn btn-sm btn-outline-secondary" wire:click="$set('busqueda', ''); $set('filtroRol', ''); $set('filtroActivo', '')">
                            <i class="fas fa-times me-1"></i>Limpiar filtros
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ────────────────────────────────────────
             Tabla
        ──────────────────────────────────────── --}}
        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width:40px">#</th>
                                <th class="cursor-pointer" wire:click="ordenar('name')">
                                    Nombre
                                    @if($ordenarPor === 'name')
                                        <i class="fas fa-sort-{{ $ordenDir === 'asc' ? 'up' : 'down' }} text-primary ms-1"></i>
                                    @else
                                        <i class="fas fa-sort text-muted ms-1"></i>
                                    @endif
                                </th>
                                <th class="cursor-pointer" wire:click="ordenar('email')">
                                    Email
                                    @if($ordenarPor === 'email')
                                        <i class="fas fa-sort-{{ $ordenDir === 'asc' ? 'up' : 'down' }} text-primary ms-1"></i>
                                    @else
                                        <i class="fas fa-sort text-muted ms-1"></i>
                                    @endif
                                </th>
                                <th>Teléfono</th>
                                <th>Rol</th>
                                <th>Centros / Especialidades</th>
                                <th class="text-center" style="width:80px">Estado</th>
                                <th class="text-center" style="width:110px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($usuarios as $usuario)
                                <tr>
                                    <td class="text-muted small">{{ $usuario->id }}</td>
                                    <td>
                                        <strong>{{ $usuario->name }}</strong>
                                    </td>
                                    <td class="small">{{ $usuario->email }}</td>
                                    <td class="small">{{ $usuario->telefono }}</td>
                                    <td>
                                        @if($usuario->centrosSupervisor->isNotEmpty())
                                            <span class="badge bg-info text-dark">Supervisor</span>
                                        @endif
                                        @if($usuario->empleadoCentroEspecialidades->isNotEmpty())
                                            <span class="badge bg-secondary">Empleado</span>
                                        @endif
                                        @if($usuario->centrosSupervisor->isEmpty() && $usuario->empleadoCentroEspecialidades->isEmpty())
                                            <span class="badge bg-light text-muted">Sin rol</span>
                                        @endif
                                    </td>
                                    <td class="small">
                                        @if($usuario->centrosSupervisor->isNotEmpty())
                                            <div class="text-muted mb-1">
                                                <i class="fas fa-building me-1"></i>
                                                {{ $usuario->centrosSupervisor->pluck('nombre')->join(', ') }}
                                            </div>
                                        @endif
                                        @if($usuario->empleadoCentroEspecialidades->isNotEmpty())
                                            @foreach($usuario->empleadoCentroEspecialidades as $ece)
                                                <div class="{{ !$ece->activo ? 'text-muted text-decoration-line-through' : '' }}">
                                                    <i class="fas fa-map-marker-alt me-1 text-secondary"></i>
                                                    {{ $ece->centro->nombre ?? '—' }}
                                                    <i class="fas fa-stethoscope ms-2 me-1 text-secondary"></i>
                                                    {{ $ece->especialidad->nombre ?? '—' }}
                                                </div>
                                            @endforeach
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div
                                            class="form-check form-switch d-flex justify-content-center mb-0"
                                            title="{{ $usuario->activo ? 'Activo — click para desactivar' : 'Inactivo — click para activar' }}"
                                        >
                                            <input
                                                class="form-check-input"
                                                type="checkbox"
                                                role="switch"
                                                {{ $usuario->activo ? 'checked' : '' }}
                                                wire:click="toggleActivo({{ $usuario->id }})"
                                            >
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <a
                                            href="{{ route('usuarios.edit', $usuario->id) }}"
                                            class="btn btn-sm btn-outline-primary me-1"
                                            title="Editar"
                                        >
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button
                                            class="btn btn-sm btn-outline-danger"
                                            title="Eliminar"
                                            wire:click="solicitarEliminar({{ $usuario->id }})"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">
                                        <i class="fas fa-users-slash fa-2x mb-2 d-block"></i>
                                        No se encontraron usuarios.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($usuarios->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center py-2">
                    <small class="text-muted">
                        Mostrando {{ $usuarios->firstItem() }}–{{ $usuarios->lastItem() }} de {{ $usuarios->total() }} usuarios
                    </small>
                    {{ $usuarios->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    {{-- ────────────────────────────────────────
         SweetAlert: confirmación de borrado
    ──────────────────────────────────────── --}}
    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('pedir-confirmacion-eliminar', ({ id }) => {
                Swal.fire({
                    title: '¿Eliminar usuario?',
                    text: 'Esta acción no se puede deshacer. Se eliminarán también todas sus asignaciones de centros y especialidades.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-trash me-1"></i>Sí, eliminar',
                    cancelButtonText: 'Cancelar',
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('eliminar', id);
                    }
                });
            });
        });
    </script>
    @endpush
</div>
