<div>
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="fas fa-clock me-2 text-primary"></i>Horarios</h4>
            <a href="{{ route('horarios.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Nuevo horario
            </a>
        </div>

        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="row g-2 align-items-end">
                    <div class="col-md-6">
                        <label class="form-label small mb-1">Buscar</label>
                        <input type="text" class="form-control form-control-sm" placeholder="Nombre..." wire:model.debounce.400ms="busqueda">
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Centro</th>
                                <th>Especialidad</th>
                                <th>Día</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th class="text-center">Activo</th>
                                <th class="text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($horarios as $h)
                                <tr>
                                    <td class="small text-muted">{{ $h->id }}</td>
                                    <td class="small"><strong>{{ $h->centro?->nombre ?? '—' }}</strong></td>
                                    <td class="small">{{ $h->especialidad?->nombre ?? '—' }}</td>
                                    <td class="small">{{ $h->dia_semana_label ?? ucfirst($h->dia_semana) }}</td>
                                    <td class="small">{{ \Illuminate\Support\Str::substr($h->hora_inicio,0,5) }}</td>
                                    <td class="small">{{ \Illuminate\Support\Str::substr($h->hora_fin,0,5) }}</td>
                                    <td class="text-center">
                                        <input type="checkbox" class="form-check-input" {{ $h->activo ? 'checked' : '' }} wire:click="toggleActivo({{ $h->id }})">
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('horarios.edit', $h->id) }}" class="btn btn-sm btn-outline-primary me-1"><i class="fas fa-edit"></i></a>
                                        <button class="btn btn-sm btn-outline-danger" wire:click="solicitarEliminar({{ $h->id }})"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No se encontraron horarios.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($horarios->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center py-2">
                    <small class="text-muted">Mostrando {{ $horarios->firstItem() }}–{{ $horarios->lastItem() }} de {{ $horarios->total() }}</small>
                    {{ $horarios->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('pedir-confirmacion-eliminar', ({ id }) => {
                Swal.fire({
                    title: '¿Eliminar horario?',
                    text: 'Esta acción no se puede deshacer.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Sí, eliminar',
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
