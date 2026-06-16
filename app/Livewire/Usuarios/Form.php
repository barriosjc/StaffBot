<?php

namespace App\Livewire\Usuarios;

use App\Models\Centro;
use App\Models\EmpleadoCentroEspecialidad;
use App\Models\Especialidad;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class Form extends Component
{
    // ──────────────────────────────────────────
    //  Estado general
    // ──────────────────────────────────────────
    public ?int  $usuarioId  = null;
    public bool  $modoEdicion = false;

    // ──────────────────────────────────────────
    //  Campos del usuario
    // ──────────────────────────────────────────
    public string  $name  = '';
    public string  $email     = '';
    public string  $telefono  = '';
    public ?string $password  = null;
    public ?string $password_confirmation = null;
    public bool    $activo    = true;

    // ──────────────────────────────────────────
    //  Rol seleccionado en el form
    // ──────────────────────────────────────────
    public string $tipoRol = 'emp'; // 'sup' | 'emp'

    // ──────────────────────────────────────────
    //  Supervisor: centros seleccionados (array de IDs como string)
    // ──────────────────────────────────────────
    public array $centrosSup = [];

    // ──────────────────────────────────────────
    //  Empleado: combinaciones centro+especialidad
    //  Cada elemento: ['centro_id' => '', 'especialidad_id' => '', 'activo' => true]
    // ──────────────────────────────────────────
    public array $combinaciones = [];

    // ──────────────────────────────────────────
    //  Catálogos
    // ──────────────────────────────────────────
    public $centros       = [];
    public $especialidades = [];

    // ──────────────────────────────────────────
    //  Lifecycle
    // ──────────────────────────────────────────

    public function mount($usuario = null): void
    {
        $this->centros        = Centro::activos()->orderBy('nombre')->get();
        $this->especialidades = Especialidad::activas()->orderBy('nombre')->get();

        if ($usuario) {
            $this->modoEdicion = true;
            $this->usuarioId   = (int) $usuario;
            $this->cargarUsuario((int) $usuario);
        } else {
            $this->agregarCombinacion(); // fila vacía por defecto para empleado
        }
    }

    private function cargarUsuario(int $id): void
    {
        $u = User::with(['centrosSupervisor', 'empleadoCentroEspecialidades'])->findOrFail($id);

        $this->name     = $u->name;
        $this->email    = $u->email;
        $this->telefono = $u->telefono;
        $this->activo   = $u->activo;

        $esSup = $u->centrosSupervisor->isNotEmpty();
        $esEmp = $u->empleadoCentroEspecialidades->isNotEmpty();

        // Si tiene ambos roles, priorizamos supervisor en el form
        $this->tipoRol = $esSup ? 'sup' : 'emp';

        // Cargar centros del supervisor
        $this->centrosSup = $u->centrosSupervisor->pluck('id')->map(fn ($v) => (string) $v)->toArray();

        // Cargar combinaciones del empleado
        $this->combinaciones = $u->empleadoCentroEspecialidades
            ->map(fn ($r) => [
                'centro_id'       => (string) $r->centro_id,
                'especialidad_id' => (string) $r->especialidad_id,
                'activo'          => (bool)   $r->activo,
            ])->toArray();

        if (empty($this->combinaciones)) {
            $this->agregarCombinacion();
        }
    }

    // ──────────────────────────────────────────
    //  Manejo de combinaciones empleado
    // ──────────────────────────────────────────

    public function agregarCombinacion(): void
    {
        $this->combinaciones[] = [
            'centro_id'       => '',
            'especialidad_id' => '',
            'activo'          => true,
        ];
    }

    public function quitarCombinacion(int $index): void
    {
        array_splice($this->combinaciones, $index, 1);
    }

    // ──────────────────────────────────────────
    //  Validación dinámica
    // ──────────────────────────────────────────

    protected function reglasValidacion(): array
    {
        $rules = [
            'name' => 'required|string|min:2|max:100',
            'telefono' => 'required|string|max:30' . ($this->modoEdicion ? "|unique:users,telefono,{$this->usuarioId}" : '|unique:users,telefono'),
            'activo'   => 'boolean',
            'tipoRol'  => 'required|in:sup,emp',
        ];

        if ($this->modoEdicion) {
            $rules['email']    = "required|email|max:150|unique:users,email,{$this->usuarioId}";
            $rules['password'] = 'nullable|string|min:8|confirmed';
        } else {
            $rules['email']    = 'required|email|max:150|unique:users,email';
            $rules['password'] = 'required|string|min:8|confirmed';
        }

        if ($this->tipoRol === 'sup') {
            $rules['centrosSup']   = 'required|array|min:1';
            $rules['centrosSup.*'] = 'exists:centros,id';
        } else {
            $rules['combinaciones']                      = 'required|array|min:1';
            $rules['combinaciones.*.centro_id']          = 'required|exists:centros,id';
            $rules['combinaciones.*.especialidad_id']    = 'required|exists:especialidades,id';
        }

        return $rules;
    }

    protected function mensajesValidacion(): array
    {
        return [
            'name.required'                          => 'El name es obligatorio.',
            'email.required'                         => 'El email es obligatorio.',
            'email.unique'                           => 'Este email ya está registrado.',
            'telefono.required'                      => 'El teléfono es obligatorio.',
            'telefono.unique'                        => 'Este teléfono ya está registrado.',
            'password.required'                      => 'La contraseña es obligatoria.',
            'password.min'                           => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed'                     => 'Las contraseñas no coinciden.',
            'centrosSup.required'                    => 'Seleccioná al menos un centro.',
            'centrosSup.min'                         => 'Seleccioná al menos un centro.',
            'combinaciones.required'                 => 'Agregá al menos una combinación de centro y especialidad.',
            'combinaciones.min'                      => 'Agregá al menos una combinación de centro y especialidad.',
            'combinaciones.*.centro_id.required'     => 'Seleccioná un centro en cada fila.',
            'combinaciones.*.especialidad_id.required' => 'Seleccioná una especialidad en cada fila.',
        ];
    }

    // ──────────────────────────────────────────
    //  Guardar
    // ──────────────────────────────────────────

    public function guardar(): void
    {
        $this->validate($this->reglasValidacion(), $this->mensajesValidacion());

        $data = [
            'name' => $this->name,
            'email'    => $this->email,
            'telefono' => $this->telefono,
            'activo'   => $this->activo,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->modoEdicion) {
            $usuario = User::findOrFail($this->usuarioId);
            $usuario->update($data);
        } else {
            $usuario = User::create($data);
        }

        // ── Sincronizar relaciones según rol ──
        if ($this->tipoRol === 'sup') {
            // Asignar centros como supervisor
            $usuario->centrosSupervisor()->sync($this->centrosSup);

            // Si en edición tenía registros de empleado, los limpiamos
            if ($this->modoEdicion) {
                $usuario->empleadoCentroEspecialidades()->delete();
            }
        } else {
            // Empleado: recrear combinaciones
            $usuario->empleadoCentroEspecialidades()->delete();

            foreach ($this->combinaciones as $combo) {
                EmpleadoCentroEspecialidad::create([
                    'user_id'       => $usuario->id,
                    'centro_id'        => $combo['centro_id'],
                    'especialidad_id'  => $combo['especialidad_id'],
                    'activo'           => $combo['activo'] ?? true,
                ]);
            }

            // Si en edición tenía centros de supervisor, los limpiamos
            if ($this->modoEdicion) {
                $usuario->centrosSupervisor()->detach();
            }
        }

        $mensaje = $this->modoEdicion ? 'Usuario actualizado correctamente.' : 'Usuario creado correctamente.';

        session()->flash('notify_tipo', 'success');
        session()->flash('notify_mensaje', $mensaje);

        $this->redirect(route('usuarios.index'));
    }

    // ──────────────────────────────────────────
    //  Render
    // ──────────────────────────────────────────

    public function render()
    {
        return view('livewire.usuarios.form')
            ->layout('layouts.app');
    }
}
