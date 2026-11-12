<?php

namespace App\Http\Controllers;

use App\Models\Expediente;
use App\Models\Paciente;
use App\Models\Visita;
use App\Models\DatosAntropometricos;
use App\Models\Habito;
use App\Models\AntecedenteGinecoObstetrico;
use App\Models\Antecedente;
use App\Models\Alergia;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use App\Models\Auditoria;
use App\Http\Requests\ExpedienteRequest;

class ExpedienteController extends Controller
{
    public function index(Request $request)
    {
        $columnas = [
            'idPaciente' => 'expedientes.idPaciente',
            'folio'      => 'expedientes.folioExpediente',
            'nombre'     => 'pacientes.nombres',
            'creado'     => 'expedientes.created_at',
            'modificado' => 'expedientes.updated_at',
            'creador'    => 'users.nombres',
            'estado'     => 'expedientes.estado',
        ];

        $sort = array_key_exists($request->sort, $columnas) ? $request->sort : 'creado';
        $dir  = $request->dir === 'asc' ? 'asc' : 'desc';

        $query = Expediente::query()
            ->join('pacientes', 'pacientes.idPaciente', '=', 'expedientes.idPaciente')
            ->leftJoin('users', 'users.idUsuario', '=', 'expedientes.idUsuarioCreador')
            ->select('expedientes.*')
            ->with(['paciente', 'creador']);

        if ($request->filled('id')) {
            $query->where('expedientes.idPaciente', 'like', '%' . $request->id . '%');
        }

        if ($request->filled('folio')) {
            $query->where('expedientes.folioExpediente', 'like', '%' . $request->folio . '%');
        }

        if ($request->filled('nombre')) {
            foreach (preg_split('/\s+/', trim($request->nombre)) as $palabra) {
                $query->where(function ($q) use ($palabra) {
                    $q->where('pacientes.nombres', 'like', "%{$palabra}%")
                        ->orWhere('pacientes.apellido1', 'like', "%{$palabra}%")
                        ->orWhere('pacientes.apellido2', 'like', "%{$palabra}%");
                });
            }
        }

        $query->orderBy($columnas[$sort], $dir);

        if ($sort === 'nombre') {
            $query->orderBy('pacientes.apellido1', $dir);
        }

        $expedientes = $query->paginate(10)->withQueryString();

        return view('expedientes.index', compact('expedientes', 'sort', 'dir'));
    }

    public function create()
    {
        return view('expedientes.create');
    }

    public function store(ExpedienteRequest $request)
    {
        $data = $request->validated();

        $expediente = DB::transaction(function () use ($data) {
            // 1. Crear paciente y contactos
            $paciente = Paciente::create($this->datosPaciente($data));
            $this->guardarRelaciones($paciente, $data);

            // 2. Crear expediente
            $expediente = Expediente::create([
                'folioExpediente'  => $this->generarFolio(),
                'idPaciente'       => $paciente->idPaciente,
                'idUsuarioCreador' => auth()->id(),
                'estado'           => 'activo',
            ]);

            // 3. Guardar Perfil Clínico
            $this->guardarPerfilClinico($expediente->folioExpediente, $data);

            // 4. Registrar Visita Inicial y Triage (si llenó los campos)
            if (!empty($data['triage'])) {
                $this->guardarVisitaInicial($expediente->folioExpediente, $data['triage']);
            }

            return $expediente;
        });

        return redirect()->route('expedientes.index')
            ->with('success', "Expediente {$expediente->folioExpediente} creado exitosamente.");
    }

    public function edit(Expediente $expediente)
    {
        $expediente->load('paciente.telefonos', 'paciente.correos', 'paciente.contactos.telefonos', 'paciente.contactos.correos');
        return view('expedientes.edit', compact('expediente'));
    }

