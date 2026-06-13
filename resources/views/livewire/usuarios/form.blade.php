<div>
    <div class="container-fluid px-4">

        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0">
                    <i class="fas fa-user{{ $modoEdicion ? '-edit' : '-plus' }} me-2 text-primary"></i>
                    {{ $modoEdicion ? 'Editar usuario' : 'Nuevo usuario' }}
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('usuarios.index') }}">Usuarios</a></li>
                        <li class="breadcrumb-item active">{{ $modoEdicion ? 'Editar' : 'Nuevo' }}</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>

        <form wire:submit="guardar">
            <div class="row g-3">

                {{-- ────────────────────────────────────────
                     Datos personales
                ──────────────────────────────────────── --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-header py-2 bg-light">
                            <strong class="small"><i class="fas fa-id-card me-1"></i>Datos personales</strong>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label">name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                                        wire:model="name" placeholder="name" autocomplete="off">
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Teléfono / WhatsApp <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                        wire:model="telefono" placeholder="+54911..." autocomplete="off">
                                    @error('telefono')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                        wire:model="email" placeholder="correo@ejemplo.com" autocomplete="off">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">
                                        Contraseña {{ $modoEdicion ? '' : '*' }}
                                        @if ($modoEdicion)
                                            <small class="text-muted">(dejar vacío para no cambiar)</small>
                                        @endif
                                    </label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                                        wire:model="password" autocomplete="new-password">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Confirmar contraseña
                                        {{ $modoEdicion ? '' : '*' }}</label>
                                    <input type="password" class="form-control" wire:model="password_confirmation"
                                        autocomplete="new-password">
                                </div>

                                <div class="col-md-3 d-flex align-items-end">
                                    <div class="form-check form-switch mb-2">
                                        <input class="form-check-input" type="checkbox" role="switch" id="activoSwitch"
                                            wire:model="activo">
                                        <label class="form-check-label" for="activoSwitch">
                                            Usuario <strong>{{ $activo ? 'activo' : 'inactivo' }}</strong>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ────────────────────────────────────────
                     Rol
                ──────────────────────────────────────── --}}
                <div class="col-12">
                    <div class="card">
                        <div class="card-header py-2 bg-light">
                            <strong class="small"><i class="fas fa-user-tag me-1"></i>Rol y asignaciones</strong>
                        </div>
                        <div class="card-body">

                            {{-- Selector de rol --}}
                            <div class="mb-4">
                                <label class="form-label">Tipo de rol <span class="text-danger">*</span></label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="rolSup" value="sup"
                                            wire:model.live="tipoRol">
                                        <label class="form-check-label" for="rolSup">
                                            <i class="fas fa-user-tie me-1 text-info"></i>
                                            <strong>Supervisor</strong>
                                            <small class="text-muted d-block">Supervisa uno o más centros</small>
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" id="rolEmp" value="emp"
                                            wire:model.live="tipoRol">
                                        <label class="form-check-label" for="rolEmp">
                                            <i class="fas fa-user me-1 text-secondary"></i>
                                            <strong>Empleado</strong>
                                            <small class="text-muted d-block">Trabaja en centros con
                                                especialidades</small>
                                        </label>
                                    </div>
                                </div>
                                @error('tipoRol')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            {{-- ── SUPERVISOR: selección de centros ── --}}
                            @if ($tipoRol === 'sup')
                                <div>
                                    <label class="form-label">
                                        Centros supervisados <span class="text-danger">*</span>
                                        <small class="text-muted">(seleccioná uno o más)</small>
                                    </label>
                                    @error('centrosSup')
                                        <div class="text-danger small mb-1">{{ $message }}</div>
                                    @enderror

                                    <div class="row g-2">
                                        @foreach ($centros as $centro)
                                            <div class="col-md-4 col-lg-3">
                                                <div
                                                    class="form-check border rounded p-2 ps-4 {{ in_array((string) $centro->id, $centrosSup) ? 'border-info bg-info bg-opacity-10' : '' }}">
                                                    <input class="form-check-input ms-2 mt-1" type="checkbox"
                                                        id="centro_sup_{{ $centro->id }}"
                                                        value="{{ $centro->id }}" wire:model.live="centrosSup">
                                                    <label class="form-check-label ms-2"
                                                        for="centro_sup_{{ $centro->id }}">
                                                        <strong class="d-block">{{ $centro->nombre }}</strong>
                                                        @if ($centro->razon_social)
                                                            <small class="text-muted">{{ $centro->razon_social }}</small>
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- ── EMPLEADO: combinaciones centro + especialidad ── --}}
                            @if ($tipoRol === 'emp')
                                <div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label class="form-label mb-0">
                                            Centro + Especialidad <span class="text-danger">*</span>
                                        </label>
                                        <button type="button" class="btn btn-sm btn-outline-success"
                                            wire:click="agregarCombinacion">
                                            <i class="fas fa-plus me-1"></i>Agregar fila
                                        </button>
                                    </div>

                                    @error('combinaciones')
                                        <div class="text-danger small mb-2">{{ $message }}</div>
                                    @enderror

                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Centro</th>
                                                    <th>Especialidad</th>
                                                    <th class="text-center" style="width:90px">Activo</th>
                                                    <th class="text-center" style="width:60px"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($combinaciones as $i => $combo)
                                                    <tr>
                                                        <td>
                                                            <select
                                                                class="form-select form-select-sm @error("combinaciones.{$i}.centro_id") is-invalid @enderror"
                                                                wire:model="combinaciones.{{ $i }}.centro_id">
                                                                <option value="">— Seleccioná un centro —
                                                                </option>
                                                                @foreach ($centros as $centro)
                                                                    <option value="{{ $centro->id }}">
                                                                        {{ $centro->nombre }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error("combinaciones.{$i}.centro_id")
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td>
                                                            <select
                                                                class="form-select form-select-sm @error("combinaciones.{$i}.especialidad_id") is-invalid @enderror"
                                                                wire:model="combinaciones.{{ $i }}.especialidad_id">
                                                                <option value="">— Seleccioná especialidad —
                                                                </option>
                                                                @foreach ($especialidades as $esp)
                                                                    <option value="{{ $esp->id }}">
                                                                        {{ $esp->nombre }}</option>
                                                                @endforeach
                                                            </select>
                                                            @error("combinaciones.{$i}.especialidad_id")
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </td>
                                                        <td class="text-center">
                                                            <div
                                                                class="form-check form-switch d-flex justify-content-center mb-0">
                                                                <input class="form-check-input" type="checkbox"
                                                                    role="switch"
                                                                    wire:model="combinaciones.{{ $i }}.activo">
                                                            </div>
                                                        </td>
                                                        <td class="text-center">
                                                            @if (count($combinaciones) > 1)
                                                                <button type="button"
                                                                    class="btn btn-sm btn-outline-danger"
                                                                    wire:click="quitarCombinacion({{ $i }})"
                                                                    title="Quitar fila">
                                                                    <i class="fas fa-times"></i>
                                                                </button>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                        </div>
                    </div>
                </div>

                {{-- Botones --}}
                <div class="col-12 d-flex justify-content-end gap-2 pb-4">
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading wire:target="guardar">
                            <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                            Guardando...
                        </span>
                        <span wire:loading.remove wire:target="guardar">
                            <i class="fas fa-save me-1"></i>
                            {{ $modoEdicion ? 'Actualizar usuario' : 'Crear usuario' }}
                        </span>
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
