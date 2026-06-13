<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0"><i class="fas fa-clock me-2 text-primary"></i>Nuevo horario</h4>
        <a href="{{ route('horarios.index') }}" class="btn btn-secondary btn-sm">Volver</a>
    </div>

    <div class="card">
        <div class="card-body">
            <form wire:submit.prevent="save">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small">Centro</label>
                        <select wire:model="centro_id" class="form-select form-select-sm">
                            <option value="">-- Seleccionar --</option>
                            @foreach($centros as $centro)
                                <option value="{{ $centro->id }}">{{ $centro->nombre }}</option>
                            @endforeach
                        </select>
                        @error('centro_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small">Especialidad</label>
                        <select wire:model="especialidad_id" class="form-select form-select-sm">
                            <option value="">-- Seleccionar --</option>
                            @foreach($especialidades as $esp)
                                <option value="{{ $esp->id }}">{{ $esp->nombre }}</option>
                            @endforeach
                        </select>
                        @error('especialidad_id') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label small">Día</label>
                        <select wire:model="dia_semana" class="form-select form-select-sm">
                            <option value="">-- Seleccionar --</option>
                            @foreach($dias as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('dia_semana') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small">Hora inicio</label>
                        <input type="time" wire:model="hora_inicio" class="form-control form-control-sm">
                        @error('hora_inicio') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small">Hora fin</label>
                        <input type="time" wire:model="hora_fin" class="form-control form-control-sm">
                        @error('hora_fin') <div class="text-danger small">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small">Activo</label>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" wire:model="activo">
                        </div>
                    </div>

                    <div class="col-12 text-end">
                        <button class="btn btn-primary btn-sm" type="submit">Guardar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