    public function update(ExpedienteRequest $request, Expediente $expediente)
    {
        $paciente = $expediente->paciente;
        $data = $request->validated();

        // Cargamos las relaciones para capturar el estado original antes de borrar y recrear (para logs)
        if ($paciente) {
            $paciente->loadMissing(['telefonos', 'correos', 'contactos.telefonos', 'contactos.correos']);
        }

        $originalExpediente = $expediente->getAttributes();
        $originalPaciente   = $paciente ? $paciente->getAttributes() : [];

        $origTelefonos = $paciente ? $paciente->telefonos->map(fn ($t) => [
            'numero' => trim((string) $t->numero),
            'tipo'   => trim((string) ($t->tipo ?? '')),
        ])->values()->all() : [];

        $origCorreos = $paciente ? $paciente->correos->pluck('correo')->map(fn ($c) => trim((string) $c))->values()->all() : [];

        $origContactos = $paciente ? $paciente->contactos->map(function ($c) {
            return [
                'nombres'    => trim((string) $c->nombres),
                'apellido1'  => trim((string) $c->apellido1),
                'apellido2'  => trim((string) ($c->apellido2 ?? '')),
                'parentesco' => trim((string) $c->parentesco),
                'telefonos'  => $c->telefonos->map(fn ($t) => ['numero' => trim((string) $t->numero), 'tipo' => trim((string) ($t->tipo ?? ''))])->values()->all(),
                'correos'    => $c->correos->pluck('correo')->map(fn ($e) => trim((string) $e))->values()->all(),
            ];
        })->values()->all() : [];

        DB::transaction(function () use ($paciente, $expediente, $data) {
            $paciente->update($this->datosPaciente($data));

            $paciente->telefonos()->delete();
            $paciente->correos()->delete();
            foreach ($paciente->contactos as $contacto) {
                $contacto->telefonos()->delete();
                $contacto->correos()->delete();
            }
            $paciente->contactos()->delete();

            $this->guardarRelaciones($paciente, $data);

            // TODO: Lógica para actualizar perfil clínico (se hará en otra vista)

            $expediente->touch();
        });

        // Generamos un array de cambios para logs

        $cambios = [];

        // 1. Cambios directos del expediente
        foreach ($expediente->getChanges() as $campo => $valorNuevo) {
            if ($campo === 'updated_at') continue;
            $cambios[$campo] = [
                'anterior' => $originalExpediente[$campo] ?? null,
                'nuevo'    => $valorNuevo
            ];
        }

        // 2. Cambios de datos personales del paciente (nombre, curp, fecha, etc.)
        if ($paciente) {
            $datosNuevosPaciente = $this->datosPaciente($data);
            foreach ($datosNuevosPaciente as $campo => $valorNuevo) {
                if (array_key_exists($campo, $originalPaciente)) {
                    $valorAnterior = $originalPaciente[$campo];

                    $valAntCmp = is_string($valorAnterior) ? trim(substr($valorAnterior, 0, 10)) : $valorAnterior;
                    $valNueCmp = is_string($valorNuevo) ? trim(substr($valorNuevo, 0, 10)) : $valorNuevo;

                    if ($valAntCmp != $valNueCmp) {
                        $cambios[$campo] = [
                            'anterior' => $valorAnterior,
                            'nuevo'    => $valorNuevo
                        ];
                    }
                }
            }

            // 3. Cambios en teléfonos
            $nuevosTelefonos = collect($data['telefonos'] ?? [])->map(fn ($t) => [
                'numero' => trim((string) ($t['numero'] ?? '')),
                'tipo'   => trim((string) ($t['tipo'] ?? '')),
            ])->values()->all();

            if ($origTelefonos != $nuevosTelefonos) {
                $cambios['telefonos'] = [
                    'anterior' => $origTelefonos,
                    'nuevo'    => $nuevosTelefonos
                ];
            }

            // 4. Cambios en correos
            $nuevosCorreos = collect($data['correos'] ?? [])->map(fn ($c) => trim((string) $c))->values()->all();

            if ($origCorreos != $nuevosCorreos) {
                $cambios['correos'] = [
                    'anterior' => $origCorreos,
                    'nuevo'    => $nuevosCorreos
                ];
            }

            // 5. Cambios en contactos de emergencia
            $nuevosContactos = collect($data['contactos'] ?? [])->map(function ($c) {
                return [
                    'nombres'    => trim((string) ($c['nombres'] ?? '')),
                    'apellido1'  => trim((string) ($c['apellido1'] ?? '')),
                    'apellido2'  => trim((string) ($c['apellido2'] ?? '')),
                    'parentesco' => trim((string) ($c['parentesco'] ?? '')),
                    'telefonos'  => collect($c['telefonos'] ?? [])->map(fn ($t) => ['numero' => trim((string) ($t['numero'] ?? '')), 'tipo' => trim((string) ($t['tipo'] ?? ''))])->values()->all(),
                    'correos'    => collect($c['correos'] ?? [])->map(fn ($e) => trim((string) $e))->values()->all(),
                ];
            })->values()->all();

            if ($origContactos != $nuevosContactos) {
                $cambios['contactos'] = [
                    'anterior' => $origContactos,
                    'nuevo'    => $nuevosContactos
                ];
            }
        }

        if (!empty($cambios)) {
            $accionTexto = 'Actualización de expediente';
            if (isset($cambios['estado'])) {
                $estadoNuevo = strtolower($cambios['estado']['nuevo']);
                if (in_array($estadoNuevo, ['inactivo', 'baja', 'suspendido'])) {
                    $accionTexto = 'Desactivación de expediente';
                } elseif ($estadoNuevo === 'activo') {
                    $accionTexto = 'Activación de expediente';
                }
            }

            Auditoria::create([
                'idUsuario'     => Auth::id() ?? 1,
                'accion'        => $accionTexto,
                'tablaAfectada' => 'expedientes',
                'detalles'      => [
                    'tipo'      => 'modificacion',
                    'paciente'  => $paciente ? $paciente->nombres . ' ' . $paciente->apellido1 . ' ' . ($paciente->apellido2 ?? '') : 'No asignado',
                    'cambios'   => $cambios
                ],
                'fechaAccion'   => now(),
            ]);
        }

        return redirect()->route('expedientes.index')
            ->with('success', "Expediente {$expediente->folioExpediente} actualizado.");
    }

