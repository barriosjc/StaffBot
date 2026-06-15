<div>
    <div class="container-fluid px-4">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="mb-0">
                    <i class="fas fa-calendar-plus me-2 text-primary"></i>Nueva solicitud de cobertura
                </h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="#">Inicio</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('solicitud-coberturas.index') }}">Solicitudes</a></li>
                        <li class="breadcrumb-item active">Nueva</li>
                    </ol>
                </nav>
            </div>
            <a href="{{ route('solicitud-coberturas.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
        </div>

        <form wire:submit="guardar">
            <div class="row g-3">

                <div class="col-12">
                    <div class="card">
                        <div class="card-header py-2 bg-light">
                            <strong class="small"><i class="fas fa-building me-1"></i>Centro y especialidad</strong>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Centro <span class="text-danger">*</span></label>
                                    <select class="form-select @error('centroId') is-invalid @enderror"
                                        wire:model.live="centroId">
                                        <option value="">— Seleccioná un centro —</option>
                                        @foreach ($centros as $centro)
                                            <option value="{{ $centro->id }}">{{ $centro->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('centroId')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Especialidad <span class="text-danger">*</span></label>
                                    <select class="form-select @error('especialidadId') is-invalid @enderror"
                                        wire:model.live="especialidadId">
                                        <option value="">— Seleccioná una especialidad —</option>
                                        @foreach ($especialidades as $esp)
                                            <option value="{{ $esp->id }}">{{ $esp->nombre }}</option>
                                        @endforeach
                                    </select>
                                    @error('especialidadId')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header py-2 bg-light">
                            <strong class="small"><i class="fas fa-calendar-day me-1"></i>Fecha y horario</strong>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Fecha <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('fechaInicio') is-invalid @enderror"
                                        wire:model.live="fechaInicio">
                                    @error('fechaInicio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Horario <span class="text-danger">*</span></label>
                                    <select class="form-select @error('horarioId') is-invalid @enderror"
                                        wire:model.live="horarioId"
                                        @if (empty($horariosDisponibles)) disabled @endif>
                                        <option value="">— Seleccioná un horario —</option>
                                        @foreach ($horariosDisponibles as $h)
                                            <option value="{{ $h['id'] }}">
                                                {{ \Carbon\Carbon::parse($h['hora_inicio'])->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($h['hora_fin'])->format('H:i') }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('horarioId')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if ($msgHorarios)
                                        <div class="small mt-1 {{ str_starts_with($msgHorarios, 'Falta') ? 'text-warning' : 'text-muted' }}">
                                            <i class="fas fa-info-circle me-1"></i>{{ $msgHorarios }}
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Rango horario</label>
                                    <div class="form-control-plaintext fw-bold pt-2">
                                        @if ($horaInicio && $horaFin)
                                            {{ \Carbon\Carbon::parse($horaInicio)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($horaFin)->format('H:i') }}
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12">
                    <div class="card">
                        <div class="card-header py-2 bg-light">
                            <strong class="small"><i class="fas fa-user me-1"></i>Destinatario</strong>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Empleado <span class="text-danger">*</span></label>
                                    <select class="form-select @error('empleadoId') is-invalid @enderror"
                                        wire:model="empleadoId"
                                        @if (empty($empleadosDisponibles)) disabled @endif>
                                        <option value="">— Seleccioná un empleado —</option>
                                        @foreach ($empleadosDisponibles as $emp)
                                            <option value="{{ $emp['id'] }}">{{ $emp['name'] }}</option>
                                        @endforeach
                                    </select>
                                    @error('empleadoId')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if ($msgEmpleados)
                                        <div class="small mt-1 {{ str_starts_with($msgEmpleados, 'Falta') ? 'text-warning' : 'text-muted' }}">
                                            <i class="fas fa-info-circle me-1"></i>{{ $msgEmpleados }}
                                        </div>
                                    @endif
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Modo de envío <span class="text-danger">*</span></label>
                                    <select class="form-select @error('modoEnvio') is-invalid @enderror"
                                        wire:model="modoEnvio">
                                        <option value="manual_uno">Manual (1 empleado)</option>
                                        <option value="secuencial">Secuencial</option>
                                        <option value="broadcast">Broadcast</option>
                                    </select>
                                    @error('modoEnvio')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 d-flex justify-content-end gap-2 pb-4">
                    <a href="{{ route('solicitud-coberturas.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times me-1"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
                        <span wire:loading wire:target="guardar">
                            <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                            Guardando...
                        </span>
                        <span wire:loading.remove wire:target="guardar">
                            <i class="fas fa-paper-plane me-1"></i>Enviar solicitud
                        </span>
                    </button>
                </div>

            </div>
        </form>
    </div>
</div>
