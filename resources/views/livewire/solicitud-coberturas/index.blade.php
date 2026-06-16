<div>
    <div class="container-fluid px-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0"><i class="fas fa-calendar-alt me-2 text-primary"></i>Solicitudes de Coberturas</h4>
            <a href="{{ route('solicitud-coberturas.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus me-1"></i> Nueva solicitud
            </a>
        </div>

        <div class="card mb-3">
            <div class="card-body py-2">
                <div class="d-flex align-items-end gap-3">
                    <div class="flex-grow-1">
                        <label class="form-label small mb-1">Buscar</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Centro, especialidad, supervisor..."
                                wire:model.live.debounce.400ms="busqueda"
                            >
                        </div>
                    </div>
                    <div class="d-flex align-items-end gap-1 pb-1 flex-shrink-0">
                        <button class="btn btn-sm btn-outline-secondary" wire:click="$set('busqueda', ''); $set('filtroEstado', ['pendiente', 'aceptada', 'rechazada', 'cancelado'])">
                            <i class="fas fa-times me-1"></i>Limpiar busqueda
                        </button>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                @if(count($filtroEstado) === 4)
                                    Estados
                                @elseif(count($filtroEstado) === 1)
                                    @php $map = ['pendiente'=>'Pendiente','aceptada'=>'Aceptada','rechazada'=>'Rechazada','cancelado'=>'Cancelado']; @endphp
                                    {{ $map[$filtroEstado[0]] ?? $filtroEstado[0] }}
                                @else
                                    {{ count($filtroEstado) }} selec.
                                @endif
                            </button>
                            <ul class="dropdown-menu p-2" style="min-width:200px">
                                @php $estados = ['pendiente'=>'Pendiente','aceptada'=>'Aceptada','rechazada'=>'Rechazada','cancelado'=>'Cancelado']; @endphp
                                @foreach($estados as $val => $label)
                                    <li>
                                        <div class="form-check mb-1">
                                            <input class="form-check-input" type="checkbox" id="est{{ $val }}" value="{{ $val }}" wire:model.live="filtroEstado">
                                            <label class="form-check-label small" for="est{{ $val }}">{{ $label }}</label>
                                        </div>
                                    </li>
                                @endforeach
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <button class="btn btn-sm btn-outline-secondary w-100" wire:click="$set('filtroEstado', ['pendiente', 'aceptada', 'rechazada', 'cancelado'])">
                                        <i class="fas fa-check-double me-1"></i>Seleccionar todos
                                    </button>
                                </li>
                            </ul>
                        </div>
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
                                <th style="width:40px">Nro Solicitud</th>
                                <th class="cursor-pointer" wire:click="ordenar('centro')">
                                    Centro
                                    @if($ordenarPor === 'centro')
                                        <i class="fas fa-sort-{{ $ordenDir === 'asc' ? 'up' : 'down' }} text-primary ms-1"></i>
                                    @else
                                        <i class="fas fa-sort text-muted ms-1"></i>
                                    @endif
                                </th>
                                <th class="cursor-pointer" wire:click="ordenar('especialidad')">
                                    Especialidad
                                    @if($ordenarPor === 'especialidad')
                                        <i class="fas fa-sort-{{ $ordenDir === 'asc' ? 'up' : 'down' }} text-primary ms-1"></i>
                                    @else
                                        <i class="fas fa-sort text-muted ms-1"></i>
                                    @endif
                                </th>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th class="cursor-pointer" wire:click="ordenar('supervisor')">
                                    Supervisor
                                    @if($ordenarPor === 'supervisor')
                                        <i class="fas fa-sort-{{ $ordenDir === 'asc' ? 'up' : 'down' }} text-primary ms-1"></i>
                                    @else
                                        <i class="fas fa-sort text-muted ms-1"></i>
                                    @endif
                                </th>
                                <th>Destinatario</th>
                                <th class="cursor-pointer" wire:click="ordenar('estado')">
                                    Estado
                                    @if($ordenarPor === 'estado')
                                        <i class="fas fa-sort-{{ $ordenDir === 'asc' ? 'up' : 'down' }} text-primary ms-1"></i>
                                    @else
                                        <i class="fas fa-sort text-muted ms-1"></i>
                                    @endif
                                </th>
                                <th class="text-center" style="width:100px">Tiempo</th>
                                <th class="text-center" style="width:50px">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($solicitudes as $solicitud)
                                <tr>
                                    <td class="text-muted small">{{ $solicitud->id }}</td>
                                    <td class="small"><strong>{{ $solicitud->centro?->nombre ?? '—' }}</strong></td>
                                    <td class="small">{{ $solicitud->especialidad?->nombre ?? '—' }}</td>
                                    <td class="small">
                                        {{ $solicitud->fecha_inicio }}
                                        @if($solicitud->fecha_fin != $solicitud->fecha_inicio)
                                            <br><small class="text-muted">al {{ $solicitud->fecha_fin }}</small>
                                        @endif
                                    </td>
                                    <td class="small">
                                        {{ $solicitud->hora_inicio->format('H:i') }} - 
                                        {{ $solicitud->hora_fin->format('H:i') }}
                                    </td>
                                    <td class="small">{{ $solicitud->supervisor?->name ?? '—' }}</td>
                                    <td class="small">
                                        @php
                                            $primerDestinatario = $solicitud->destinatarios
                                                ->sortByDesc(fn($d) => $d->estado === 'aceptada' ? 1 : 0)
                                                ->first();
                                        @endphp
                                        @if($primerDestinatario)
                                            <a href="#" class="text-decoration-none" wire:click.prevent="toggleDestinatarios({{ $solicitud->id }})" title="Ver destinatarios">
                                                {{ $primerDestinatario->empleado?->name ?? '—' }}
                                                @if($solicitud->destinatarios->count() > 1)
                                                    <span class="text-muted">+{{ $solicitud->destinatarios->count() - 1 }}</span>
                                                @endif
                                            </a>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($solicitud->estado === 'pendiente')
                                            <span class="badge bg-warning text-dark">Pendiente</span>
                                        @elseif($solicitud->estado === 'aceptada')
                                            <span class="badge bg-success">Aceptada</span>
                                        @elseif($solicitud->estado === 'rechazada')
                                            <span class="badge bg-danger">Rechazada</span>
                                        @elseif($solicitud->estado === 'cancelado')
                                            <span class="badge bg-secondary">Cancelado</span>
                                        @endif
                                    </td>
                                    <td class="small text-nowrap text-center">
                                        @php
                                            $notificado = $solicitud->destinatarios->sortBy('orden')->first()?->notificado_at;
                                            $mins = $notificado ? (int) round($notificado->diffInMinutes(now())) : null;
                                        @endphp
                                        @if($mins !== null)
                                            @php
                                                $ratio = min($mins / 25, 1);
                                                $hue = 120 - ($ratio * 120);
                                            @endphp
                                            <span class="badge" style="background-color:hsl({{ $hue }},75%,40%);color:#fff">
                                                {{ $mins >= 30 ? '+30 min' : $mins . ' min' }}
                                            </span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end" style="max-height:240px">
                                                <li>
                                                    <button class="dropdown-item" wire:click="reenviar({{ $solicitud->id }})">
                                                        <i class="fas fa-redo me-2"></i>Reenviar al mismo
                                                    </button>
                                                </li>
                                                <li>
                                                    <button class="dropdown-item" wire:click="enviarOtro({{ $solicitud->id }})">
                                                        <i class="fas fa-user-plus me-2"></i>Enviar a otro
                                                    </button>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <button class="dropdown-item text-danger" wire:click="solicitarCancelar({{ $solicitud->id }})">
                                                        <i class="fas fa-ban me-2"></i>Cancelar
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                
                                {{-- Fila de destinatarios (detalle) --}}
                                @if($this->expandedSolicitud === $solicitud->id)
                                    <tr class="bg-white">
                                        <td colspan="10">
                                            <div class="p-3">
                                                <h6 class="mb-2"><i class="fas fa-users me-2"></i>Destinatarios</h6>
                                                    <table class="table table-sm table-bordered mb-0">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Empleado</th>
                                                                <th>Orden</th>
                                                                <th>Estado</th>
                                                                <th style="width:80px">Motivo</th>
                                                                <th>Notificado</th>
                                                                <th>Respondido</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @forelse($solicitud->destinatarios as $destinatario)
                                                                <tr>
                                                                    <td class="small">{{ $destinatario->empleado?->name ?? '—' }}</td>
                                                                    <td class="small text-center">{{ $destinatario->orden }}</td>
                                                                    <td>
                                                                        @if($destinatario->estado === 'no_respondio')
                                                                            <span class="badge bg-warning text-dark">No respondió</span>
                                                                        @elseif($destinatario->estado === 'aceptada')
                                                                            <span class="badge bg-success">Aceptada</span>
                                                                        @elseif($destinatario->estado === 'rechazada')
                                                                            <span class="badge bg-danger">Rechazada</span>
                                                                        @elseif($destinatario->estado === 'expirada')
                                                                            <span class="badge bg-dark">Expirada</span>
                                                                        @elseif($destinatario->estado === 'cancelado')
                                                                            <span class="badge bg-secondary">Cancelado</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="small">
                                                                        @if($destinatario->motivo_rechazo)
                                                                            <span class="badge bg-danger badge-sm" title="{{ $destinatario->motivo_rechazo }}" style="cursor:help">
                                                                                <i class="fas fa-comment-dots me-1"></i>Motivo
                                                                            </span>
                                                                        @else
                                                                            <span class="text-muted">—</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="small">
                                                                        @if($destinatario->notificado_at)
                                                                            {{ $destinatario->notificado_at }}
                                                                        @else
                                                                            <span class="text-muted">—</span>
                                                                        @endif
                                                                    </td>
                                                                    <td class="small">
                                                                        @if($destinatario->respondido_at)
                                                                            {{ $destinatario->respondido_at }}
                                                                        @else
                                                                            <span class="text-muted">—</span>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="6" class="text-center text-muted">No hay destinatarios</td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">
                                        <i class="fas fa-calendar-times fa-2x mb-2 d-block"></i>
                                        No se encontraron solicitudes.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            @if($solicitudes->hasPages())
                <div class="card-footer d-flex justify-content-between align-items-center py-2">
                    <small class="text-muted">
                        Mostrando {{ $solicitudes->firstItem() }}–{{ $solicitudes->lastItem() }} de {{ $solicitudes->total() }} solicitudes
                    </small>
                    {{ $solicitudes->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            document.addEventListener('shown.bs.dropdown', function (e) {
                const menu = e.target.nextElementSibling;
                if (menu && menu.classList.contains('dropdown-menu')) {
                    menu.style.overflowY = 'auto';
                }
            });

            Livewire.on('pedir-motivo-rechazo', ({ destinatarioId }) => {
                Swal.fire({
                    title: 'Motivo de rechazo',
                    input: 'textarea',
                    inputLabel: '¿Por qué rechazás la solicitud?',
                    inputPlaceholder: 'Escribí el motivo...',
                    inputAttributes: { required: true },
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-times me-1"></i>Rechazar',
                    cancelButtonText: 'Volver',
                    confirmButtonColor: '#dc3545',
                    reverseButtons: true,
                    inputValidator: (value) => {
                        if (!value || !value.trim()) {
                            return 'Debés ingresar un motivo.';
                        }
                    },
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('rechazarConMotivo', destinatarioId, result.value);
                    }
                });
            });

            Livewire.on('pedir-confirmacion-cancelar', ({ id }) => {
                Swal.fire({
                    title: '¿Cancelar solicitud?',
                    text: 'La solicitud pasará a estado cancelado.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#6c757d',
                    cancelButtonColor: '#0d6efd',
                    confirmButtonText: '<i class="fas fa-ban me-1"></i>Sí, cancelar',
                    cancelButtonText: 'Volver',
                }).then((result) => {
                    if (result.isConfirmed) {
                        @this.call('cancelar', id);
                    }
                });
            });
        });
    </script>
    @endpush
</div>