    // ---------- Auxiliares ----------




    private function datosPaciente(array $data): array
    {
        $paciente = Arr::only($data, ['nombres', 'apellido1', 'apellido2', 'curp', 'sexo',
            'ocupacion', 'escolaridad', 'servicioMedico', 'lugarProcedencia', 'lugarResidencia']);
        $paciente['fechaNac'] = Carbon::createFromFormat('d/m/Y', $data['fechaNac'])->format('Y-m-d');
        return $paciente;
    }

    private function guardarRelaciones(Paciente $paciente, array $data): void
    {
        foreach ($data['telefonos'] ?? [] as $t) {
            $paciente->telefonos()->create(['numero' => $t['numero'], 'tipo' => $t['tipo']]);
        }
        foreach ($data['correos'] ?? [] as $correo) {
            $paciente->correos()->create(['correo' => $correo]);
        }
        foreach ($data['contactos'] ?? [] as $c) {
            $contacto = $paciente->contactos()->create(Arr::only($c, ['nombres', 'apellido1', 'apellido2', 'parentesco']));
            foreach ($c['telefonos'] ?? [] as $t) {
                $contacto->telefonos()->create(['numero' => $t['numero'], 'tipo' => $t['tipo']]);
            }
            foreach ($c['correos'] ?? [] as $correo) {
                $contacto->correos()->create(['correo' => $correo]);
            }
        }
    }

    private function guardarPerfilClinico(string $folio, array $data): void
    {
        Habito::create(array_merge(['folioExpediente' => $folio], $data['habitos']));

        if (! empty($data['gineco'])) {
            AntecedenteGinecoObstetrico::create([
                'folioExpediente'  => $folio,
                'embarazoActual'   => (bool) ($data['gineco']['embarazoActual'] ?? false),
                'semanasGestacion' => $data['gineco']['semanasGestacion'] ?? null,
                'menstruacion'     => (bool) ($data['gineco']['menstruacion'] ?? false),
                'lactancia'        => (bool) ($data['gineco']['lactancia'] ?? false),
                'observaciones'    => $data['gineco']['observaciones'] ?? null,
            ]);
        }

        foreach ($data['antecedentes'] ?? [] as $a) {
            Antecedente::create([
                'folioExpediente' => $folio,
                'tipo'            => $a['tipo'],
                'nombre'          => $a['nombre'],
                'descripcion'     => $a['descripcion'] ?? null,
                'esFamiliar'      => (bool) ($a['esFamiliar'] ?? false),
            ]);
        }

        foreach ($data['alergias'] ?? [] as $al) {
            Alergia::create([
                'folioExpediente' => $folio,
                'sustancia'       => $al['sustancia'],
                'reaccion'        => $al['reaccion'] ?? null,
                'severidad'       => $al['severidad'],
                'fechaAproximada' => \App\Http\Requests\ExpedienteRequest::aISO($al['fechaAproximada'] ?? null),
            ]);
        }

        foreach ($data['noFarmacologicos'] ?? [] as $t) {
            \App\Models\TratamientoNoFarmacologico::create([
                'folioExpediente' => $folio,
                'tipo'            => $t['tipo'],
                'titulo'          => $t['titulo'],
                'descripcion'     => $t['descripcion'] ?? null,
            ]);
        }
    }

    private function guardarVisitaInicial(string $folio, array $triage): void
    {
        $visita = Visita::create([
            'folioExpediente' => $folio,
            'idUsuario'       => auth()->id(),
            'motivo'          => 'Apertura de expediente y Triage inicial',
            'estado'          => 'completada'
        ]);

        // Cálculo seguro de IMC en backend
        $peso = $triage['peso'] ?? null;
        $talla = $triage['talla'] ?? null;
        $imc = null;

        if ($peso && $talla && $talla > 0) {
            $imc = round($peso / ($talla * $talla), 2);
        }

        DatosAntropometricos::create([
            'idVisita'               => $visita->idVisita,
            'peso'                   => $peso,
            'talla'                  => $talla,
            'imc'                    => $imc,
            'temperatura'            => $triage['temperatura'] ?? null,
            'presionSistolica'       => $triage['presionSistolica'] ?? null,
            'presionDiastolica'      => $triage['presionDiastolica'] ?? null,
            'pulsoPpm'               => $triage['pulsoPpm'] ?? null,
            'frecuenciaRespiratoria' => $triage['frecuenciaRespiratoria'] ?? null,
            'saturacionOxigeno'      => $triage['saturacionOxigeno'] ?? null,
        ]);
    }

