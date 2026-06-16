<?php

namespace App\Livewire\SolicitudCoberturas;

use App\Models\Centro;
use App\Models\Especialidad;
use App\Models\Horario;
use App\Models\EmpleadoCentroEspecialidad;
use App\Models\SolicitudCobertura;
use App\Models\SolicitudDestinatario;
use App\Services\WhatsApp\WhatsAppService;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

#[Layout('layouts.app')]
class Form extends Component
{
    public $centroId = '';
    public $especialidadId = '';
    public $fechaInicio = '';
    public $horarioId = '';
    public $empleadoId = '';
    public $modoEnvio = 'manual_uno';

    public $horariosDisponibles = [];
    public $empleadosDisponibles = [];
    public $horaInicio = '';
    public $horaFin = '';
    public $msgHorarios = '';
    public $msgEmpleados = '';

    public $solicitudId = null;
    public $modo = 'crear';

    protected $rules = [
        'centroId'        => 'required|exists:centros,id',
        'especialidadId'  => 'required|exists:especialidades,id',
        'fechaInicio'     => 'required|date',
        'horarioId'       => 'required|exists:horarios,id',
        'empleadoId'      => 'required|exists:users,id',
        'modoEnvio'       => 'required|in:manual_uno,secuencial,broadcast',
    ];

    public function mount(): void
    {
        $this->modo = request()->query('modo', 'crear');

        if (!$this->solicitudId) {
            return;
        }

        $solicitud = SolicitudCobertura::with('destinatarios')->findOrFail($this->solicitudId);
        $this->centroId = (string) $solicitud->centro_id;
        $this->especialidadId = (string) $solicitud->especialidad_id;
        $this->fechaInicio = $solicitud->getRawOriginal('fecha_inicio');
        $this->cargarHorarios();
        $this->cargarEmpleados();
        $this->horarioId = (string) $solicitud->horario_id;
        $this->updatedHorarioId();
        $this->modoEnvio = $solicitud->modo_envio;

        $destinatario = $solicitud->destinatarios->sortBy('orden')->first();
        if ($this->modo === 'enviar_otro') {
            $this->empleadoId = '';
        } elseif ($destinatario) {
            $this->empleadoId = (string) $destinatario->empleado_id;
            $yaExiste = collect($this->empleadosDisponibles)->firstWhere('id', (int) $this->empleadoId);
            if (!$yaExiste) {
                $this->empleadosDisponibles[] = [
                    'id'   => $destinatario->empleado->id,
                    'name' => $destinatario->empleado->name,
                ];
            }
        }
    }

    public function updatedCentroId()
    {
        $this->resetHorarios();
        $this->cargarHorarios();
        $this->cargarEmpleados();
    }

    public function updatedEspecialidadId()
    {
        $this->resetHorarios();
        $this->cargarHorarios();
        $this->cargarEmpleados();
    }

    public function updatedFechaInicio()
    {
        $this->resetHorarios();
        $this->cargarHorarios();
    }

    public function updatedHorarioId()
    {
        if (!$this->horarioId) {
            $this->horaInicio = '';
            $this->horaFin = '';
            return;
        }

        $horario = Horario::find($this->horarioId);
        if ($horario) {
            $this->horaInicio = $horario->hora_inicio;
            $this->horaFin = $horario->hora_fin;
        }
    }

    private function resetHorarios()
    {
        $this->horarioId = '';
        $this->horaInicio = '';
        $this->horaFin = '';
        $this->horariosDisponibles = [];
        $this->msgHorarios = '';
    }

    public function cargarHorarios()
    {
        $this->horariosDisponibles = [];
        $this->msgHorarios = '';

        if (!$this->centroId || !$this->especialidadId) {
            return;
        }

        if (!$this->fechaInicio) {
            $this->msgHorarios = 'Seleccioná una fecha para ver los horarios disponibles.';
            return;
        }

        $diasSemana = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        $dia = $diasSemana[(int) \Carbon\Carbon::parse($this->fechaInicio)->dayOfWeek];

        $this->horariosDisponibles = Horario::where('centro_id', $this->centroId)
            ->where('especialidad_id', $this->especialidadId)
            ->where('dia_semana', $dia)
            ->where('activo', true)
            ->get()
            ->toArray();

        if (empty($this->horariosDisponibles)) {
            $this->msgHorarios = 'Falta definir "Horarios" para los datos ingresados.';
        }
    }

    public function cargarEmpleados()
    {
        $this->empleadosDisponibles = [];
        $this->empleadoId = '';
        $this->msgEmpleados = '';

        if (!$this->centroId || !$this->especialidadId) {
            return;
        }

        $this->empleadosDisponibles = EmpleadoCentroEspecialidad::where('centro_id', $this->centroId)
            ->where('especialidad_id', $this->especialidadId)
            ->where('activo', true)
            ->with('user')
            ->get()
            ->map(fn($e) => ['id' => $e->user->id, 'name' => $e->user->name])
            ->unique('id')
            ->values()
            ->toArray();

        if (empty($this->empleadosDisponibles)) {
            $this->msgEmpleados = 'Falta definir "Empleados" para los datos ingresados.';
        }
    }

    public function guardar()
    {
        $this->validate();

        $fechaFin = $this->calcularFechaFin();

        $solicitud = SolicitudCobertura::create([
            'centro_id'       => $this->centroId,
            'especialidad_id' => $this->especialidadId,
            'horario_id'      => $this->horarioId,
            'supervisor_id'   => Auth::id(),
            'fecha_inicio'    => $this->fechaInicio,
            'hora_inicio'     => $this->horaInicio,
            'fecha_fin'       => $fechaFin,
            'hora_fin'        => $this->horaFin,
            'modo_envio'      => $this->modoEnvio,
            'estado'          => 'pendiente',
        ]);

        $destinatario = SolicitudDestinatario::create([
            'solicitud_id'  => $solicitud->id,
            'empleado_id'   => $this->empleadoId,
            'orden'         => 1,
            'estado'        => 'no_respondio',
            'notificado_at' => now(),
        ]);

        try {
            app(WhatsAppService::class)->sendToDestinatario($destinatario, $solicitud);
        } catch (\Throwable $e) {
            Log::error('Error al enviar WhatsApp: ' . $e->getMessage());
        }

        $msg = $this->modo === 'crear' ? 'Solicitud creada correctamente.' : 'Solicitud reenviada correctamente.';
        $this->dispatch('notify', tipo: 'success', mensaje: $msg);
        $this->redirect(route('solicitud-coberturas.index'));
    }

    private function calcularFechaFin(): string
    {
        $horaInicio = $this->horaInicio;
        $horaFin = $this->horaFin;

        if ($horaInicio && $horaFin && $horaInicio >= $horaFin) {
            return \Carbon\Carbon::parse($this->fechaInicio)->addDay()->toDateString();
        }

        return $this->fechaInicio;
    }

    public function render()
    {
        $centros = Centro::where('activo', true)->get();
        $especialidades = Especialidad::where('activo', true)->get();

        return view('livewire.solicitud-coberturas.form', compact('centros', 'especialidades'));
    }
}