    private function generarFolio(): string
    {
        $anio = now()->year;
        $ultimo = Expediente::where('folioExpediente', 'like', "EXP-{$anio}-%")->orderByDesc('folioExpediente')->value('folioExpediente');
        $siguiente = $ultimo ? ((int) substr($ultimo, -4)) + 1 : 1;
        return sprintf('EXP-%d-%04d', $anio, $siguiente);
    }

    public function destroy(Expediente $expediente) { $expediente->update(['estado' => 'inactivo']); return back()->with('success', "Expediente {$expediente->folioExpediente} desactivado."); }
    public function restore(Expediente $expediente) { $expediente->update(['estado' => 'activo']); return back()->with('success', "Expediente {$expediente->folioExpediente} reactivado."); }

    public function show(Expediente $expediente)
    {
        // Cargamos todas las relaciones necesarias para pintar el perfil clínico completo
        $expediente->load([
            'paciente.telefonos',
            'paciente.correos',
            'paciente.contactos.telefonos',
            'paciente.contactos.correos',
            'habitos',
            'antecedentesGinecoObstetricos', // Relación con el modelo gineco
            'antecedentes',
            'alergias',
            'visitas.datosAntropometricos', // Para ver el historial de triage/signos vitales
            'creador'
        ]);

        $paciente = $expediente->paciente;

        Auditoria::create([
            'idUsuario'     => auth()->id() ?? 1,
            'accion'        => 'Consulta de expediente',
            'tablaAfectada' => 'expedientes',
            'detalles'      => [
                'tipo'     => 'consulta',
                'folio'    => $expediente->folioExpediente,
                'paciente' => $paciente ? trim("{$paciente->nombres} {$paciente->apellido1} " . ($paciente->apellido2 ?? '')) : 'No asignado',
            ],
            'fechaAccion'   => now(),
        ]);

        return view('expedientes.show', compact('expediente'));
    }

    public function registrarMedicion(Request $request, Expediente $expediente)
    {
        if ($expediente->estado !== 'activo') {
            return back()->with('error', 'El expediente está inactivo; reactívalo para registrar mediciones.');
        }

        $validated = $request->validate([
            'triage.peso'                   => ['nullable', 'numeric', 'min:0.5', 'max:400'],
            'triage.talla'                  => ['nullable', 'numeric', 'min:0.3', 'max:2.5'],
            'triage.temperatura'            => ['nullable', 'numeric', 'min:30', 'max:45'],
            'triage.saturacionOxigeno'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'triage.presionSistolica'       => ['nullable', 'integer', 'min:0', 'max:300'],
            'triage.presionDiastolica'      => ['nullable', 'integer', 'min:0', 'max:200'],
            'triage.pulsoPpm'               => ['nullable', 'integer', 'min:0', 'max:300'],
            'triage.frecuenciaRespiratoria' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $triage = array_filter($validated['triage'] ?? [], fn ($v) => $v !== null && $v !== '');
        if (empty($triage)) {
            return back()->with('error', 'Captura al menos un signo vital para registrar la medición.');
        }

        DB::transaction(function () use ($expediente, $triage) {
            // Cada medición posterior a la inicial es una visita de seguimiento con fecha del sistema
            $visita = Visita::create([
                'folioExpediente' => $expediente->folioExpediente,
                'idUsuario'       => auth()->id(),
                'fecha'           => now(),
                'tipo'            => 'seguimiento',
                'motivo'          => 'Registro de signos vitales',
                'estado'          => 'completada',
            ]);

            $imc = null;
            if (!empty($triage['peso']) && !empty($triage['talla'])) {
                $imc = round($triage['peso'] / ($triage['talla'] ** 2), 2);
            }

            DatosAntropometricos::create([
                'idVisita'               => $visita->idVisita,
                'peso'                   => $triage['peso'] ?? null,
                'talla'                  => $triage['talla'] ?? null,
                'imc'                    => $imc,
                'temperatura'            => $triage['temperatura'] ?? null,
                'presionSistolica'       => $triage['presionSistolica'] ?? null,
                'presionDiastolica'      => $triage['presionDiastolica'] ?? null,
                'pulsoPpm'               => $triage['pulsoPpm'] ?? null,
                'frecuenciaRespiratoria' => $triage['frecuenciaRespiratoria'] ?? null,
                'saturacionOxigeno'      => $triage['saturacionOxigeno'] ?? null,
            ]);
        });

        return back()->with('success', 'Medición de signos vitales registrada.');
    }
}
